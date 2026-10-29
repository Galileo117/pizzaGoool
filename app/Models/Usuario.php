<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

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
}
