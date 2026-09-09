<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('disponible', true)
            ->with('categoria')
            ->paginate(8);
            
        $carrito = session('carrito', []);
        $total = $this->calcularTotal($carrito);

        return view('cliente.catalogo', compact('productos', 'carrito', 'total'));
    }

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
                'descripcion' => $producto->descripcion,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
            ];
        }

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito' => $carrito,
            'total' => $this->calcularTotal($carrito),
            'cantidad_items' => count($carrito)
        ]);
    }

    public function guardarCarrito(Request $request)
    {
        $carrito = $request->input('carrito', []);
        
        $carritoSesion = [];
        foreach ($carrito as $item) {
            $carritoSesion[$item['id']] = [
                'producto_id' => $item['id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                'cantidad' => $item['cantidad'],
                'imagen' => $item['imagen'] ?? null,
            ];
        }
        
        session()->put('carrito', $carritoSesion);

        return response()->json([
            'success' => true,
            'carrito' => $carritoSesion
        ]);
    }

    private function calcularTotal($carrito)
    {
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }
}