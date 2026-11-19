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
    public function index(Request $request)
    {
        $repartidorId = auth()->id();
        $estadosDisponibles = [
            'en_reparto' => 'En reparto',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];
        $estado = $request->query('estado', 'en_reparto');

        if (! is_string($estado) || ! array_key_exists($estado, $estadosDisponibles)) {
            $estado = 'en_reparto';
        }

        $pedidos = Pedido::with(['usuario.cliente', 'detallePedidos.producto'])
            ->where('repartidor_id', $repartidorId)
            ->where('estado', $estado)
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('repartidor.pedidos.index', compact('pedidos', 'estado', 'estadosDisponibles'));
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
