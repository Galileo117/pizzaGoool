<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Crea pedidos web y ofrece al cliente su historial y cancelación permitida.
 */
class PedidoController extends Controller
{
    /**
     * Revalida catálogo/precios y persiste pedido, líneas y pago en una transacción.
     */
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
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        $pedido = DB::transaction(function () use ($request, $carrito) {
            $idsProductos = array_column($carrito, 'producto_id');
            $productos = Producto::whereIn('id', $idsProductos)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $totalCentavos = 0;
            $detalles = [];

            foreach ($carrito as $clave => $item) {
                $producto = $productos->get((int) ($item['producto_id'] ?? $clave));

                if (! $producto || ! $producto->disponible) {
                    throw ValidationException::withMessages([
                        'carrito' => 'El carrito contiene productos que ya no están disponibles.',
                    ]);
                }

                $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT);

                if ($cantidad === false || $cantidad < 1 || $cantidad > 20) {
                    throw ValidationException::withMessages([
                        'carrito' => 'La cantidad de un producto del carrito no es válida.',
                    ]);
                }

                $precioCentavos = (int) round((float) $producto->precio * 100);
                $precioAnteriorCentavos = (int) round((float) ($item['precio'] ?? 0) * 100);

                if ($precioAnteriorCentavos !== $precioCentavos) {
                    $carrito[$clave]['precio'] = number_format($precioCentavos / 100, 2, '.', '');
                    session()->put('carrito', $carrito);

                    throw ValidationException::withMessages([
                        'carrito' => 'El precio de un producto cambió. Revisa el total actualizado antes de confirmar.',
                    ]);
                }

                $totalCentavos += $precioCentavos * $cantidad;
                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => number_format($precioCentavos / 100, 2, '.', ''),
                ];
            }

            $total = number_format($totalCentavos / 100, 2, '.', '');

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

            foreach ($detalles as $detalle) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                ]);
            }

            Pago::create([
                'pedido_id' => $pedido->id,
                'monto' => $total,
                'metodo' => $request->metodo_pago,
                'estado_pago' => 'pendiente',
                'codigo_qr' => null,
            ]);

            return $pedido;
        });

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
