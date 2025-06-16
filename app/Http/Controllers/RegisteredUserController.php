<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bidang; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth as IlluminateAuth;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        // ▼▼▼ TAMBAHKAN LOGIKA INI ▼▼▼
        // Ambil semua data bidang untuk ditampilkan di dropdown
        $bidangs = Bidang::orderBy('name')->get();
        // Kirim data bidang ke view
        return view('auth.register', compact('bidangs'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // ▼▼▼ TAMBAHKAN VALIDASI UNTUK bidang_id ▼▼▼
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:mahasiswa,siswa', // Hanya role ini yang bisa mendaftar
            'asal_kampus' => 'required|string|max:255',
            'bidang_id' => 'required|exists:bidangs,id', // <-- VALIDASI BARU
            'nim' => 'nullable|string|max:255|unique:users,nim',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ▼▼▼ TAMBAHKAN bidang_id SAAT MEMBUAT USER BARU ▼▼▼
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'asal_kampus' => $request->asal_kampus,
            'nim' => $request->nim,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'bidang_id' => $request->bidang_id, // <-- DATA BARU DISIMPAN
        ]);

        // Berikan Spatie role 'user' untuk mahasiswa/siswa
        $user->assignRole('user');

        event(new Registered($user));

        IlluminateAuth::login($user);

        // Arahkan ke dashboard user biasa
        return redirect()->route('dashboard');
    }
}