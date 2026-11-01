<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Datos operativos del repartidor asociados a su cuenta de usuario.
 */
class Repartidor extends Model
{
    use HasFactory;

    protected $table = 'repartidores';

    protected $fillable = [
        'usuario_id',
        'nombres',
        'apellidos',
        'telefono',
        'ci',
        'vehiculo',
        'placa',
        'licencia_conducir',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
