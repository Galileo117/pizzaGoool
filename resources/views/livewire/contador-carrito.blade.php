{{-- Indicador Livewire del número de unidades guardadas en el carrito de sesión. --}}
<div class="flex items-center gap-2 bg-white/10 text-white px-3 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold backdrop-blur-sm text-xs sm:text-base max-w-full overflow-hidden">
    <i class="fa-solid fa-cart-shopping text-goool-yellow" aria-hidden="true"></i>
    <span class="truncate">Carrito: <span class="text-goool-yellow font-extrabold">{{ $cantidad }}</span></span>
