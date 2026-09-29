<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function procesar(Request $request)
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('cliente.catalogo')
                ->with('error', 'Tu carrito está vacío.');
        }

        $request->validate([
            'tipo_entrega' => 'required|in:recoger_local,contraentrega',
            'metodo_pago' => 'required|in:efectivo,qr',
            'direccion' => 'required_if:tipo_entrega,contraentrega|nullable|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $pedido = Pedido::create([
            'usuario_id' => auth()->id(),
            'total' => $total,
            'estado' => 'pendiente',
            'metodo_pago' => $request->metodo_pago,
            'tipo_entrega' => $request->tipo_entrega,
            'direccion' => $request->direccion ?? null,
            'referencia' => $request->referencia ?? null,
            'latitud' => $request->latitud ?? null,
            'longitud' => $request->longitud ?? null,
        ]);

        foreach ($carrito as $item) {
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => $total,
            'metodo' => $request->metodo_pago,
            'estado_pago' => 'pendiente',
            'codigo_qr' => null,
        ]);

        session()->forget('carrito');

        return redirect()
            ->route('cliente.seguimiento', $pedido->id)
            ->with('success', '¡Pedido creado correctamente!');
    }

    public function cancelar($pedidoId)
    {
        $pedido = Pedido::findOrFail($pedidoId);

        if ($pedido->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para cancelar este pedido.');
        }

        if ($pedido->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Este pedido ya no se puede cancelar.');
        }

        $pedido->update(['estado' => 'cancelado']);

        return redirect()->route('cliente.mis_pedidos')
            ->with('success', 'Pedido cancelado correctamente.');
    }

    public function misPedidos()
    {
        $pedidos = Pedido::where('usuario_id', auth()->id())
            ->with(['detallePedidos.producto', 'pago'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.mis-pedidos', compact('pedidos'));
    }
}