<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'date',
        'name',
        'avatar',
        'phone',
        'calling',
        'organization',
        'active',
        'lastAttendance',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function miembros()
    {
        return $this->belongsToMany(Miembro::class)
            ->withPivot('attended')
            ->withTimestamps();
    }
}
