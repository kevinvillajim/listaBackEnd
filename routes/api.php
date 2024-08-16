<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\AsistenciaController;
use App\Models\User;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('edit-profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/change-password', [AuthController::class, 'changePassword']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('users', function () {
        return User::all();
    });
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('miembros', [MiembroController::class, 'index']);
    Route::get('miembros/attendance', [MiembroController::class, 'indexAttendance']);
    Route::get('miembros/{id}/last-attendance', [MiembroController::class, 'getLastAttendance']);
    Route::post('miembros', [MiembroController::class, 'store']);
    Route::put('miembros/{id}', [MiembroController::class, 'update']);
    Route::delete('miembros/{id}', [MiembroController::class, 'destroy']);
    Route::get('miembros/activity', [MiembroController::class, 'getActiveInactiveCount']);
    Route::get('miembros/calling', [MiembroController::class, 'getCallingCount']);
});

// Rutas para la gestión de asistencia
Route::middleware(['auth:api'])->group(function () {
    Route::get('asistencias/fecha/{date}', [AsistenciaController::class, 'getAsistenciaByDate']);
    Route::get('asistencias/miembro/{miembroId}', [AsistenciaController::class, 'getAsistenciaByMiembro']);
    Route::get('asistencias/miembro/{miembroId}/fecha/{date}', [AsistenciaController::class, 'getAsistenciaByMiembroAndDate']);
    Route::post('asistencias', [AsistenciaController::class, 'store']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('asistencias/chart', [AsistenciaController::class, 'getFormattedAttendanceData']);
});
