<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ContadorCarrito extends Component
{
    public $cantidad = 0;

    public function mount()
    {
        $this->actualizarContador();
    }

    #[On('carritoActualizado')]
    public function actualizarContador()
    {
        $carrito = session()->get('carrito', []);
        $this->cantidad = array_sum(array_column($carrito, 'cantidad'));
    }

    public function render()
    {
        return view('livewire.contador-carrito');
    }
}
