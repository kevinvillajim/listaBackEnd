<?php

namespace App\Observers;

use App\Models\Miembro;

class MiembroObserver
{

    /**
     * Handle Miembro "saving" event.
     */
    public function saving(Miembro $miembro)
    {
        if ($miembro->lastAttendance) {
            $miembro->active = $miembro->lastAttendance->isAfter(now()->subMonth());
        } else {
            $miembro->active = false;
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
