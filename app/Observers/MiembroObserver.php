<?php

namespace App\Observers;

use App\Models\Miembro;
use Carbon\Carbon;

class MiembroObserver
{

    /**
     * Handle Miembro "saving" event.
     */
    public function saving(Miembro $miembro)
    {
        if ($miembro->lastAttendance) {
            $now = Carbon::now();

            if ($miembro->lastAttendance->isAfter($now->subWeeks(2))) {
                $miembro->active = 1;
            } elseif ($miembro->lastAttendance->isAfter($now->subWeeks(6))) {
                $miembro->active = 2;
            } else {
                $miembro->active = 3;
            }
        } else {
            $miembro->active = 3;
        }
    }
    /**
     * Handle the Miembro "created" event.
     */
    public function created(Miembro $miembro): void
    {
        //
    }

    /**
     * Handle the Miembro "updated" event.
     */
    public function updated(Miembro $miembro): void
    {
        //
    }

    /**
     * Handle the Miembro "deleted" event.
     */
    public function deleted(Miembro $miembro): void
    {
        //
    }

    /**
     * Handle the Miembro "restored" event.
     */
    public function restored(Miembro $miembro): void
    {
        //
    }

    /**
     * Handle the Miembro "force deleted" event.
     */
    public function forceDeleted(Miembro $miembro): void
    {
        //
    }
}
