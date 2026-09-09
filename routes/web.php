<?php

use App\Http\Controllers\Cliente\CatalogoController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogoController::class, 'welcome'])->name('cliente.home');

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('cliente.catalogo');

Route::middleware(['auth', 'role:cliente'])->group(function () {

    Route::post('/catalogo/agregar/{productoId}', [CatalogoController::class, 'agregarAlCarrito'])
        ->name('cliente.agregar_al_carrito');

    Route::post('/catalogo/guardar_carrito', [CatalogoController::class, 'guardarCarrito'])
        ->name('cliente.guardar_carrito');

    Route::post('/catalogo/vaciar', [CatalogoController::class, 'vaciarCarrito'])
        ->name('cliente.vaciar_carrito');

    Route::get('/checkout', function () {
        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('cliente.catalogo')
                ->with('error', 'Tu carrito está vacío. Agrega productos primero.');
        }
        return view('cliente.checkout');
    })->name('cliente.checkout');

    Route::post('/procesar_pedido', [PedidoController::class, 'procesar'])
        ->name('cliente.procesar_pedido');

    Route::get('/seguimiento/{pedido}', function ($pedidoId) {
        $pedido = \App\Models\Pedido::with(['detallePedidos.producto', 'pago'])
            ->findOrFail($pedidoId);

        if ($pedido->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        return view('cliente.seguimiento', compact('pedido'));
    })->name('cliente.seguimiento');

    Route::get('/mis-pedidos', function () {
        $pedidos = \App\Models\Pedido::where('usuario_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('cliente.mis-pedidos', compact('pedidos'));
    })->name('cliente.mis-pedidos');

    Route::delete('/cancelar_pedido/{pedido}', [PedidoController::class, 'cancelar'])
        ->name('cliente.cancelar_pedido');
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