<?php

namespace App\Livewire;

use App\Models\Producto;
use Livewire\Component;

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
            $this->carrito[$productoId]['cantidad']++;
        } else {
            $this->carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
            ];
        }

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
        return view('livewire.carrito-compras', [
            'productos' => Producto::where('disponible', true)->paginate(8),
        ]);
    }
}