<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Pastikan ini ada

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'activity_title',
        'activity_description',
        // 'status', // Hapus jika ini tidak digunakan untuk status utama absensi
    ];

    // Kolom yang akan di-cast ke tipe data tertentu.
    // Ini berarti kolom check_in dan check_out di DB Anda harus bertipe DATETIME.
    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // '$appends' berguna agar accessor ini selalu disertakan saat model di-convert ke array/JSON
    protected $appends = ['attendance_status', 'formatted_date', 'day_name'];

    /**
     * Get the attendance status.
     * Logika disederhanakan menjadi hanya "Complete" dan "Absent (Belum Lengkap)".
     *
     * @return string
     */
    public function getAttendanceStatusAttribute()
    {
        // Jika check_in dan check_out keduanya terisi (tidak null)
        if ($this->check_in && $this->check_out) {
            return 'Lengkap';
        }

        // Jika salah satu (check_in atau check_out) kosong, berarti "Absent (Belum Lengkap)"
        // Ini akan mencakup:
        // - Hanya check_in ada, check_out kosong
        // - Hanya check_out ada, check_in kosong
        // - Keduanya kosong (untuk record yang ada tapi tidak ada data absen sama sekali)
        return 'Tidak Hadir (Belum Lengkap)';
    }

    /**
     * Accessor untuk mendapatkan format tanggal 'DD Month YYYY'
     * Contoh: '03 Juni 2025'
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        // Pastikan Carbon::setLocale('id') sudah diatur di AppServiceProvider atau di tempat lain
        return $this->date->translatedFormat('d F Y');
    }

    /**
     * Accessor untuk mendapatkan nama hari (Senin, Selasa, dst.)
     * Contoh: 'Selasa'
     *
     * @return string
     */
    public function getDayNameAttribute()
    {
        // Pastikan Carbon::setLocale('id') sudah diatur di AppServiceProvider atau di tempat lain
        return $this->date->translatedFormat('l');
    }

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}