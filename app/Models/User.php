<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // <<< TAMBAHKAN IMPORT INI

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // <<< TAMBAHKAN HasRoles DI SINI

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Kita biarkan dulu untuk sementara agar registrasi lama tidak error
        'asal_kampus',
        'nim',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relasi ke Attendance (ini dari kode Anda sebelumnya, tetap dipertahankan)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}