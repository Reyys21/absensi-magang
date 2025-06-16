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
            $table->foreignId('bidang_id')
                  ->nullable()
                  ->after('id') // Posisikan setelah kolom id agar rapi
                  ->constrained('bidangs')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint sebelum drop kolom
            $table->dropForeign(['bidang_id']);
            $table->dropColumn('bidang_id');
        });
    }
};