<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Cajero\DashboardController as CajeroDashboardController;
use App\Http\Controllers\Cajero\PedidoController as CajeroPedidoController;
use App\Http\Controllers\Cliente\CatalogoController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\repartidor\DashboardController as RepartidorDashboardController;
use App\Http\Controllers\repartidor\PedidoController as RepartidorPedidoController;
use App\Models\Pedido;
use Illuminate\Support\Facades\Route;

/*
 * Rutas HTML agrupadas por rol; los alias de autorización se registran en bootstrap/app.php.
 */
// Rutas públicas
Route::get('/', [InicioController::class, 'index'])->name('cliente.home');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('cliente.catalogo');

// Rutas del Cliente
Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::post('/catalogo/agregar/{productoId}', [CatalogoController::class, 'agregarAlCarrito'])->name('cliente.agregar_al_carrito');
    Route::post('/catalogo/guardar_carrito', [CatalogoController::class, 'guardarCarrito'])->name('cliente.guardar_carrito');
    Route::post('/catalogo/vaciar', [CatalogoController::class, 'vaciarCarrito'])->name('cliente.vaciar_carrito');

    Route::get('/checkout', [CatalogoController::class, 'mostrarCheckout'])->name('cliente.checkout');

    Route::post('/procesar_pedido', [PedidoController::class, 'procesar'])->name('cliente.procesar_pedido');
    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('cliente.mis_pedidos');

    Route::get('/seguimiento/{pedido}', function ($pedidoId) {
        $pedido = Pedido::with(['detallePedidos.producto', 'pago'])->findOrFail($pedidoId);
        if ($pedido->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        return view('cliente.seguimiento', compact('pedido'));
    })->name('cliente.seguimiento');

    Route::delete('/cancelar_pedido/{pedido}', [PedidoController::class, 'cancelar'])->name('cliente.cancelar_pedido');

    Route::get('/mi-perfil', function () {
        return view('cliente.perfil');
    })->name('cliente.perfil');
});

// Rutas del Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::get('pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
    Route::resource('usuarios', UsuarioController::class);
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/pdf', [ReporteController::class, 'pdf'])->name('reportes.pdf');
    Route::get('reportes/excel', [ReporteController::class, 'excel'])->name('reportes.excel');
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::post('configuracion/qr', [ConfiguracionController::class, 'guardarQR'])->name('configuracion.guardar_qr');
});

// Rutas del Cajero
Route::middleware(['auth', 'role:cajero'])->prefix('cajero')->name('cajero.')->group(function () {
    Route::get('/', [CajeroDashboardController::class, 'index'])->name('dashboard');
    Route::get('pedidos', [CajeroPedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{pedido}', [CajeroPedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('pedidos/{pedido}/estado', [CajeroPedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar_estado');
    Route::patch('pedidos/{pedido}/asignar-repartidor', [CajeroPedidoController::class, 'asignarRepartidor'])->name('pedidos.asignar_repartidor');
});

// Rutas del Repartidor
Route::middleware(['auth', 'role:repartidor'])->prefix('repartidor')->name('repartidor.')->group(function () {
    Route::get('/', [RepartidorDashboardController::class, 'index'])->name('dashboard');
    Route::get('pedidos', [RepartidorPedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{pedido}', [RepartidorPedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('pedidos/{pedido}/entregado', [RepartidorPedidoController::class, 'marcarEntregado'])->name('pedidos.marcar_entregado');
    Route::patch('pedidos/{pedido}/fallido', [RepartidorPedidoController::class, 'marcarFallido'])->name('pedidos.marcar_fallido');
});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
