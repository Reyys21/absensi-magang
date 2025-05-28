<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class CheckIncompleteAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-incomplete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for incomplete or missing attendance records for each user from their creation date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for incomplete attendance records from user creation date...');

        $users = User::all(); // Ambil semua user

        foreach ($users as $user) {
            $this->info("Processing attendance for user: {$user->name} (ID: {$user->id})");

            // Tentukan tanggal mulai pemeriksaan: created_at user
            $startDate = Carbon::parse($user->created_at)->startOfDay();

            // Tentukan tanggal akhir pemeriksaan: kemarin (karena kita biasanya memeriksa hari yang sudah selesai)
            $endDate = Carbon::yesterday()->startOfDay();

            // Pastikan tanggal mulai tidak lebih besar dari tanggal akhir
            if ($startDate->gt($endDate)) {
                $this->info("  User created today or after the check period. Skipping for now.");
                continue; // Lanjut ke user berikutnya jika akun baru dibuat hari ini atau setelahnya
            }

            // Loop dari tanggal pembuatan akun user hingga kemarin
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Hanya proses jika hari yang sedang dicek adalah hari kerja (Senin-Jumat)
                if ($date->isWeekday()) {
                    $attendance = Attendance::where('user_id', $user->id)
                                            ->whereDate('date', $date)
                                            ->first();

                    // Jika tidak ada catatan absensi sama sekali untuk hari kerja ini
                    if (!$attendance) {
                        // Buat catatan baru dengan check_in dan check_out kosong.
                        // Model Attendance akan menginterpretasikannya sebagai 'Absent'.
                        Attendance::create([
                            'user_id' => $user->id,
                            'date' => $date->toDateString(),
                            'check_in' => null,
                            'check_out' => null,
                            'activity_title' => null,
                            'activity_description' => null,
                        ]);
                        $this->info("  Marked user {$user->name} as ABSENT for {$date->toDateString()}.");
                    }
                    // Catatan: Jika ada catatan tapi tidak lengkap (hanya check_in atau check_out),
                    // model Attendance Anda sudah akan menginterpretasikannya sebagai 'Absent (Belum Lengkap)'.
                    // Jadi, kita tidak perlu update di sini, hanya perlu memastikan record-nya ada.
                }
            }
            $this->info("  Finished checking attendance for {$user->name}.");
        }

        $this->info('All incomplete attendance checks completed.');
    }
}