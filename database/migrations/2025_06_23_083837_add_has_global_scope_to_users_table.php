<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom boolean baru setelah 'bidang_id'
            // Kolom ini akan menandai apakah seorang admin memiliki hak akses global
            // Defaultnya adalah false (tidak punya hak akses global)
            $table->boolean('has_global_scope')->default(false)->after('bidang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('has_global_scope');
        });
    }
};