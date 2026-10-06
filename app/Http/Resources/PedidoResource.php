<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => '#' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'metodo_pago' => $this->metodo_pago,
            'tipo_entrega' => $this->tipo_entrega,
            'direccion' => $this->direccion,
            'referencia' => $this->referencia,
            'latitud' => $this->latitud ? (float) $this->latitud : null,
            'longitud' => $this->longitud ? (float) $this->longitud : null,
            'fecha' => $this->created_at->format('d/m/Y H:i'),
            'fecha_iso' => $this->created_at->toIso8601String(),
            'detalle' => DetallePedidoResource::collection($this->whenLoaded('detallePedidos')),
            'repartidor' => $this->repartidor ? [
                'id' => $this->repartidor->id,
                'nombre' => $this->repartidor->nombre,
                'telefono' => $this->repartidor->telefono,
            ] : null,
        ];
    }
}
