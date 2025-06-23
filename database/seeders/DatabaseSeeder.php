<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder yang mengatur Role dan Permission
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Anda bisa menambahkan seeder lain di sini di masa depan
        ]);
    }
}