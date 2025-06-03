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
        Schema::create('correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date'); // Tanggal absensi yang ingin dikoreksi
            $table->dateTime('old_check_in')->nullable(); // Waktu check-in lama
            $table->dateTime('old_check_out')->nullable(); // Waktu check-out lama
            $table->dateTime('new_check_in')->nullable(); // Waktu check-in baru yang diajukan
            $table->dateTime('new_check_out')->nullable(); // Waktu check-out baru yang diajukan
            $table->string('old_activity_title')->nullable(); // Judul aktivitas lama
            $table->text('old_activity_description')->nullable(); // Deskripsi aktivitas lama
            $table->string('new_activity_title')->nullable(); // Judul aktivitas baru
            $table->text('new_activity_description')->nullable(); // Deskripsi aktivitas baru
            $table->text('reason'); // Alasan koreksi dari user
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status permintaan
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang menyetujui
            $table->timestamp('approved_at')->nullable(); // Waktu persetujuan
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->timestamps();

            // Menambahkan index untuk pencarian lebih cepat
            $table->index(['user_id', 'attendance_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correction_requests');
    }
};