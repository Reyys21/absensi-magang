<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    protected $appends = ['attendance_status', 'formatted_date', 'day_name'];

    /**
     * Get the attendance status.
     *
     * @return string
     */
    public function getAttendanceStatusAttribute()
    {
        $today = Carbon::now()->toDateString();
        $recordDate = $this->date->toDateString();

        // Jika tidak ada check-in dan tidak ada check-out
        if (empty($this->check_in) && empty($this->check_out)) {
            // Jika tanggalnya hari ini dan belum ada data, anggap belum ada data
            if ($recordDate == $today) {
                return 'No Data Yet'; // Atau bisa juga 'Waiting'
            }
            // Jika tanggalnya di masa lalu dan tidak ada data, berarti Absent
            if ($this->date->isPast() && $recordDate != $today) {
                return 'Absent';
            }
            // Untuk tanggal di masa depan (jika ada data kosong untuk masa depan)
            return 'Upcoming';
        }

        // Jika hanya check-in (tidak ada check-out) ATAU hanya check-out (tidak ada check-in)
        if ((!empty($this->check_in) && empty($this->check_out)) || (empty($this->check_in) && !empty($this->check_out))) {
            return 'Absent (Belum Lengkap)'; // Menggabungkan "Not Checked In" dan "Not Checked Out"
        }

        // Jika check-in dan check-out sudah ada
        if (!empty($this->check_in) && !empty($this->check_out)) {
            return 'Complete';
        }

        // Default jika ada kondisi lain yang tidak terduga
        return 'Unknown';
    }

    // Accessor untuk mendapatkan format tanggal 'DD Month YYYY'
    public function getFormattedDateAttribute()
    {
        return $this->date->translatedFormat('d F Y');
    }

    // Accessor untuk mendapatkan nama hari (Senin, Selasa, dst.)
    public function getDayNameAttribute()
    {
        return $this->date->translatedFormat('l');
    }

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}