<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Estado de pago asociado a un único pedido.
 */
class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'pedido_id',
        'monto',
        'metodo',
        'estado_pago',
        'codigo_qr',
    ];

    // Relación: Un pago pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
