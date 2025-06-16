<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Http\Request;
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
        // Ambil semua user yang memiliki role 'admin'
        $admins = User::role('admin')->with('bidang')->latest()->paginate(10);
        return view('superadmin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        // Ambil semua bidang untuk ditampilkan di dropdown
        $bidangs = Bidang::orderBy('name')->get();
        return view('superadmin.admins.form', compact('bidangs'));
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
        ]);

        // Buat user baru
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bidang_id' => $request->bidang_id,
            // Anda bisa mengisi kolom lain jika ada defaultnya
            'role' => 'admin', // Kolom role lama jika masih digunakan
            'asal_kampus' => 'Kantor Pusat',
        ]);

        // Berikan role 'admin' menggunakan Spatie
        $admin->assignRole('admin');

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Admin baru berhasil dibuat dan ditugaskan.');
    }

    /**
     * Show the form for editing an admin.
     * (Fungsi Edit dan Update opsional, tapi bagus untuk ada)
     */
    public function edit(User $admin)
    {
        $bidangs = Bidang::orderBy('name')->get();
        return view('superadmin.admins.form', [
            'admin' => $admin,
            'bidangs' => $bidangs
        ]);
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'bidang_id' => ['required', 'exists:bidangs,id'],
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'bidang_id' => $request->bidang_id,
            'asal_kampus' => $admin->asal_kampus ?? 'Kantor Pusat', 
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }
        
        $admin->update($dataToUpdate);

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin)
    {
        // Pastikan tidak menghapus diri sendiri atau superadmin lain jika ada
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $admin->delete();
        
        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'Akun admin berhasil dihapus.');
    }
}