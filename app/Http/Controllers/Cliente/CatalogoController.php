<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function mostrarCheckout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('cliente.catalogo')
                ->with('error', 'Tu carrito está vacío.');
        }

        $idsProductos = array_column($carrito, 'producto_id');
        $productos = Producto::whereIn('id', $idsProductos)
            ->get()
            ->keyBy('id');

        foreach ($carrito as $clave => $item) {
            $producto = $productos->get((int) ($item['producto_id'] ?? $clave));

            if (! $producto || ! $producto->disponible) {
                return redirect()->route('cliente.catalogo')
                    ->with('error', 'El carrito contiene productos que ya no están disponibles.');
            }

            $carrito[$clave]['nombre'] = $producto->nombre;
            $carrito[$clave]['precio'] = $producto->precio;
        }

        session()->put('carrito', $carrito);

        return view('cliente.checkout');
    }

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

    public function index(Request $request)
    {
        $query = Producto::where('disponible', true)->with('categoria');

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        // Búsqueda por nombre o descripción
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        // Ordenamiento
        $orden = $request->input('orden', 'recientes');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'nombre':
                $query->orderBy('nombre', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $productos = $query->paginate(12);

        $categorias = Categoria::all();
        $carrito = session()->get('carrito', []);
        $total = $this->calcularTotal($carrito);
        $cantidadItems = $this->contarItems($carrito);

        return view('cliente.catalogo', compact('productos', 'categorias', 'carrito', 'total', 'cantidadItems'));
    }

    public function agregarAlCarrito(Request $request, $productoId)
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para agregar productos.',
                'redirect' => route('login'),
            ], 401);
        }

        $producto = Producto::findOrFail($productoId);

        if (! $producto->disponible) {
            return response()->json([
                'success' => false,
                'message' => 'Este producto no está disponible.',
            ], 422);
        }

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] = min($carrito[$productoId]['cantidad'] + 1, 20);
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
            'total' => $this->calcularTotal($carrito),
            'cantidad_items' => $this->contarItems($carrito),
        ]);
    }

    public function guardarCarrito(Request $request)
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
                'redirect' => route('login'),
            ], 401);
        }

        $productoId = $request->input('producto_id');
        $cantidad = (int) $request->input('cantidad', 0);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$productoId])) {
            if ($cantidad <= 0) {
                unset($carrito[$productoId]);
            } else {
                $carrito[$productoId]['cantidad'] = min($cantidad, 20);
            }
        }

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'total' => $this->calcularTotal($carrito),
            'cantidad_items' => $this->contarItems($carrito),
        ]);
    }

    public function vaciarCarrito()
    {
        if (! auth()->check()) {
            return response()->json(['success' => false], 401);
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
