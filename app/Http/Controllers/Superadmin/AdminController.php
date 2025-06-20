<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;


class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        $user = Auth::user();
        $query = User::role('admin');

        // Jika pengguna adalah 'admin' (bukan 'superadmin'), filter berdasarkan bidangnya
        if ($user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $query->where('bidang_id', $user->bidang_id);
        }

        $admins = $query->with('bidang')->latest()->paginate(10);
        return view('superadmin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $user = Auth::user();
        $bidangs = Bidang::orderBy('name')->get();
        
        // Admin tidak bisa membuat Superadmin
        $roles = Role::where('name', '!=', 'superadmin')->pluck('name', 'name');

        // Jika user adalah admin, batasi pilihan bidang hanya ke bidangnya sendiri
        if ($user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $bidangs = Bidang::where('id', $user->bidang_id)->get();
        }

        return view('superadmin.admins.form', [
            'admin' => new User(),
            'bidangs' => $bidangs,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bidang_id' => ['required', 'exists:bidangs,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = Auth::user();
        $bidangId = $request->input('bidang_id');

        // Paksa bidang_id jika yang membuat adalah admin
        if ($user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $bidangId = $user->bidang_id;
        }

        // Buat user baru dengan menyertakan 'role'
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bidang_id' => $bidangId,
            'role' => $request->role, // <-- INI PERBAIKANNYA
            'asal_kampus' => 'Kantor Pusat', // Nilai default untuk admin
        ]);

        // Berikan role 'admin' menggunakan Spatie
        $admin->assignRole($request->role);

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Admin baru berhasil dibuat dan ditugaskan.');
    }

    /**
     * Show the form for editing an admin.
     */
    public function edit(User $admin)
    {
        $user = Auth::user();

        // Cek hak akses: admin hanya boleh edit user di bidangnya
        if ($user->hasRole('admin') && !$user->hasRole('superadmin') && $user->bidang_id !== $admin->bidang_id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MENGEDIT ADMIN INI.');
        }
        
        $bidangs = Bidang::orderBy('name')->get();
        $roles = Role::where('name', '!=', 'superadmin')->pluck('name', 'name');

        if ($user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $bidangs = Bidang::where('id', $user->bidang_id)->get();
        }

        return view('superadmin.admins.form', [
            'admin' => $admin,
            'bidangs' => $bidangs,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $admin)
    {
        $user = Auth::user();

        // Cek hak akses: admin hanya boleh update user di bidangnya
        if ($user->hasRole('admin') && !$user->hasRole('superadmin') && $user->bidang_id !== $admin->bidang_id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MEMPERBARUI ADMIN INI.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'bidang_id' => ['required', 'exists:bidangs,id'],
            'role' => 'required|exists:roles,name',
        ]);

        $bidangId = $request->input('bidang_id');
        if ($user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $bidangId = $user->bidang_id;
        }

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'bidang_id' => $bidangId,
            'role' => $request->role, // <-- PASTIKAN ROLE JUGA DIUPDATE
            'asal_kampus' => $admin->asal_kampus ?? 'Kantor Pusat', 
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }
        
        $admin->update($dataToUpdate);

        // Sinkronkan role Spatie
        $admin->syncRoles($request->role);

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin)
    {
        $user = Auth::user();

        // Mencegah menghapus diri sendiri
        if ($admin->id === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // Cek hak akses: admin hanya boleh hapus user di bidangnya
        if ($user->hasRole('admin') && !$user->hasRole('superadmin') && $user->bidang_id !== $admin->bidang_id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MENGHAPUS ADMIN INI.');
        }

        $admin->delete();
        
        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Akun admin berhasil dihapus.');
    }
}