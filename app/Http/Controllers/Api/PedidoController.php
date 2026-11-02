<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Configuracion;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Expone las operaciones de pedidos móviles con aislamiento por usuario autenticado.
 */
class PedidoController extends Controller
{
    use ApiResponse;

    /**
     * Construye la compra desde precios de catálogo actuales, no desde importes del cliente.
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
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        DB::beginTransaction();

        try {
            $totalCentavos = 0;
            $detalles = [];

            foreach ($request->items as $item) {
                $producto = Producto::lockForUpdate()->find($item['producto_id']);

                if (! $producto || ! $producto->disponible) {
                    DB::rollBack();

                    return $this->errorResponse(
                        "El producto {$item['producto_id']} no está disponible.",
                        422
                    );
                }

                $precioCentavos = (int) round((float) $producto->precio * 100);
                $totalCentavos += $precioCentavos * $item['cantidad'];

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => number_format($precioCentavos / 100, 2, '.', ''),
                ];
            }

            $costoEnvioCentavos = 0;
            $esDelivery = $request->input('tipo_entrega') === 'contraentrega';

            if ($esDelivery) {
                $costoEnvioDecimal = Configuracion::obtener('costo_envio', '10.00');
                $costoEnvioCentavos = (int) round((float) $costoEnvioDecimal * 100);
            }

            $totalCentavos += $costoEnvioCentavos;
            $total = number_format($totalCentavos / 100, 2, '.', '');
            $costoEnvioFinal = number_format($costoEnvioCentavos / 100, 2, '.', '');

            $pedido = Pedido::create([
                'usuario_id' => $request->user()->id,
                'total' => $total,
                'costo_envio' => $costoEnvioFinal, // <-- GUARDADO
                'estado' => 'pendiente',
                'metodo_pago' => $request->metodo_pago,
                'tipo_entrega' => $esDelivery ? 'delivery' : 'local',
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

            $pedido->load([
                'usuario.cliente',
                'repartidor.repartidor',
                'detallePedidos.producto',
                'pago',
            ]);

            return $this->successResponse(
                new PedidoResource($pedido),
                'Pedido creado correctamente.',
                201
            );
        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return $this->errorResponse(
                'No se pudo crear el pedido.',
                500
            );
        }
    }

    public function index(Request $request)
    {
        $pedidos = Pedido::where(
            'usuario_id',
            $request->user()->id
        )
            ->with([
                'usuario.cliente',
                'repartidor.repartidor',
                'detallePedidos.producto',
                'pago',
            ])
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse(
            PedidoResource::collection($pedidos),
            'Lista de pedidos.'
        );
    }

    public function show(Request $request, $id)
    {
        $pedido = Pedido::where(
            'usuario_id',
            $request->user()->id
        )
            ->with([
                'usuario.cliente',
                'repartidor.repartidor',
                'detallePedidos.producto',
                'pago',
            ])
            ->find($id);

        if (! $pedido) {
            return $this->notFoundResponse(
                'Pedido no encontrado.'
            );
        }

        return $this->successResponse(
            new PedidoResource($pedido),
            'Detalle del pedido.'
        );
    }

    public function cancelar(Request $request, $id)
    {
        $pedido = Pedido::where(
            'usuario_id',
            $request->user()->id
        )->find($id);

        if (! $pedido) {
            return $this->notFoundResponse(
                'Pedido no encontrado.'
            );
        }

        if ($pedido->estado !== 'pendiente') {
            return $this->errorResponse(
                'Este pedido ya no se puede cancelar.',
                422
            );
        }

        $pedido->update([
            'estado' => 'cancelado',
        ]);

        return $this->successResponse(
            null,
            'Pedido cancelado correctamente.'
        );
    }
}
