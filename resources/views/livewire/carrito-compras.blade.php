<div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">

    @if ($contexto === 'catalogo')
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-book-open text-goool-yellow" aria-hidden="true"></i>
                Nuestro Catálogo
            </h1>

            <a href="#carrito"
                class="inline-flex items-center gap-2 bg-goool-yellow text-goool-green-dark px-5 py-2.5 rounded-xl font-bold hover:bg-yellow-400 transition-all duration-200 shadow-sm hover:shadow-md w-full sm:w-auto justify-center focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2"
                aria-label="Ir al carrito de compras">
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                Ir al Carrito
                <span
                    class="bg-goool-green text-white text-xs px-2.5 py-0.5 rounded-full font-bold min-w-[1.5rem] text-center">
                    {{ count($this->carrito) }}
                </span>
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-12">
        @foreach ($productos as $producto)
            <div
                class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-goool-yellow group">
                <div
                    class="relative h-44 sm:h-48 bg-gradient-to-br from-goool-green-light/20 to-goool-green-light/5 flex items-center justify-center overflow-hidden">
                    @if ($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy" decoding="async">
                    @else
                        <i class="fa-solid fa-pizza-slice text-6xl sm:text-7xl text-goool-green/20 group-hover:scale-110 transition-transform duration-300"
                            aria-hidden="true"></i>
                    @endif

                    @if ($producto->disponible)
                        <span
                            class="absolute top-3 right-3 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Disponible
                        </span>
                    @else
                        <span
                            class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                            Agotado
                        </span>
                    @endif
                </div>

                <div class="p-4 sm:p-5">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 line-clamp-1">
                        {{ $producto->nombre }}
                    </h3>
                    <p class="text-gray-500 text-sm mt-1.5 line-clamp-2 min-h-[2.5rem]">
                        {{ $producto->descripcion }}
                    </p>

                    <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100">
                        <span class="text-xl sm:text-2xl font-extrabold text-goool-green">
                            Bs. {{ number_format($producto->precio, 2) }}
                        </span>

                        @if ($producto->disponible)
                            <button wire:click="agregarAlCarrito({{ $producto->id }})"
                                class="inline-flex items-center gap-2 bg-goool-orange hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md active:scale-95 focus:outline-none focus:ring-2 focus:ring-goool-orange focus:ring-offset-2"
                                aria-label="Agregar {{ $producto->nombre }} al carrito">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                                <span class="hidden sm:inline">Agregar</span>
                            </button>
                        @else
                            <span class="text-gray-400 text-sm font-medium flex items-center gap-1">
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                No disponible
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($contexto === 'catalogo')
        @if (count($this->carrito) > 0)
            <div id="carrito"
                class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden scroll-mt-20">
                <div
                    class="bg-goool-green px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                        Tu Carrito
                        <span
                            class="bg-goool-yellow text-goool-green-dark text-xs px-2.5 py-0.5 rounded-full font-bold">
                            {{ count($this->carrito) }} items
                        </span>
                    </h2>

                    <button wire:click="vaciarCarrito"
                        class="text-white/70 hover:text-white text-sm transition-colors duration-200 flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2 focus:ring-offset-goool-green rounded-lg px-2 py-1"
                        onclick="return confirm('¿Estás seguro de vaciar el carrito completamente?')"
                        aria-label="Vaciar carrito">
                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                        Vaciar
                    </button>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($this->carrito as $productoId => $item)
                        <div
                            class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition-colors duration-150">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 bg-goool-green/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-pizza-slice text-goool-green" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $item['nombre'] }}</h4>
                                    <p class="text-sm text-gray-500">Bs. {{ number_format($item['precio'], 2) }} c/u
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 sm:gap-4 self-end sm:self-center">
                                <div
                                    class="flex items-center gap-1 bg-gray-100 rounded-xl overflow-hidden shadow-inner">
                                    <button
                                        wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] - 1 }})"
                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 transition-colors duration-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-inset"
                                        aria-label="Disminuir cantidad" title="Quitar uno">
                                        <i class="fa-solid fa-minus" aria-hidden="true"></i>
                                    </button>

                                    <span class="w-8 text-center font-bold text-gray-800 text-sm"
                                        aria-label="Cantidad: {{ $item['cantidad'] }}">
                                        {{ $item['cantidad'] }}
                                    </span>

                                    <button
                                        wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] + 1 }})"
                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 transition-colors duration-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-inset"
                                        aria-label="Aumentar cantidad" title="Agregar uno">
                                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <span class="text-sm font-bold text-goool-green min-w-[5rem] text-right">
                                    Bs. {{ number_format($item['precio'] * $item['cantidad'], 2) }}
                                </span>

                                <button wire:click="quitarDelCarrito({{ $productoId }})"
                                    class="text-red-400 hover:text-red-600 transition-colors duration-200 text-sm p-1 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
                                    aria-label="Eliminar {{ $item['nombre'] }} del carrito"
                                    title="Eliminar del carrito">
                                    <i class="fa-solid fa-xmark text-xl" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div
                    class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600 text-sm">Total</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                            Bs. {{ number_format($this->total, 2) }}
                        </span>
                    </div>

                    <a href="{{ route('cliente.checkout') }}"
                        class="inline-flex items-center gap-3 bg-goool-green hover:bg-goool-green-dark text-white px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full sm:w-auto justify-center focus:outline-none focus:ring-2 focus:ring-goool-green focus:ring-offset-2"
                        aria-label="Proceder al pago">
                        <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                        Proceder al Pago
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        @else
            <div id="carrito"
                class="mt-8 text-center py-10 text-gray-400 border-2 border-dashed border-gray-200 rounded-2xl scroll-mt-20">
                <i class="fa-solid fa-cart-shopping text-5xl mb-4 opacity-30" aria-hidden="true"></i>
                <p class="text-lg font-medium">Tu carrito está vacío</p>
                <p class="text-sm text-gray-300 mt-1">¡Agrega tus pizzas favoritas desde el catálogo!</p>
            </div>
        @endif
    @endif

</div>
