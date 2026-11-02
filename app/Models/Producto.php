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
        'imagen',
        'disponible',
    ];

    // Relación: Un producto pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relación: Un producto puede estar en muchos detalles de pedidos
    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
