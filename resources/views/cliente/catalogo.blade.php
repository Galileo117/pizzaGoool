@extends('layouts.cliente')

@section('title', 'Catálogo - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8 lg:py-10">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-goool-green flex items-center gap-3">
                    <i class="fa-solid fa-utensils text-goool-yellow text-2xl sm:text-3xl"></i>
                    Nuestro Menú
                </h1>
                <p class="text-gray-500 text-sm sm:text-base mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-goool-orange"></i>
                    Pide tu pizza favorita y recíbela en casa
                </p>
            </div>

            <a href="#carrito" id="cart-button"
                class="inline-flex items-center gap-2 bg-goool-yellow text-goool-green-dark px-4 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg hover:bg-yellow-400 transition-all duration-200 self-start sm:self-center">
                <i class="fa-solid fa-cart-shopping text-base"></i>
                <span>Carrito</span>
                <span id="cart-badge"
                    class="bg-goool-green text-white text-xs px-2.5 py-0.5 rounded-full font-bold min-w-[1.5rem] text-center">
                    {{ $cantidadItems ?? 0 }}
                </span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @forelse ($productos as $producto)
                <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-goool-yellow group" data-producto-id="{{ $producto->id }}">
                    <div class="relative h-44 sm:h-48 bg-gradient-to-br from-goool-green-light/20 to-goool-green-light/5 flex items-center justify-center overflow-hidden">
                        @if ($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy">
                        @else
                            <i class="fa-solid fa-pizza-slice text-6xl sm:text-7xl text-goool-green/20 group-hover:scale-110 transition-transform duration-300"></i>
                        @endif

                        @if ($producto->disponible)
                            <span class="absolute top-3 right-3 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Disponible
                            </span>
                        @else
                            <span class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Agotado
                            </span>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 line-clamp-1">{{ $producto->nombre }}</h3>
                        <p class="text-gray-500 text-sm mt-1.5 line-clamp-2 min-h-[2.5rem]">{{ $producto->descripcion }}</p>

                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                            <span class="text-xl sm:text-2xl font-extrabold text-goool-green">
                                Bs. {{ number_format($producto->precio, 2) }}
                            </span>

                            @if ($producto->disponible)
                                <div class="add-control" data-producto-id="{{ $producto->id }}">
                                    @if(isset($carrito[$producto->id]))
                                        <div class="qty-control flex items-center gap-2 bg-goool-green rounded-xl px-2 py-1">
                                            <button onclick="decrementar({{ $producto->id }})" class="w-7 h-7 bg-white/20 rounded-lg text-white hover:bg-white/30 transition flex items-center justify-center">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>
                                            <span class="text-white font-bold text-sm min-w-[2rem] text-center" id="cantidad-{{ $producto->id }}">
                                                {{ $carrito[$producto->id]['cantidad'] }}
                                            </span>
                                            <button onclick="incrementar({{ $producto->id }})" class="w-7 h-7 bg-white/20 rounded-lg text-white hover:bg-white/30 transition flex items-center justify-center">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    @else
                                        <button onclick="agregarAlCarrito({{ $producto->id }})"
                                            class="inline-flex items-center gap-2 bg-goool-orange hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            <span class="hidden sm:inline">Agregar</span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-sm font-medium flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> No disponible
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="fa-solid fa-plate-wheat text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-400 text-lg font-medium">No hay productos disponibles</p>
                    <p class="text-gray-300 text-sm mt-1">Vuelve más tarde para ver nuestras novedades.</p>
                </div>
            @endforelse
        </div>

        @if (method_exists($productos, 'links'))
            <div class="mt-8 sm:mt-10">
                {{ $productos->links() }}
            </div>
        @endif

        {{-- Carrito --}}
        <div id="carrito" class="mt-12">
            @if (isset($carrito) && count($carrito) > 0)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-goool-green px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 class="text-white font-bold text-lg flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Tu Carrito
                            <span class="bg-goool-yellow text-goool-green-dark text-xs px-2.5 py-0.5 rounded-full font-bold" id="cart-items-count">
                                {{ $cantidadItems ?? 0 }} items
                            </span>
                        </h2>

                        <button onclick="vaciarCarrito()"
                            class="text-white/70 hover:text-white text-sm transition-colors duration-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i> Vaciar
                        </button>
                    </div>

                    <div class="divide-y divide-gray-100" id="cart-items">
                        @foreach ($carrito as $productoId => $item)
                            <div class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50" id="cart-item-{{ $productoId }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-goool-green/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-pizza-slice text-goool-green"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $item['nombre'] }}</h4>
                                        <p class="text-sm text-gray-500">Bs. {{ number_format($item['precio'], 2) }} c/u</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 self-end sm:self-center">
                                    <span class="text-sm font-bold text-goool-green" id="subtotal-{{ $productoId }}">
                                        Bs. {{ number_format($item['precio'] * $item['cantidad'], 2) }}
                                    </span>
                                    <span class="text-sm text-gray-500" id="cant-{{ $productoId }}">x{{ $item['cantidad'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-600 text-sm">Total</span>
                            <span class="text-2xl font-extrabold text-goool-green" id="cart-total">
                                Bs. {{ number_format($total ?? 0, 2) }}
                            </span>
                        </div>

                        <a href="{{ route('cliente.checkout') }}"
                            class="inline-flex items-center gap-3 bg-goool-green hover:bg-goool-green-dark text-white px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200 w-full sm:w-auto justify-center">
                            <i class="fa-solid fa-credit-card"></i>
                            Proceder al Pago
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-200 rounded-2xl">
                    <i class="fa-solid fa-cart-shopping text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm font-medium">Tu carrito está vacío</p>
                    <p class="text-xs text-gray-300 mt-0.5">¡Agrega tus pizzas favoritas!</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const MAX_CANTIDAD = 20;

        function agregarAlCarrito(productoId) {
            fetch(`/catalogo/agregar/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(err => console.error('Error:', err));
        }

        function vaciarCarrito() {
            if (!confirm('¿Estás seguro de vaciar el carrito?')) return;

            fetch('/catalogo/vaciar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(err => console.error('Error:', err));
        }

        function incrementar(productoId) {
            const cantActual = parseInt(document.getElementById(`cantidad-${productoId}`).textContent.trim());

            if (cantActual >= MAX_CANTIDAD) {
                mostrarToast(`Máximo ${MAX_CANTIDAD} unidades por producto.`, 'warning');
                return;
            }

            fetch('/catalogo/guardar_carrito', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    producto_id: productoId,
                    cantidad: cantActual + 1
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(err => console.error('Error:', err));
        }

        function decrementar(productoId) {
            const cantActual = parseInt(document.getElementById(`cantidad-${productoId}`).textContent.trim());

            fetch('/catalogo/guardar_carrito', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    producto_id: productoId,
                    cantidad: cantActual - 1
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
    @endpush
@endsection