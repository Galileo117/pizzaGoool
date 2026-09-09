<?php

namespace App\Livewire;

use App\Models\Producto;
use Livewire\Component;

class CarritoCompras extends Component
{
    public $carrito = [];
    public $total = 0;
    public $cantidad = 1;

    public function mount()
    {
        // Cargar el carrito desde la sesión al iniciar
        $this->carrito = session()->get('carrito', []);
        $this->calcularTotal();
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::findOrFail($productoId);

        // Si el producto ya está en el carrito, aumentar la cantidad
        if (isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
            // Si no está, agregarlo
            $this->carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
            ];
        }

        // Guardar en sesión
        session()->put('carrito', $this->carrito);
        $this->calcularTotal();
    }

    public function quitarDelCarrito($productoId)
    {
        unset($this->carrito[$productoId]);
        session()->put('carrito', $this->carrito);
        $this->calcularTotal();
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        if ($cantidad <= 0) {
            $this->quitarDelCarrito($productoId);
            return;
        }

        $this->carrito[$productoId]['cantidad'] = $cantidad;
        session()->put('carrito', $this->carrito);
        $this->calcularTotal();
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
        // Mostrar solo los productos disponibles
        return view('livewire.carrito-compras', [
            'productos' => Producto::where('disponible', true)->get(), // Usamos get() en lugar de paginate()
        ]);
    }
}