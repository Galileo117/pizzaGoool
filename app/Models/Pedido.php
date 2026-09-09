<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'usuario_id',
        'total',
        'estado',
        'metodo_pago',
        'tipo_entrega',
        'direccion',
        'referencia',
    ];

    // Relación: Un pedido pertenece a un usuario (cliente)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación: Un pedido tiene muchos detalles
    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    // Relación: Un pedido tiene un pago asociado
    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }
}