<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Producto del catálogo; sus detalles históricos permanecen ligados a pedidos.
 */
class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
        'disponible',
    ];

    protected $casts = [
        'stock'      => 'integer',
        'disponible' => 'boolean',
        'precio'     => 'decimal:2',
    ];

    /**
     * Relación: Un producto pertenece a una categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación: Un producto puede estar en muchos detalles de pedidos
     */
    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }

    /**
     * Verifica si hay stock suficiente para la cantidad solicitada.
     */
    public function tieneStock(int $cantidad = 1): bool
    {
        return $this->disponible && $this->stock >= $cantidad;
    }

    /**
     * Stock realmente vendible: 0 si el producto está agotado o no disponible.
     */
    public function getStockDisponibleAttribute(): int
    {
        return $this->disponible ? (int) $this->stock : 0;
    }
}
