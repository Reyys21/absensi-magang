<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon; // Pastikan ini di-import

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'old_check_in',
        'old_check_out',
        'new_check_in',
        'new_check_out',
        'old_activity_title',
        'old_activity_description',
        'new_activity_title',
        'new_activity_description',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'old_check_in' => 'datetime',
        'old_check_out' => 'datetime',
        'new_check_in' => 'datetime',
        'new_check_out' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relasi dengan User yang mengajukan koreksi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan User yang menyetujui (jika ada)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessor untuk mendapatkan waktu check-in lama dalam format H:i
    // Karena $casts, $this->old_check_in sudah objek Carbon dalam timezone aplikasi.
    public function getOldCheckInTimeAttribute()
    {
        return $this->old_check_in ? $this->old_check_in->format('H:i') : '--.--';
    }

    // Accessor untuk mendapatkan waktu check-out lama dalam format H:i
    public function getOldCheckOutTimeAttribute()
    {
        return $this->old_check_out ? $this->old_check_out->format('H:i') : '--.--';
    }

    // Accessor untuk mendapatkan waktu check-in baru dalam format H:i
    public function getNewCheckInTimeAttribute()
    {
        return $this->new_check_in ? $this->new_check_in->format('H:i') : '--.--';
    }

    // Accessor untuk mendapatkan waktu check-out baru dalam format H:i
    public function getNewCheckOutTimeAttribute()
    {
        return $this->new_check_out ? $this->new_check_out->format('H:i') : '--.--';
    }
}