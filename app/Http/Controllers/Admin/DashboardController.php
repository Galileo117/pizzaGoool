<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;

/**
 * Prepara métricas y accesos de la portada administrativa.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $totalPedidos = Pedido::count();
        $ventasTotales = Pedido::whereIn('estado', ['entregado', 'en_reparto', 'listo', 'preparacion'])->sum('total');
        $totalProductos = Producto::count();
        $totalClientes = Usuario::where('rol', 'cliente')->count();

        return view('admin.dashboard', compact(
            'totalPedidos',
            'ventasTotales',
            'totalProductos',
            'totalClientes'
        ));
    }
}
