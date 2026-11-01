<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relación con el perfil de cliente
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    // Relación con el perfil de repartidor
    public function repartidor()
    {
        return $this->hasOne(Repartidor::class, 'usuario_id');
    }

    // Relación con el perfil de empleado (Cajero o Administrador)
    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'usuario_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    public function pedidosAsignados()
    {
        return $this->hasMany(Pedido::class, 'repartidor_id');
    }

    // Accesor para obtener el nombre completo según el perfil asociado
    public function getNombreCompletoAttribute()
    {
        if ($this->rol === 'cliente' && $this->cliente) {
            return $this->cliente->nombres.' '.$this->cliente->apellidos;
        }

        if ($this->rol === 'repartidor' && $this->repartidor) {
            return $this->repartidor->nombres.' '.$this->repartidor->apellidos;
        }

        if ($this->empleado) {
            return $this->empleado->nombres.' '.$this->empleado->apellidos;
        }

        return 'Usuario sin perfil';
    }

    // Accesor para obtener el teléfono
    public function getTelefonoContactoAttribute()
    {
        if ($this->cliente) {
            return $this->cliente->telefono;
        }
        if ($this->repartidor) {
            return $this->repartidor->telefono;
        }
        if ($this->empleado) {
            return $this->empleado->telefono;
        }

        return null;
    }
}
