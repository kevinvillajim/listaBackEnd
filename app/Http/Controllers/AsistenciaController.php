<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'records' => 'required|array',
            'records.*.id' => 'required|exists:miembros,id',
            'records.*.attended' => 'required|boolean',
        ]);

        // Crear la asistencia
        $asistencia = Asistencia::create([
            'date' => $validatedData['date'],
        ]);

        // Adjuntar los miembros a la asistencia
        foreach ($validatedData['records'] as $record) {
            $miembro = Miembro::findOrFail($record['id']);
            $asistencia->miembros()->attach($miembro, [
                'attended' => $record['attended'],
            ]);

            // Actualizar lastAttendance si asistió
            if ($record['attended']) {
                $miembro->updateLastAttendance($validatedData['date']);
            }
        }

        return response()->json(['message' => 'Asistencia registrada correctamente'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Asistencia $asistencia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asistencia $asistencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asistencia $asistencia)
    {
        //
    }

    /**
     * Get the attendance records for a given date.
     */
    public function getAsistenciaByDate($date)
    {
        $asistencia = Asistencia::whereDate('date', $date)->with('miembros')->get();
        if ($asistencia->isEmpty()) {
            return response()->json(['message' => 'No se encontraron asistencias para la fecha dada'], 404);
        }
        return response()->json($asistencia);
    }

    /**
     * Get the attendance records for a given user.
     */
    public function getAsistenciaByMiembro($miembroId)
    {
        $miembro = Miembro::with('asistencias')->were('id', $miembroId)->first();
        if (!$miembro) {
            return response()->json(['message' => 'Miembro no encontrado'], 404);
        }
        return response()->json($miembro->asistencias);
    }

    /**
     * Get the attendance records for a given user and date.
     */
    public function getAsistenciaByMiembroAndDate($miembroId, $date)
    {
        $asistencia = Asistencia::where('date', $date)
            ->with(['miembros' => function ($query) use ($miembroId) {
                $query->where('miembro_id', $miembroId);
            }])
            ->first();

        if (!$asistencia || $asistencia->miembros->isEmpty()) {
            return response()->json(['message' => 'No se encontró asistencia para este usuario en la fecha dada'], 404);
        }

        return response()->json($asistencia->miembros);
    }

    public function getFormattedAttendanceData()
    {
        $totalMembers = Miembro::count();
        $asistencias = Asistencia::with('miembros')->get();
        $formattedData = $asistencias->map(function ($asistencia) use ($totalMembers) {
            $attended = $asistencia->miembros->where('pivot.attended', true)->count();
            $absent = $totalMembers - $attended;

            $formattedData = Carbon::parse($asistencia->date)->format('d-m-Y');
            return [
                'date' => $formattedData,
                'attended' => $attended,
                'absent' => $absent,
                'total' => $totalMembers,
            ];
        });

        return response()->json($formattedData);
    }
}
