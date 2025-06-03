<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register'); // arahkan ke view register
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:mahasiswa,siswa,admin',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'asal_kampus' => 'required|string|max:255',
            'nim' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'asal_kampus' => $request->asal_kampus,
            'nim' => $request->nim,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        auth()->login($user);

        return redirect()->route('login');
    }
}