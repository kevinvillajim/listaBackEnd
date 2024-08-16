<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    protected $casts = [
        'active' => 'boolean',
        'lastAttendance' => 'date',
    ];

    public function asistencias()
    {
        return $this->belongsToMany(Asistencia::class)
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function updateLastAttendance($date)
    {
        $this->lastAttendance = Carbon::parse($date); // Asegúrate de que el valor sea una instancia de Carbon
        $this->active = $this->lastAttendance->isAfter(now()->subMonth());
        $this->save();
    }
}
