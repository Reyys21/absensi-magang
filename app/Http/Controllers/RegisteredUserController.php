<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth as IlluminateAuth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // REVISI 1: Menambahkan 'role' ke dalam validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:mahasiswa,siswa,admin', // <-- Memvalidasi role dari form
            'asal_kampus' => 'required|string|max:255',
            'nim' => 'nullable|string|max:255|unique:users,nim',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // REVISI 2: Menggunakan input 'role' dari form saat membuat user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'asal_kampus' => $request->asal_kampus,
            'nim' => $request->nim,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role, // <-- Menggunakan role dari form, bukan 'user'
        ]);

        // REVISI 3: Memberikan Spatie role yang sesuai berdasarkan pilihan
        if ($request->role === 'admin') {
            $user->assignRole('admin');
        } else {
            // Untuk 'mahasiswa' dan 'siswa', Spatie role-nya adalah 'user'
            $user->assignRole('user');
        }

        event(new Registered($user));

        IlluminateAuth::login($user);

        // Arahkan ke dashboard yang sesuai setelah login
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
}