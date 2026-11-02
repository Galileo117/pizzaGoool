<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Agrupa los productos visibles en el catálogo.
 */
class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación: Una categoría tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
