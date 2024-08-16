<?php

namespace App\Observers;

use App\Models\Asistencia;
use App\Models\Miembro;

class AsistenciaObserver
{
    /**
     * Handle the Asistencia "saved" event.
     */
    public function saved(Asistencia $asistencia)
    {
        // Obtener todos los miembros relacionados con esta asistencia
        $miembros = $asistencia->miembros;

        foreach ($miembros as $miembro) {
            // Actualizar lastAttendance para cada miembro
            if ($miembro->pivot->attended) {
                $miembro->updateLastAttendance($asistencia->date);
            } else {
                $miembro->active = 3;
                $miembro->save();
            }
        }
    }
    /**
     * Handle the Asistencia "created" event.
     */
    public function created(Asistencia $asistencia): void
    {
        //
    }

    /**
     * Handle the Asistencia "updated" event.
     */
    public function updated(Asistencia $asistencia): void
    {
        //
    }

    /**
     * Handle the Asistencia "deleted" event.
     */
    public function deleted(Asistencia $asistencia): void
    {
        //
    }

    /**
     * Handle the Asistencia "restored" event.
     */
    public function restored(Asistencia $asistencia): void
    {
        //
    }

    /**
     * Handle the Asistencia "force deleted" event.
     */
    public function forceDeleted(Asistencia $asistencia): void
    {
        //
    }
}
