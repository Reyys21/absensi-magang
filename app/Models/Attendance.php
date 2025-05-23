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
        'status', // Pastikan kolom status juga ada di fillable
    ];

    /**
     * Get the attendance status.
     *
     * @return string
     */
    public function getAttendanceStatusAttribute()
    {
        // Jika tidak ada check-in dan tidak ada check-out
        if (empty($this->check_in) && empty($this->check_out)) {
            return 'Absent';
        }

        // Jika hanya check-in, tidak ada check-out
        if (!empty($this->check_in) && empty($this->check_out)) {
            return 'Not Checked Out';
        }

        // Jika hanya check-out, tidak ada check-in (ini kondisi yang tidak biasa, tapi sesuai permintaan)
        if (empty($this->check_in) && !empty($this->check_out)) {
            return 'Not Checked In'; // Mengubah "Blm Check Out" menjadi "Belum Check-In" agar lebih akurat
        }

        // Jika check-in dan check-out sudah ada
        if (!empty($this->check_in) && !empty($this->check_out)) {
            return 'Complete';
        }

        // Default jika ada kondisi lain yang tidak terduga
        return 'Unknown';
    }
}