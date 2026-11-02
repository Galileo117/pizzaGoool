<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro histórico de una compra, incluyendo su entrega y estado operativo.
 */
class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $casts = [
        'total'       => 'decimal:2',
        'costo_envio' => 'decimal:2',
    ];
    protected $fillable = [
        'usuario_id',
        'repartidor_id',
        'total',
        'costo_envio',
        'estado',
        'metodo_pago',
        'tipo_entrega',
        'direccion',
        'referencia',
        'latitud',
        'longitud',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function repartidor()
    {
        return $this->belongsTo(Usuario::class, 'repartidor_id');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    /**
     * Comprueba la máquina de estados compartida por caja y repartidores.
     */
    public function puedeCambiarEstado(string $nuevoEstado): bool
    {
        $transiciones = [
            'pendiente' => ['preparacion', 'cancelado'],
            'preparacion' => ['listo', 'cancelado'],
            'listo' => $this->tipo_entrega === 'recoger_local'
                ? ['entregado']
                : ['en_reparto'],
            'en_reparto' => ['entregado', 'cancelado'],
            'entregado' => [],
            'cancelado' => [],
        ];

        return in_array($nuevoEstado, $transiciones[$this->estado] ?? [], true);
    }
}
