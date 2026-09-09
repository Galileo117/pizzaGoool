<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function welcome()
    {
        $productos = Producto::where('disponible', true)
            ->with('categoria')
            ->limit(4)
            ->get();
            
        $carrito = session()->get('carrito', []);
        $cantidadItems = $this->contarItems($carrito);

        return view('welcome', compact('productos', 'carrito', 'cantidadItems'));
    }

    public function index()
    {
        $productos = Producto::where('disponible', true)
            ->with('categoria')
            ->paginate(8);
            
        $carrito = session()->get('carrito', []);
        $total = $this->calcularTotal($carrito);
        $cantidadItems = $this->contarItems($carrito);

        return view('cliente.catalogo', compact('productos', 'carrito', 'total', 'cantidadItems'));
    }

    public function agregarAlCarrito(Request $request, $productoId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para agregar productos al carrito.',
                'redirect' => route('login')
            ], 401);
        }

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

        $total = $this->calcularTotal($carrito);
        $cantidadItems = $this->contarItems($carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito' => $carrito,
            'total' => $total,
            'cantidad_items' => $cantidadItems
        ]);
    }

    public function guardarCarrito(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para continuar.',
                'redirect' => route('login')
            ], 401);
        }

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
        $total = $this->calcularTotal($carritoSesion);
        $cantidadItems = $this->contarItems($carritoSesion);

        return response()->json([
            'success' => true,
            'carrito' => $carritoSesion,
            'total' => $total,
            'cantidad_items' => $cantidadItems
        ]);
    }

    public function vaciarCarrito()
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para vaciar el carrito.'
            ], 401);
        }

        session()->forget('carrito');
        return response()->json(['success' => true]);
    }

    private function calcularTotal($carrito)
    {
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }

    private function contarItems($carrito)
    {
        $cantidad = 0;
        foreach ($carrito as $item) {
            $cantidad += $item['cantidad'];
        }
        return $cantidad;
    }
}