<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    use ApiResponse;

    /**
     * Devuelve la configuración pública del sistema.
     * Incluye la imagen del QR estático y el mensaje personalizado.
     */
    public function publica()
    {
        $imagenQr = Configuracion::obtener('imagen_qr_estatico');
        $mensajeQr = Configuracion::obtener('mensaje_qr', 'Escanea este código QR desde tu aplicación bancaria para realizar el pago.');

        return $this->successResponse([
            'imagen_qr' => $imagenQr ? asset('storage/' . $imagenQr) : null,
            'mensaje_qr' => $mensajeQr,
        ], 'Configuración pública.');
    }
}