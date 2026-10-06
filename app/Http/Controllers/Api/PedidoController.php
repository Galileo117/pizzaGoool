<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Http\Traits\ApiResponse;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    use ApiResponse;

    /**
     * Crear un pedido desde la app móvil.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1|max:20',
            'metodo_pago' => 'required|in:qr,efectivo',
            'tipo_entrega' => 'required|in:recoger_local,contraentrega',
            'direccion' => 'required_if:tipo_entrega,contraentrega|nullable|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $detalles = [];

            foreach ($request->items as $item) {
                $producto = Producto::find($item['producto_id']);

                if (!$producto || !$producto->disponible) {
                    DB::rollBack();
                    return $this->errorResponse("El producto {$item['producto_id']} no está disponible.", 422);
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ];
            }

            $pedido = Pedido::create([
                'usuario_id' => $request->user()->id,
                'total' => $total,
                'estado' => 'pendiente',
                'metodo_pago' => $request->metodo_pago,
                'tipo_entrega' => $request->tipo_entrega,
                'direccion' => $request->direccion,
                'referencia' => $request->referencia,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
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

            DB::commit();

            $pedido->load('detallePedidos.producto', 'repartidor');

            return $this->successResponse(
                new PedidoResource($pedido),
                'Pedido creado correctamente.',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear el pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Listar los pedidos del cliente autenticado.
     */
    public function index(Request $request)
    {
        $pedidos = Pedido::where('usuario_id', $request->user()->id)
            ->with(['detallePedidos.producto', 'repartidor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(
            PedidoResource::collection($pedidos),
            'Lista de pedidos.'
        );
    }

    /**
     * Ver el detalle de un pedido.
     */
    public function show(Request $request, $id)
    {
        $pedido = Pedido::with(['detallePedidos.producto', 'repartidor', 'pago'])
            ->where('usuario_id', $request->user()->id)
            ->find($id);

        if (!$pedido) {
            return $this->notFoundResponse('Pedido no encontrado.');
        }

        return $this->successResponse(
            new PedidoResource($pedido),
            'Detalle del pedido.'
        );
    }

    /**
     * Cancelar un pedido (solo si está pendiente).
     */
    public function cancelar(Request $request, $id)
    {
        $pedido = Pedido::where('usuario_id', $request->user()->id)->find($id);

        if (!$pedido) {
            return $this->notFoundResponse('Pedido no encontrado.');
        }

        if ($pedido->estado !== 'pendiente') {
            return $this->errorResponse('Este pedido ya no se puede cancelar.', 422);
        }

        $pedido->update(['estado' => 'cancelado']);

        return $this->successResponse(null, 'Pedido cancelado correctamente.');
    }
}
