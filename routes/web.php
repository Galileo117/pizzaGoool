<?php

use App\Http\Controllers\Cliente\CatalogoController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', [InicioController::class, 'index'])->name('cliente.home');

// Catálogo público (cualquiera puede ver los productos, pero solo clientes compran)
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('cliente.catalogo');

// Rutas protegidas para Clientes
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

// Rutas protegidas para Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::get('pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
});

// Rutas protegidas para Cajero
Route::middleware(['auth', 'role:cajero'])->prefix('cajero')->name('cajero.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Cajero\DashboardController::class, 'index'])->name('dashboard');
    Route::get('pedidos', [\App\Http\Controllers\Cajero\PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{pedido}', [\App\Http\Controllers\Cajero\PedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('pedidos/{pedido}/estado', [\App\Http\Controllers\Cajero\PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar_estado');
    Route::patch('pedidos/{pedido}/asignar-repartidor', [\App\Http\Controllers\Cajero\PedidoController::class, 'asignarRepartidor'])->name('pedidos.asignar_repartidor');
});

// Rutas protegidas para Repartidor
Route::middleware(['auth', 'role:repartidor'])->prefix('repartidor')->name('repartidor.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Repartidor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('pedidos', [\App\Http\Controllers\Repartidor\PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{pedido}', [\App\Http\Controllers\Repartidor\PedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('pedidos/{pedido}/entregado', [\App\Http\Controllers\Repartidor\PedidoController::class, 'marcarEntregado'])->name('pedidos.marcar_entregado');
});

// Rutas de perfil (de Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';