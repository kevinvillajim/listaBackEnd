<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miembro;
use Illuminate\Support\Facades\Log;

class MiembroImportController extends Controller
{
    public function import(Request $request)
    {

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        Log::info('Inicio de la importación de miembros');

        // Validar que el archivo sea un CSV o TXT
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        // Obtener el path real del archivo subido y abrirlo para lectura
        $path = $request->file('file')->getRealPath();
        Log::info("Path del archivo: $path");

        $file = fopen($path, 'r');
        Log::info('Archivo abierto correctamente');

        // Leer la primera línea para obtener el encabezado
        $header = fgetcsv($file, 0, ';');
        Log::info('Encabezado leído: ' . implode(', ', $header));

        $csvData = [];
        $rowNumber = 1; // Para seguimiento de filas

        // Leer el resto del archivo
        while ($row = fgetcsv($file, 0, ';')) {
            // Limpiar los espacios en blanco de cada celda
            $row = array_map('trim', $row);

            // Convertir campos vacíos a null
            $row = array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $row);

            $csvData[] = array_combine($header, $row);
            Log::info("Fila $rowNumber leída: " . implode(', ', $row));
            $rowNumber++;
        }

        // Cerrar el archivo
        fclose($file);
        Log::info('Archivo cerrado correctamente');

        // Obtener todos los miembros existentes e indexarlos por su nombre
        $existingMiembros = Miembro::all()->keyBy('name');
        Log::info('miembros existentes cargados');

        // Obtener los correos electrónicos del CSV
        $namesInCSV = collect($csvData)->pluck('name')->all();
        Log::info('Correos electrónicos en CSV: ' . implode(', ', $namesInCSV));

        $errors = []; // Array para almacenar los errores

        // Crear o actualizar miembros
        foreach ($csvData as $data) {
            try {
                Log::info("Procesando miembro: {$data['name']}");
                if (isset($existingMiembros[$data['name']])) {
                    // Si el miembro existe, actualizarlo
                    $user = $existingMiembros[$data['name']];
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'calling' => $data['calling'],
                        'organization' => $data['organization'],
                    ]);
                    Log::info("Miembro actualizado: {$data['name']}");
                } else {
                    // Si el miembro no existe, crearlo
                    Miembro::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'calling' => $data['calling'],
                        'organization' => $data['organization'],
                    ]);
                    Log::info("Miembro creado: {$data['name']}");
                }
            } catch (\Exception $e) {
                // Agregar el error al array
                $errors[] = [
                    'name' => $data['name'],
                    'error' => $e->getMessage(),
                ];

                // Registrar el error en el log para revisarlo más tarde
                Log::error("Error importando miembro {$data['name']}: {$e->getMessage()}");
            }
        }

        // Retornar el resultado de la importación
        Log::info('Finalización de la importación de miembros');
        return response()->json([
            'message' => 'Miembros imported successfully',
            'errors' => $errors, // Incluir los errores en la respuesta
        ], 200);
    }
}
