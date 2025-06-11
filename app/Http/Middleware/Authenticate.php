<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they ar
     * e not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // ▼▼▼ TAMBAHKAN BARIS INI ▼▼▼
            $request->session()->flash('session_expired', 'Sesi Anda telah berakhir. Silakan login kembali.');
            
            return route('login');
        }

        return null;
    }
   
}