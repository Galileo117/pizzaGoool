<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Pizza Goool funcionando correctamente.',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren token de Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    /*
    |----------------------------------------------------------------------
    | Rutas de Cliente
    |----------------------------------------------------------------------
    */
    Route::middleware('api.role:cliente')->group(function () {
        // Aquí irán los endpoints de pedidos del cliente
    });

    /*
    |----------------------------------------------------------------------
    | Rutas de Repartidor
    |----------------------------------------------------------------------
    */
    Route::middleware('api.role:repartidor')->group(function () {
        // Aquí irán los endpoints de pedidos del repartidor
    });
});
