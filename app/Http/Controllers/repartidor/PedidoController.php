<?php

namespace App\Http\Controllers\repartidor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

/**
 * Limita la consulta y las acciones del panel a los pedidos asignados al repartidor.
 */
class PedidoController extends Controller
{
    public function index()
    {
        $repartidorId = auth()->id();

        $pedidos = Pedido::with(['usuario.cliente', 'detallePedidos.producto'])
            ->where('repartidor_id', $repartidorId)
            ->whereIn('estado', ['en_reparto', 'entregado', 'cancelado'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('repartidor.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        if ($pedido->repartidor_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        $pedido->load(['usuario.cliente', 'detallePedidos.producto']);

        return view('repartidor.pedidos.show', compact('pedido'));
    }

    public function marcarEntregado(Pedido $pedido)
    {
        if ($pedido->repartidor_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar este pedido.');
        }

        if (! $pedido->puedeCambiarEstado('entregado')) {
            return redirect()->back()->with('error', 'Este pedido no está en reparto.');
        }

        $pedido->update(['estado' => 'entregado']);

        return redirect()->route('repartidor.pedidos.index')
            ->with('success', 'Pedido marcado como entregado correctamente.');
    }

    // NUEVO MÉTODO: Reportar pedido fallido
    public function marcarFallido(Request $request, Pedido $pedido)
    {
        if ($pedido->repartidor_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar este pedido.');
        }

        if (! $pedido->puedeCambiarEstado('cancelado')) {
            return redirect()->back()->with('error', 'Este pedido ya no puede reportarse como fallido.');
        }

        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $pedido->update([
            'estado' => 'cancelado',
            'referencia' => $pedido->referencia.' | Fallo: '.$request->motivo,
        ]);

        return redirect()->route('repartidor.pedidos.index')
            ->with('success', 'Pedido reportado como fallido. El cajero ha sido notificado.');
    }
}
