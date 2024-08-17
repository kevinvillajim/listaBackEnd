<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MessageTemplate::all();
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
        $request->validate([
            'savedMessage' => 'required|string',
        ]);

        return MessageTemplate::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(MessageTemplate $messageTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageTemplate $messageTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'savedMessage' => 'required|string',
        ]);


        $messageTemplate = MessageTemplate::findOrFail($id);
        $messageTemplate->update($request->all());

        return response()->json($messageTemplate, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $messageTemplate = MessageTemplate::find($id);

        if (!$messageTemplate) {
            return response()->json([
                'message' => 'Record not found',
            ], 404);
        }
        try {
            $messageTemplate->delete();
            return response()->json(['message' => 'Mensaje eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el mensaje'], 500);
        }
    }
}
