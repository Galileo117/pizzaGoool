<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Línea de un pedido que conserva el precio unitario confirmado al comprar.
 */
class DetallePedido extends Model
{
    use HasFactory;

    protected $table = "detalle_pedidos";

    protected $fillable = [
        "pedido_id",
        "producto_id",
        "producto_nombre",
        "categoria_nombre",
        "cantidad",
        "precio_unitario",
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

    /**
     * Devuelve el nombre histórico del producto; si no existe (pedidos viejos),
     * recurre al nombre actual del producto o un texto por defecto.
     */
    public function getNombreProductoAttribute(): string
    {
        return $this->producto_nombre ??
            ($this->producto?->nombre ?? "Producto no disponible");
    }

    public function getNombreCategoriaAttribute(): string
    {
        return $this->categoria_nombre ??
            ($this->producto?->categoria?->nombre ?? "Sin categoría");
    }
}
