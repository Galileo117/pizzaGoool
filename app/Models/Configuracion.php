<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Configuración clave-valor utilizada por las pantallas administrativas y la API.
 */
class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
    ];

    public static function obtener(string $clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();

        return $config ? $config->valor : $default;
    }

    public static function guardar(string $clave, $valor): self
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor]
        );
    }
}
