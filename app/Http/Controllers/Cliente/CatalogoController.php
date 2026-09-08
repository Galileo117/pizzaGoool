<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('disponible', true)->with('categoria')->get();
        return view('cliente.catalogo', compact('productos'));
    }
}
