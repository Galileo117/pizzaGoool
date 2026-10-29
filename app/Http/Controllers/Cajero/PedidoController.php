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

        // Validación extra de seguridad: Evitar que el cajero cancele si ya está en reparto
        if ($request->estado === 'cancelado' && in_array($pedido->estado, ['en_reparto', 'entregado'])) {
            return redirect()->back()->with('error', 'No puedes cancelar un pedido que ya está en reparto o entregado.');
        }

        $pedido->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function asignarRepartidor(Request $request, Pedido $pedido)
    {
        $request->validate([
            'repartidor_id' => 'required|exists:usuarios,id',
        ]);

        $pedido->update([
            'repartidor_id' => $request->repartidor_id,
            'estado' => 'en_reparto',
        ]);

        return redirect()->back()->with('success', 'Repartidor asignado correctamente.');
    }
}
