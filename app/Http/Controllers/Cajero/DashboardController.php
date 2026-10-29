<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class DashboardController extends Controller
{
    public function index()
    {
        $pendientes = Pedido::where('estado', 'pendiente')->count();
        $enPreparacion = Pedido::where('estado', 'preparacion')->count();
        $listos = Pedido::where('estado', 'listo')->count();
        $enReparto = Pedido::where('estado', 'en_reparto')->count();

        return view('cajero.dashboard', compact(
            'pendientes',
            'enPreparacion',
            'listos',
            'enReparto'
        ));
    }
}
