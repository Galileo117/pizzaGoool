<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetallePedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'producto' => $this->producto ? $this->producto->nombre : 'Producto eliminado',
            'cantidad' => $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,
            'subtotal' => (float) ($this->precio_unitario * $this->cantidad),
        ];
    }
}
