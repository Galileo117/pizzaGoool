<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Mantiene el contrato JSON de pedidos y expone relaciones sólo cuando se cargaron.
 */
class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'codigo' => '#'.str_pad(
                $this->id,
                6,
                '0',
                STR_PAD_LEFT
            ),

            'total' => (float) $this->total,

            'estado' => $this->estado,

            'metodo_pago' => $this->metodo_pago,

            'tipo_entrega' => $this->tipo_entrega,

            'direccion' => $this->direccion,

            'referencia' => $this->referencia,

            'latitud' => $this->latitud !== null
                ? (float) $this->latitud
                : null,

            'longitud' => $this->longitud !== null
                ? (float) $this->longitud
                : null,

            'fecha' => $this->created_at?->format('d/m/Y H:i'),

            'fecha_iso' => $this->created_at?->toIso8601String(),

            'usuario' => $this->whenLoaded('usuario', function () {
                $usuario = $this->usuario;
                $cliente = $usuario?->cliente;

                if (! $usuario) {
                    return null;
                }

                return [
                    'id' => $usuario->id,
                    'nombres' => $cliente?->nombres,
                    'apellidos' => $cliente?->apellidos,
                    'nombre_completo' => trim(
                        ($cliente?->nombres ?? '').' '.
                            ($cliente?->apellidos ?? '')
                    ),
                    'telefono' => $cliente?->telefono,
                    'email' => $usuario->email,
                ];
            }),

            'repartidor' => $this->whenLoaded('repartidor', function () {
                $usuario = $this->repartidor;
                $repartidor = $usuario?->repartidor;

                if (! $usuario || ! $repartidor) {
                    return null;
                }

                return [
                    'id' => $usuario->id,
                    'nombres' => $repartidor->nombres,
                    'apellidos' => $repartidor->apellidos,
                    'nombre_completo' => trim(
                        $repartidor->nombres.' '.
                            ($repartidor->apellidos ?? '')
                    ),
                    'telefono' => $repartidor->telefono,
                ];
            }),

            'detalle' => DetallePedidoResource::collection(
                $this->whenLoaded('detallePedidos')
            ),

            'pago' => $this->whenLoaded('pago', function () {
                if (! $this->pago) {
                    return null;
                }

                return [
                    'id' => $this->pago->id,
                    'monto' => (float) $this->pago->monto,
                    'metodo' => $this->pago->metodo,
                    'estado_pago' => $this->pago->estado_pago,
                    'codigo_qr' => $this->pago->codigo_qr,
                ];
            }),
        ];
    }
}
