<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User; // Pastikan model User di-import

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // Gate untuk halaman yang hanya bisa diakses oleh user biasa (bukan admin)
        Gate::define('access-user-pages', function (User $user) {
            // Asumsi: Anda memiliki kolom 'role' di tabel users
            // Izinkan jika rolenya BUKAN admin atau superadmin
            return !$user->hasAnyRole(['admin', 'superadmin']);
        });

        // Gate untuk halaman yang bisa diakses oleh admin dan superadmin
        Gate::define('access-admin-pages', function (User $user) {
            // Izinkan jika user memiliki role 'admin' ATAU 'superadmin'
            return $user->hasAnyRole(['admin', 'superadmin']);
        });

        // Gate untuk halaman yang hanya bisa diakses oleh superadmin
        Gate::define('access-superadmin-pages', function (User $user) {
            // Izinkan HANYA jika user memiliki role 'superadmin'
            return $user->hasRole('superadmin');
        });
    }
}