<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PedidoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Pedido;
use Illuminate\Http\Request;

class RepartidorController extends Controller
{
    use ApiResponse;

    /**
     * Listar los pedidos asignados al repartidor autenticado.
     * Solo muestra pedidos en_reparto o entregados recientemente.
     */
    public function pedidos(Request $request)
    {
        $repartidorId = $request->user()->id;

        $pedidos = Pedido::where('repartidor_id', $repartidorId)
            ->with(['detallePedidos.producto', 'usuario'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->successResponse(
            PedidoResource::collection($pedidos),
            'Lista de pedidos asignados.'
        );
    }

    /**
     * Ver el detalle de un pedido asignado.
     */
    public function show(Request $request, $id)
    {
        $pedido = Pedido::with(['detallePedidos.producto', 'usuario', 'pago'])
            ->where('repartidor_id', $request->user()->id)
            ->find($id);

        if (!$pedido) {
            return $this->notFoundResponse('Pedido no encontrado o no asignado a ti.');
        }

        return $this->successResponse(
            new PedidoResource($pedido),
            'Detalle del pedido.'
        );
    }

    /**
     * Marcar un pedido como entregado.
     */
    public function entregar(Request $request, $id)
    {
        $pedido = Pedido::where('repartidor_id', $request->user()->id)->find($id);

        if (!$pedido) {
            return $this->notFoundResponse('Pedido no encontrado o no asignado a ti.');
        }

        if ($pedido->estado !== 'en_reparto') {
            return $this->errorResponse('Este pedido no está en reparto.', 422);
        }

        $pedido->update(['estado' => 'entregado']);

        return $this->successResponse(
            new PedidoResource($pedido->load('detallePedidos.producto')),
            'Pedido marcado como entregado correctamente.'
        );
    }
}
