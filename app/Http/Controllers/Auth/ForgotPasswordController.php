<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman form untuk meminta link reset password.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        // Kita akan membuat view ini di langkah berikutnya
        return view('auth.forgot-password');
    }

    /**
     * Memvalidasi email dan mengirim link reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Menggunakan Password broker bawaan Laravel untuk mengirim link
        $status = Password::sendResetLink($request->only('email'));

        // Memberikan feedback ke user berdasarkan hasil pengiriman email
        return $status == Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
}