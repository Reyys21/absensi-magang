<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Pastikan Carbon diimport

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
        'status', // Pastikan kolom status juga ada di fillable jika Anda menyimpannya di DB
    ];

    // *** INI ADALAH BAGIAN PENTING YANG MENYELESAIKAN ERROR "Attempt to read property 'day' on string" ***
    protected $casts = [
        'date' => 'date',       // Mengkonversi kolom 'date' menjadi objek Carbon (tanpa waktu)
        'check_in' => 'datetime', // Mengkonversi 'check_in' ke objek Carbon (tanggal dan waktu)
        'check_out' => 'datetime',// Mengkonversi 'check_out' ke objek Carbon (tanggal dan waktu)
    ];

    // Tambahkan ini agar accessor 'attendance_status', 'formatted_date', dan 'day_name'
    // selalu tersedia saat instance model di-serialize atau diubah ke array/JSON.
    protected $appends = ['attendance_status', 'formatted_date', 'day_name'];

    /**
     * Get the attendance status.
     *
     * @return string
     */
    public function getAttendanceStatusAttribute()
    {
        // Mendapatkan waktu saat ini untuk perbandingan dengan tanggal hari ini
        // Karena 'date' sudah di-cast ke Carbon, kita tidak perlu Carbon::parse($this->date) lagi di sini
        $today = Carbon::now()->toDateString();
        $recordDate = $this->date->toDateString(); // Sekarang $this->date adalah objek Carbon

        // Jika tidak ada check-in dan tidak ada check-out
        if (empty($this->check_in) && empty($this->check_out)) {
            // Jika tanggalnya hari ini dan belum ada data, anggap belum ada data
            if ($recordDate == $today) {
                return 'No Data Yet'; // Atau bisa juga 'Waiting'
            }
            // Jika tanggalnya di masa lalu dan tidak ada data, berarti Absent
            if ($this->date->isPast() && $recordDate != $today) { // $this->date adalah objek Carbon
                return 'Absent';
            }
            // Untuk tanggal di masa depan (jika ada data kosong untuk masa depan)
            return 'Upcoming'; // Atau 'No Data Yet'
        }

        // Jika hanya check-in, tidak ada check-out
        if (!empty($this->check_in) && empty($this->check_out)) {
            // Jika hari ini dan hanya check-in, berarti sedang berlangsung
            if ($recordDate == $today) {
                return 'Not Checked Out'; // Sesuai permintaan Anda
            }
            // Jika sudah lewat hari ini dan hanya check-in, berarti belum check-out
            return 'Not Checked Out'; // Sesuai permintaan Anda
        }

        // Jika hanya check-out, tidak ada check-in
        if (empty($this->check_in) && !empty($this->check_out)) {
            return 'Not Checked In';
        }

        // Jika check-in dan check-out sudah ada
        if (!empty($this->check_in) && !empty($this->check_out)) {
            return 'Complete';
        }

        // Default jika ada kondisi lain yang tidak terduga
        return 'Unknown';
    }

    // Accessor untuk mendapatkan format tanggal 'DD Month Neanderthal'
    public function getFormattedDateAttribute()
    {
        // Karena 'date' sudah di-cast ke Carbon, kita bisa langsung menggunakannya
        return $this->date->translatedFormat('d F Y');
    }

    // Accessor untuk mendapatkan nama hari (Senin, Selasa, dst.)
    public function getDayNameAttribute()
    {
        // Karena 'date' sudah di-cast ke Carbon, kita bisa langsung menggunakannya
        return $this->date->translatedFormat('l');
    }

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}