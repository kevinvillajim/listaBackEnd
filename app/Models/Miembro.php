<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $fillable = [
        'name',
        'avatar',
        'phone',
        'calling',
        'organization',
        'active',
        'lastAttendance',
    ];
}
