<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->except('_token');
        $loginType = filter_var($input['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => $input['email'],
            'password' => $input['password']
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // <<< AWAL PERUBAHAN LOGIKA REDIRECT MENGGUNAKAN SPATIE >>>
            if ($user->hasRole('superadmin')) {
                // Arahkan superadmin ke dashboardnya
                return redirect()->intended(route('superadmin.dashboard'));
            } elseif ($user->hasRole('admin')) {
                // Arahkan admin ke dashboardnya
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // User biasa ke dashboard mereka
                return redirect()->intended(route('dashboard'));
            }
            // <<< AKHIR PERUBAHAN >>>
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