<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RepartidorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

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

// Catálogo (público)
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::get('/configuracion', [App\Http\Controllers\Api\ConfiguracionController::class, 'publica']);
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
        Route::post('/pedidos', [PedidoController::class, 'store']);
        Route::get('/pedidos', [PedidoController::class, 'index']);
        Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
        Route::delete('/pedidos/{id}', [PedidoController::class, 'cancelar']);
    });

    /*
    |----------------------------------------------------------------------
    | Rutas de Repartidor
    |----------------------------------------------------------------------
    */
    Route::middleware('api.role:repartidor')->group(function () {
        Route::get('/repartidor/pedidos', [RepartidorController::class, 'pedidos']);
        Route::get('/repartidor/pedidos/{id}', [RepartidorController::class, 'show']);
        Route::patch('/repartidor/pedidos/{id}/entregar', [RepartidorController::class, 'entregar']);
    });
});
