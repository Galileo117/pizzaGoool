<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    public function procesar(Request $request)
    {
        $request->validate([
            'tipo_entrega' => 'required|in:recoger_local,contraentrega',
            'metodo_pago' => 'required|in:efectivo,qr',
            'direccion' => 'required_if:tipo_entrega,contraentrega|nullable|string|max:255',
            'referencia' => 'nullable|string|max:255',
        ]);

        $carrito = session('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('cliente.catalogo')
                ->with('error', 'Tu carrito está vacío.');
        }

        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        DB::beginTransaction();

        try {
            // Crear el pedido
            $pedido = Pedido::create([
                'usuario_id' => auth()->id(),
                'total' => $total,
                'estado' => 'pendiente',
                'metodo_pago' => $request->metodo_pago,
                'tipo_entrega' => $request->tipo_entrega,
                'direccion' => $request->direccion,
                'referencia' => $request->referencia,
            ]);

            // Crear los detalles del pedido
            foreach ($carrito as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                ]);
            }

            // Crear el pago
            Pago::create([
                'pedido_id' => $pedido->id,
                'monto' => $total,
                'metodo' => $request->metodo_pago,
                'estado_pago' => 'pendiente',
                'codigo_qr' => $request->metodo_pago === 'qr' ? Str::random(20) : null,
            ]);

            // Limpiar el carrito
            session()->forget('carrito');

            DB::commit();

            return redirect()->route('cliente.seguimiento', $pedido->id)
                ->with('success', '¡Pedido realizado con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al procesar tu pedido: ' . $e->getMessage());
        }
    }

    public function cancelar(Request $request, $pedidoId)
    {
        $pedido = Pedido::where('usuario_id', auth()->id())
            ->where('id', $pedidoId)
            ->firstOrFail();

        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'No puedes cancelar un pedido que ya está en proceso.');
        }

        $pedido->update(['estado' => 'cancelado']);

        return back()->with('success', 'Pedido cancelado correctamente.');
    }
}