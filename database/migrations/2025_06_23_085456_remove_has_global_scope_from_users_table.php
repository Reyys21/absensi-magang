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
            // Hapus kolom has_global_scope jika ada
            if (Schema::hasColumn('users', 'has_global_scope')) {
                $table->dropColumn('has_global_scope');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika di-rollback, buat kembali kolomnya (opsional, tapi praktik yang baik)
            $table->boolean('has_global_scope')->default(false)->after('bidang_id');
        });
    }
};