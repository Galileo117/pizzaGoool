{{-- Indicador Livewire del número de unidades guardadas en el carrito de sesión. --}}
<div class="inline-flex items-center gap-3 bg-white/10 text-white px-6 py-3 rounded-xl font-semibold backdrop-blur-sm">
    <i class="fa-solid fa-cart-shopping text-goool-yellow" aria-hidden="true"></i>
    <span>Tu carrito: <span class="text-goool-yellow font-extrabold">{{ $cantidad }}</span>
        {{ $cantidad === 1 ? 'producto' : 'productos' }}</span>
    <a href="{{ route('cliente.catalogo') }}#carrito"
        class="ml-2 bg-goool-yellow text-goool-green-dark px-3 py-1 rounded-lg text-sm font-bold hover:bg-yellow-400 transition-colors duration-200">
        Ver
    </a>
</div>
