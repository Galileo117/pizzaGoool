<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        // Cargamos las relaciones para optimizar las consultas (Eager Loading)
        $query = Pedido::with(['usuario.cliente', 'detallePedidos.producto', 'repartidor.repartidor'])
            ->orderBy('created_at', 'asc');

        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        } else {
            $query->whereNotIn('estado', ['entregado', 'cancelado']);
        }

        $pedidos = $query->paginate(15);

        return view('cajero.pedidos.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['usuario.cliente', 'detallePedidos.producto', 'pago', 'repartidor.repartidor']);

        // Obtenemos a los usuarios que tienen el rol de repartidor y cargamos su perfil
        $repartidores = Usuario::where('rol', 'repartidor')->with('repartidor')->get();

        return view('cajero.pedidos.show', compact('pedido', 'repartidores'));
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,preparacion,listo,en_reparto,entregado,cancelado',
        ]);

        if ($request->estado === 'cancelado' && in_array($pedido->estado, ['en_reparto', 'entregado'])) {
            return redirect()->back()->with('error', 'No puedes cancelar un pedido que ya está en reparto o entregado.');
        }

        if (
            $request->estado === 'en_reparto'
            || ! $pedido->puedeCambiarEstado($request->estado)
        ) {
            return redirect()->back()->with('error', 'La transición solicitada no es válida para este pedido.');
        }

        $pedido->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function asignarRepartidor(Request $request, Pedido $pedido)
    {
        $request->validate([
            'repartidor_id' => 'required|exists:repartidores,usuario_id',
        ]);

        $esRepartidor = Usuario::whereKey($request->repartidor_id)
            ->where('rol', 'repartidor')
            ->whereHas('repartidor')
            ->exists();

        if (! $esRepartidor) {
            return redirect()->back()->with('error', 'La cuenta seleccionada no tiene un perfil de repartidor válido.');
        }

        if (
            $pedido->tipo_entrega !== 'contraentrega'
            || ! $pedido->puedeCambiarEstado('en_reparto')
        ) {
            return redirect()->back()->with('error', 'Solo se puede asignar un repartidor a un pedido de delivery listo.');
        }

        $pedido->update([
            'repartidor_id' => $request->repartidor_id,
            'estado' => 'en_reparto',
        ]);

        return redirect()->back()->with('success', 'Repartidor asignado correctamente.');
    }
}
