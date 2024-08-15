<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use Illuminate\Http\Request;

class MiembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Miembro::all();
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
            'active' => 'boolean',
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
}
