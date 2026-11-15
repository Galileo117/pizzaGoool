<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Component;

/**
 * Mantiene el carrito de sesión reactivo mientras el usuario ajusta productos.
 * El límite por producto es el stock real (no 20 fijo).
 */
class CarritoCompras extends Component
{
    public $carrito = [];
    public $total = 0;
    public $cantidadItems = 0;
    public $contexto = 'catalogo';

    public function mount($contexto = 'catalogo', $carrito = null, $total = 0, $cantidadItems = 0)
    {
        $this->contexto      = $contexto;
        $this->carrito       = $carrito ?? session()->get('carrito', []);
        $this->total         = $total;
        $this->cantidadItems = $cantidadItems;

        if (empty($this->total)) {
            $this->calcularTotal();
        }

        if (empty($this->cantidadItems)) {
            $this->cantidadItems = array_sum(array_column($this->carrito, 'cantidad'));
        }
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::findOrFail($productoId);

        if (! $producto->disponible || $producto->stock <= 0) {
            $this->dispatch('stockAlcanzado', mensaje: "Este producto está agotado.");
            return;
        }

        $cantidadActual = $this->carrito[$productoId]['cantidad'] ?? 0;

        if ($cantidadActual >= $producto->stock) {
            $this->dispatch('stockAlcanzado', mensaje: "Solo hay {$producto->stock} unidades disponibles.");
            return;
        }

        if ($cantidadActual > 0) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
            $this->carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre'      => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio'      => $producto->precio,
                'cantidad'    => 1,
                'imagen'      => $producto->imagen,
            ];
        }

        $this->guardarCarrito();
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        $producto = Producto::find($productoId);

        if (! $producto) {
            return;
        }

        if ($cantidad <= 0) {
            $this->quitarDelCarrito($productoId);
            return;
        }

        if ($cantidad > $producto->stock) {
            $this->dispatch('stockAlcanzado', mensaje: "Solo hay {$producto->stock} unidades disponibles.");
            return;
        }

        if (isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad'] = $cantidad;
            $this->guardarCarrito();
        }
    }

    public function quitarDelCarrito($productoId)
    {
        unset($this->carrito[$productoId]);
        $this->guardarCarrito();
    }

    public function vaciarCarrito()
    {
        $this->carrito = [];
        $this->guardarCarrito();
    }

    private function guardarCarrito()
    {
        session()->put('carrito', $this->carrito);
        $this->calcularTotal();
        $this->cantidadItems = array_sum(array_column($this->carrito, 'cantidad'));
        $this->dispatch('carritoActualizado');
    }

    public function calcularTotal()
    {
        $this->total = 0;
        foreach ($this->carrito as $item) {
            $this->total += $item['precio'] * $item['cantidad'];
        }
    }

    public function render()
    {
        // Contexto "inicio": solo productos destacados (4), sin filtros ni paginación
        if ($this->contexto === 'inicio') {
            $productos = Producto::where('disponible', true)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();

            return view('livewire.carrito-compras', [
                'productos'  => $productos,
                'categorias' => collect(),
            ]);
        }

        // Contexto "catalogo": comportamiento actual (filtros + paginación)
        $query = Producto::where('disponible', true)->with('categoria');

        if (request()->filled('categoria')) {
            $query->where('categoria_id', request('categoria'));
        }

        if (request()->filled('buscar')) {
            $busqueda = request('buscar');
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        switch (request('orden', 'recientes')) {
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

        return view('livewire.carrito-compras', [
            'productos'  => $query->paginate(12),
            'categorias' => Categoria::all(),
        ]);
    }
}
