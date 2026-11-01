<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Pedido;
use Illuminate\Http\Request;

/**
 * Expone al repartidor móvil sólo las entregas vinculadas a su cuenta.
 */
class RepartidorController extends Controller
{
    use ApiResponse;

    public function pedidos(Request $request)
    {
        $pedidos = Pedido::where(
            'repartidor_id',
            $request->user()->id
        )
            ->with([
                'usuario.cliente',
                'repartidor.repartidor',
                'detallePedidos.producto',
                'pago',
            ])
            ->orderByDesc('updated_at')
            ->get();

        return $this->successResponse(
            PedidoResource::collection($pedidos),
            'Lista de pedidos asignados.'
        );
    }

    public function show(Request $request, $id)
    {
        $pedido = Pedido::where(
            'repartidor_id',
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
                'Pedido no encontrado o no asignado a ti.'
            );
        }

        return $this->successResponse(
            new PedidoResource($pedido),
            'Detalle del pedido.'
        );
    }

    public function entregar(Request $request, $id)
    {
        $pedido = Pedido::where(
            'repartidor_id',
            $request->user()->id
        )->find($id);

        if (! $pedido) {
            return $this->notFoundResponse(
                'Pedido no encontrado o no asignado a ti.'
            );
        }

        if (! $pedido->puedeCambiarEstado('entregado')) {
            return $this->errorResponse(
                'Este pedido no está en reparto.',
                422
            );
        }

        $pedido->update([
            'estado' => 'entregado',
        ]);

        $pedido->load([
            'usuario.cliente',
            'repartidor.repartidor',
            'detallePedidos.producto',
            'pago',
        ]);

        return $this->successResponse(
            new PedidoResource($pedido),
            'Pedido marcado como entregado correctamente.'
        );
    }
}
