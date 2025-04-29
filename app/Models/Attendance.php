<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'name', // <- DITAMBAHKAN bro!
        'date',
        'check_in',
        'check_out',
        'activity_title',
        'activity_description',
    ];
}