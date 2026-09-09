<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        // Obtener solo los primeros 4 productos disponibles para destacar
        $productos = Producto::where('disponible', true)->latest()->take(4)->get();

        return view('welcome', compact('productos'));
    }
}