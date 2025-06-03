<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan ini diimpor
use Illuminate\Support\Facades\Hash; // Pastikan ini diimpor

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->except('_token');
        // Deteksi apakah input adalah format email yang valid
        $loginType = filter_var($input['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => $input['email'], // Gunakan 'email' atau 'name' sebagai kunci identifikasi
            'password' => $input['password']
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email/Nama Pengguna atau Kata Sandi Salah.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}