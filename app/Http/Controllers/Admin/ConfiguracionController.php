<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $imagenQR = Configuracion::obtener('imagen_qr_estatico');
        $mensajeQR = Configuracion::obtener('mensaje_qr');
        $costoEnvio = Configuracion::obtener('costo_envio', '10.00');

        return view('admin.configuracion', compact('imagenQR', 'mensajeQR', 'costoEnvio'));
    }

    public function guardarQR(Request $request)
    {
        $request->validate([
            'imagen_qr' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'mensaje_qr' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('imagen_qr')) {
            $imagenAnterior = Configuracion::obtener('imagen_qr_estatico');
            if ($imagenAnterior && Storage::disk('public')->exists($imagenAnterior)) {
                Storage::disk('public')->delete($imagenAnterior);
            }

            $path = $request->file('imagen_qr')->store('qr', 'public');
            Configuracion::guardar('imagen_qr_estatico', $path);
        }

        if ($request->filled('mensaje_qr')) {
            Configuracion::guardar('mensaje_qr', $request->mensaje_qr);
        }

        return redirect()->back()->with('success', 'Configuración del QR actualizada correctamente.');
    }

    public function guardarCostoEnvio(Request $request)
    {
        $validated = $request->validate([
            'costo_envio' => 'required|numeric|decimal:0,2|min:0|max:9999.99',
        ], [
            'costo_envio.required' => 'El monto es obligatorio.',
            'costo_envio.numeric' => 'Debe ingresar un número válido.',
            'costo_envio.decimal' => 'El monto puede tener como máximo dos decimales.',
            'costo_envio.min' => 'El monto no puede ser negativo.',
            'costo_envio.max' => 'El monto máximo permitido es 9999.99.',
        ]);

        $valor = number_format((float) $validated['costo_envio'], 2, '.', '');
        Configuracion::guardar('costo_envio', $valor);

        return redirect()->back()->with('success', 'Costo de envío actualizado correctamente.');
    }
}
