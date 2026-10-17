<?php

namespace App\Livewire;

use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\On;

class CarritoCompras extends Component
{
    public $carrito = [];
    public $total = 0;

    public function mount()
    {
        $this->carrito = session()->get('carrito', []);
        $this->calcularTotal();
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::findOrFail($productoId);

        if (isset($this->carrito[$productoId])) {
            if ($this->carrito[$productoId]['cantidad'] < 20) {
                $this->carrito[$productoId]['cantidad']++;
            }
        } else {
            $this->carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
            ];
        }

        $this->guardarCarrito();
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        if ($cantidad <= 0) {
            $this->quitarDelCarrito($productoId);
            return;
        }

        if ($cantidad > 20) {
            $cantidad = 20;
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
        return view('livewire.carrito-compras', [
            'productos' => Producto::where('disponible', true)->paginate(8),
        ]);
    }
}
