<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MiembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Miembro::all();
    }

    public function indexAttendance()
    {
        $miembros = Miembro::with(['asistencias' => function ($query) {
            $query->latest('date')->take(1);
        }])->get()->map(function ($miembro) {
            $lastAttendance = $miembro->asistencias->first();
            $lastAttendanceDate = $lastAttendance ? Carbon::parse($lastAttendance->date)->format('Y-m-d') : null;

            $now = Carbon::now();
            $active = 3;

            if ($lastAttendanceDate) {
                $parsedDate = Carbon::parse($lastAttendanceDate);
                if ($parsedDate->isAfter($now->subWeeks(2))) {
                    $active = 1;
                } elseif ($parsedDate->isAfter($now->subWeeks(6))) {
                    $active = 2;
                }
            }

            return [
                'id' => $miembro->id,
                'name' => $miembro->name,
                'avatar' => $miembro->avatar,
                'phone' => $miembro->phone,
                'calling' => $miembro->calling,
                'organization' => $miembro->organization,
                'lastAttendance' => $lastAttendanceDate,
                'active' => $active,
            ];
        });

        return response()->json($miembros);
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
        request()->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable',
            'phone' => 'nullable',
            'calling' => 'nullable',
            'organization' => 'nullable',
            'active' => 'boolean',
            'lastAttendance' => 'nullable',
        ]);

        return Miembro::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Miembro $miembro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Miembro $miembro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable',
            'phone' => 'nullable',
            'calling' => 'nullable',
            'organization' => 'nullable',
            'active' => 'integer',
            'lastAttendance' => 'nullable',
        ]);

        $miembro = Miembro::findOrFail($id);
        $miembro->update($request->all());

        return response()->json($miembro);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $miembro = Miembro::find($id);

        if (!$miembro) {
            return response()->json(['error' => 'Miembro no encontrado'], 404);
        }

        try {
            $miembro->delete();
            return response()->json(['message' => 'Miembro eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo eliminar el miembro'], 500);
        }
    }

    public function getLastAttendance($id)
    {
        $miembro = Miembro::findOrFail($id);
        return response()->json([
            'lastAttendance' => $miembro->lastAttendance,
            'active' => $miembro->active,
        ]);
    }

    public function getActiveInactiveCount()
    {
        $activeCount = Miembro::where('active', 1)->count();
        $almostInactiveCount = Miembro::where('active', 2)->count();
        $inactiveCount = Miembro::where('active', 3)->count();
        $totalCount = Miembro::count();

        return response()->json([
            'active' => $activeCount,
            'inactive' => $inactiveCount,
            'total' => $totalCount,
        ]);
    }

    public function getCallingCount()
    {
        $noCallingCount = Miembro::where('calling', null)->count();
        $totalCount = Miembro::count();
        $callingCount = $totalCount - $noCallingCount;

        return response()->json([
            'calling' => $callingCount,
            'noCalling' => $noCallingCount,
            'total' => $totalCount,
        ]);
    }
}
