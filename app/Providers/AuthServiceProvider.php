<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Definisikan Gate untuk mengakses halaman Admin
        // Hanya user dengan role 'admin' ATAU 'superadmin' yang boleh lewat
        Gate::define('access-admin-pages', function (User $user) {
            return $user->hasAnyRole(['admin', 'superadmin']);
        });

        // Definisikan Gate untuk mengakses halaman Superadmin
        // HANYA user dengan role 'superadmin' yang boleh lewat
        Gate::define('access-superadmin-pages', function (User $user) {
            return $user->hasRole('superadmin');
        });

        // <<< TAMBAHKAN GATE UNTUK USER BIASA DI SINI >>>
        // Definisikan Gate untuk mengakses halaman khusus mahasiswa/siswa
        // Hanya user dengan role 'user' yang boleh lewat
        Gate::define('access-user-pages', function (User $user) {
            return $user->hasRole('user');
        });
    }
}