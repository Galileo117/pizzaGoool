<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('disponible', true)->get();
        return view('cliente.catalogo', compact('productos'));
    }

    // Agregar al carrito (solo guarda en sesión y devuelve JSON)
    public function agregarAlCarrito(Request $request, $productoId)
    {
        $producto = Producto::findOrFail($productoId);
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad']++;
        } else {
            $carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
            ];
        }

        session()->put('carrito', $carrito);

        return response()->json(['success' => true]);
    }

    // Guardar todo el carrito desde JavaScript antes de ir al Checkout
    public function guardarCarrito(Request $request)
    {
        $carrito = $request->input('carrito', []);
        
        // Convertir el carrito de JS al formato de sesión
        $carritoSesion = [];
        foreach ($carrito as $item) {
            $carritoSesion[$item['id']] = [
                'producto_id' => $item['id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                'cantidad' => $item['cantidad'],
            ];
        }
        
        session()->put('carrito', $carritoSesion);

        return response()->json(['success' => true]);
    }
}