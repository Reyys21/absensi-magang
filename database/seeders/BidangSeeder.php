<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bidang;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel bidang sebelum mengisi
        DB::table('bidangs')->truncate();

        // Tambahkan data bidang
        Bidang::create(['name' => 'Divisi KKU']);
        Bidang::create(['name' => 'Divisi Sistem Teknologi Informasi']);
        Bidang::create(['name' => 'Divisi Keuangan']);
        Bidang::create(['name' => 'Divisi Niaga']);
    }
}