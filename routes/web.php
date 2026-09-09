<?php

use App\Http\Controllers\Cliente\CatalogoController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('cliente.home');

Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('cliente.catalogo');
    
    // Ruta para agregar al carrito (con AJAX, no recarga)
    Route::post('/catalogo/agregar/{productoId}', [CatalogoController::class, 'agregarAlCarrito'])->name('cliente.agregar_al_carrito');
    
    // Ruta untuk guardar el carrito antes de ir al checkout
    Route::post('/catalogo/guardar_carrito', [CatalogoController::class, 'guardarCarrito'])->name('cliente.guardar_carrito');
    
    Route::get('/checkout', function () {
        return view('cliente.checkout');
    })->name('cliente.checkout');
    
    Route::post('/procesar_pedido', [PedidoController::class, 'procesar'])->name('cliente.procesar_pedido');
    
    Route::get('/seguimiento/{id}', function ($id) {
        $pedido = \App\Models\Pedido::findOrFail($id);
        return view('cliente.seguimiento', compact('pedido'));
    })->name('cliente.seguimiento');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:cajero'])->group(function () {
    Route::get('/cajero', function () {
        return view('cajero.dashboard');
    })->name('cajero.dashboard');
});

Route::middleware(['auth', 'role:repartidor'])->group(function () {
    Route::get('/repartidor', function () {
        return view('repartidor.dashboard');
    })->name('repartidor.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';