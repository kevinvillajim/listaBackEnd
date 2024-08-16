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
        'active' => 'integer',
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
        $now = Carbon::now();

        if ($this->lastAttendance->isAfter($now->subWeeks(2))) {
            $this->active = 1;
        } elseif ($this->lastAttendance->isAfter($now->subWeeks(6))) {
            $this->active = 2;
        } else {
            $this->active = 3;
        }

        $this->save();
    }
}
