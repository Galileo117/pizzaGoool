<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class PedidoController extends Controller
{
    public function index()
    {
        $repartidorId = auth()->id();

        // Pedidos asignados a este repartidor que aún no están entregados
        $pedidos = Pedido::with(['usuario.cliente', 'detallePedidos.producto'])
            ->where('repartidor_id', $repartidorId)
            ->whereIn('estado', ['en_reparto', 'entregado'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('repartidor.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        // Validar que el pedido le pertenezca a este repartidor
        if ($pedido->repartidor_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        $pedido->load(['usuario.cliente', 'detallePedidos.producto']);

        return view('repartidor.pedidos.show', compact('pedido'));
    }

    public function marcarEntregado(Pedido $pedido)
    {
        // Validar que el pedido le pertenezca a este repartidor
        if ($pedido->repartidor_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar este pedido.');
        }

        // Validar que esté en estado "en_reparto"
        if ($pedido->estado !== 'en_reparto') {
            return redirect()->back()->with('error', 'Este pedido no está en reparto.');
        }

        $pedido->update(['estado' => 'entregado']);

        return redirect()->route('repartidor.pedidos.index')
            ->with('success', 'Pedido marcado como entregado correctamente.');
    }
}
