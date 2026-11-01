<?php

namespace App\Http\Controllers\repartidor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class DashboardController extends Controller
{
    public function index()
    {
        $repartidorId = auth()->id();

        $asignados = Pedido::where('repartidor_id', $repartidorId)
            ->where('estado', 'en_reparto')
            ->count();

        $entregadosHoy = Pedido::where('repartidor_id', $repartidorId)
            ->where('estado', 'entregado')
            ->whereDate('updated_at', today())
            ->count();

        $entregadosTotales = Pedido::where('repartidor_id', $repartidorId)
            ->where('estado', 'entregado')
            ->count();

        return view('repartidor.dashboard', compact(
            'asignados',
            'entregadosHoy',
            'entregadosTotales'
        ));
    }
}
