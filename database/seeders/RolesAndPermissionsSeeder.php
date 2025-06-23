<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache role dan permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Izin (Permissions) yang kita butuhkan
        $permissions = [
            'view global dashboard',
            'view all users',
            'approve all requests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Buat Role dasar jika belum ada
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // 3. Berikan semua izin yang ada di sistem ke role 'superadmin'
        // Ini memastikan superadmin selalu memiliki semua hak akses
        $allPermissions = Permission::all();
        $superadminRole->syncPermissions($allPermissions);
    }
}