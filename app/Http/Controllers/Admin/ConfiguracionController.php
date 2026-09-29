<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $imagenQR = Configuracion::obtener('imagen_qr_estatico');
        $mensajeQR = Configuracion::obtener('mensaje_qr');

        return view('admin.configuracion', compact('imagenQR', 'mensajeQR'));
    }

    public function guardarQR(Request $request)
    {
        $request->validate([
            'imagen_qr' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'mensaje_qr' => 'nullable|string|max:500',
        ]);

        // Guardar la imagen (si se subió)
        if ($request->hasFile('imagen_qr')) {
            // Eliminar la imagen anterior si existe
            $imagenAnterior = Configuracion::obtener('imagen_qr_estatico');
            if ($imagenAnterior && \Storage::disk('public')->exists($imagenAnterior)) {
                \Storage::disk('public')->delete($imagenAnterior);
            }

            $path = $request->file('imagen_qr')->store('qr', 'public');
            Configuracion::guardar('imagen_qr_estatico', $path);
        }

        // Guardar el mensaje (si se envió)
        if ($request->filled('mensaje_qr')) {
            Configuracion::guardar('mensaje_qr', $request->mensaje_qr);
        }

        return redirect()->back()->with('success', 'Configuración del QR actualizada correctamente.');
    }
}