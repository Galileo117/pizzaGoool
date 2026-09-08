<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
    ];

    // Relación: Un detalle pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación: Un detalle pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
