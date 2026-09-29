<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        // Si el usuario está logueado y NO es cliente, redirigir a su panel
        if (auth()->check()) {
            $rol = auth()->user()->rol;

            if ($rol === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($rol === 'cajero') {
                return redirect()->route('cajero.dashboard');
            }

            if ($rol === 'repartidor') {
                return redirect()->route('repartidor.dashboard');
            }
            // Si es cliente, se le muestra la página de inicio normal
        }

        // Obtener los productos destacados
        $productos = Producto::where('disponible', true)->latest()->take(4)->get();

        return view('welcome', compact('productos'));
    }
}