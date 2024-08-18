<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Miembro;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $date = $request->input('date');
        $records = $request->input('records');

        DB::beginTransaction();

        try {
            // Verificar si ya existe una asistencia para esta fecha
            $asistencia = Asistencia::firstOrCreate(
                ['date' => $date],
                ['is_submitted' => true]
            );

            if ($asistencia->is_submitted) {
                return response()->json(['message' => 'Ya se ha enviado la asistencia para esta fecha'], 400);
            }

            // Preparar los datos para la inserción
            $attendanceData = [];
            foreach ($records as $record) {
                $attendanceData[$record['id']] = [
                    'attended' => $record['attended'],
                    'attendedClass' => $record['attendedClass'],
                ];
                if ($record['attended']) {
                    $miembrosAsistieron[] = $record['id'];
                }
            }

            // Actualizar o crear los registros de asistencia
            $asistencia->miembros()->sync($attendanceData);

            Miembro::whereIn('id', $miembrosAsistieron)->update(['lastAttendance' => $date]);

            // Marcar la asistencia como enviada
            $asistencia->is_submitted = true;
            $asistencia->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar la asistencia'], 500);
        }

        return response()->json(['message' => 'Asistencia guardada correctamente'], 200);
    }

    public function updateAttendedClass($miembroId, $asistenciaId, Request $request)
    {
        $attendedClass = $request->input('attendedClass');

        DB::table('asistencia_miembro')
            ->where('miembro_id', $miembroId)
            ->where('asistencia_id', $asistenciaId)
            ->update(['attendedClass' => $attendedClass]);

        return response()->json(['message' => 'Asistencia a clases actualizada correctamente'], 200);
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
        $asistencia = Asistencia::with('miembros')->where('date', $date)->first();

        if (!$asistencia) {
            return response()->json([], 204);
        }

        $records = $asistencia->miembros->map(function ($miembro) {
            return [
                'id' => $miembro->id,
                'name' => $miembro->name,
                'avatar' => $miembro->avatar,
                'attended' => $miembro->pivot->attended,
                'attendedClass' => $miembro->pivot->attendedClass,
            ];
        });

        return response()->json([
            'id' => $asistencia->id,
            'date' => $asistencia->date,
            'records' => $records,
        ]);
    }

    /**
     * Get the attendance records for a given user.
     */
    public function getAsistenciaByMiembro($miembroId)
    {
        $miembro = Miembro::with(['asistencias' => function ($query) {
            $query->wherePivot('attended', true); // Filtra las asistencias con 'attended' true
        }])->find($miembroId);

        if (!$miembro) {
            return response()->json(['message' => 'No se encontró el miembro'], 404);
        }

        $asistencias = $miembro->asistencias->map(function ($asistencia) {
            return [
                'id' => $asistencia->id,
                'date' => $asistencia->date,
                'attended' => $asistencia->pivot->attended,
                'attendedClass' => $asistencia->pivot->attendedClass,
            ];
        });
        return response()->json($asistencias);
    }

    /**
     * Get the attendance records for a given user and date.
     */
    public function getAsistenciaByMiembroAndDate($miembroId, $date)
    {
        $asistencia = Asistencia::where('date', $date)
            ->with([
                'miembros' => function ($query) use ($miembroId) {
                    $query->where('miembro_id', $miembroId);
                }
            ])
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

        // Verifica si 'attendedClass' existe y contiene datos
        $formattedData = $asistencias->map(function ($asistencia) use ($totalMembers) {
            // Asegúrate de que 'pivot.attended' es correcto si estás usando la relación pivot
            $attended = $asistencia->miembros->where('pivot.attended', true)->count();
            $attendedClass = $asistencia->miembros->where('pivot.attendedClass', true)->count();  // Verifica que 'attendedClass' esté presente

            $absent = $totalMembers - $attended;

            // Formatea la fecha
            $formattedData = Carbon::parse($asistencia->date)->format('d-m-Y');
            return [
                'date' => $formattedData,
                'attended' => $attended,
                'attendedClass' => $attendedClass,
                'absent' => $absent,
                'total' => $totalMembers,
            ];
        });

        return response()->json($formattedData);
    }
}
