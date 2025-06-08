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
        // <<< VALIDASI DISESUAIKAN, 'role' TIDAK LAGI DIPERLUKAN DARI FORM >>>
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'asal_kampus' => 'required|string|max:255',
            'nim' => 'nullable|string|max:255|unique:users,nim', // Disederhanakan
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'asal_kampus' => $request->asal_kampus,
            'nim' => $request->nim,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user', // <<< KITA BISA ISI KOLOM LAMA DENGAN 'user' UNTUK KONSISTENSI SEMENTARA >>>
        ]);

        // <<< TETAPKAN ROLE MENGGUNAKAN SPATIE >>>
        $user->assignRole('user');

        event(new Registered($user));

        IlluminateAuth::login($user);

        // <<< LANGSUNG ARAHKAN KE DASHBOARD USER BIASA >>>
        return redirect()->route('dashboard');
    }
}