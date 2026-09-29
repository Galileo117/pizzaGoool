@extends('layouts.cliente')

@section('title', 'Inicio - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 sm:py-12">

        <div class="text-center mb-12">
            <div class="inline-block bg-goool-yellow/20 rounded-full px-6 py-2 mb-4">
                <span class="text-goool-green-dark font-semibold text-sm">
                    <i class="fa-solid fa-futbol mr-2"></i>
                    ¡Un golazo de sabor!
                </span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-goool-green">
                Pizza <span class="text-goool-yellow">Goool</span>
            </h1>

            <p class="text-gray-600 text-lg sm:text-xl mt-4 max-w-2xl mx-auto">
                La mejor pizza con pasión por el fútbol. Pide tu favorita y recíbela en casa.
            </p>

            @auth
                @if (auth()->user()->rol === 'cliente')
                    <a href="{{ route('cliente.catalogo') }}"
                        class="mt-6 inline-flex items-center gap-3 bg-goool-yellow text-goool-green-dark font-bold py-3 px-8 rounded-xl hover:bg-yellow-400 transition-all duration-200 shadow-md hover:shadow-lg text-lg">
                        <i class="fa-solid fa-book-open"></i>
                        Ver Catálogo Completo
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    <div
                        class="mt-6 inline-flex items-center gap-3 bg-goool-green text-white font-bold py-3 px-8 rounded-xl text-lg">
                        <i class="fa-solid fa-circle-check"></i>
                        Sesión activa como {{ ucfirst(auth()->user()->rol) }}
                    </div>
                @endif
            @else
                <div class="mt-6 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-3 bg-goool-yellow text-goool-green-dark font-bold py-3 px-8 rounded-xl hover:bg-yellow-400 transition-all duration-200 shadow-md hover:shadow-lg text-lg">
                        <i class="fa-solid fa-user-plus"></i>
                        Crear Cuenta
                    </a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-3 bg-goool-green text-white font-bold py-3 px-8 rounded-xl hover:bg-goool-green-dark transition-all duration-200 shadow-md hover:shadow-lg text-lg">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Iniciar Sesión
                    </a>
                </div>
            @endauth
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fa-solid fa-star text-goool-yellow"></i>
                    Productos Destacados
                </h2>
                <a href="{{ route('cliente.catalogo') }}"
                    class="text-goool-green hover:text-goool-green-dark font-semibold text-sm flex items-center gap-1 transition-colors duration-200">
                    Ver todos
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

                @forelse ($productos ?? [] as $producto)
                    <div
                        class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-goool-yellow group">

                        <div
                            class="relative h-40 sm:h-44 bg-gradient-to-br from-goool-green-light/20 to-goool-green-light/5 flex items-center justify-center overflow-hidden">
                            @if ($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy" decoding="async">
                            @else
                                <i
                                    class="fa-solid fa-pizza-slice text-5xl sm:text-6xl text-goool-green/20 group-hover:scale-110 transition-transform duration-300"></i>
                            @endif

                            @if ($producto->disponible)
                                <span
                                    class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Disponible
                                </span>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 text-base line-clamp-1">
                                {{ $producto->nombre }}
                            </h3>

                            <p class="text-gray-500 text-xs mt-1 line-clamp-2 min-h-[2rem]">
                                {{ $producto->descripcion }}
                            </p>

                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                                <span class="text-lg font-extrabold text-goool-green">
                                    Bs. {{ number_format($producto->precio, 2) }}
                                </span>

                                @auth
                                    @if (auth()->user()->rol === 'cliente' && $producto->disponible)
                                        <button onclick="agregarAlCarrito({{ $producto->id }})"
                                            class="inline-flex items-center gap-1.5 bg-goool-orange hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            <span>Agregar</span>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center gap-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200">
                                            <i class="fa-solid fa-lock"></i>
                                            Iniciar Sesión
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center gap-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200">
                                        <i class="fa-solid fa-lock"></i>
                                        Iniciar Sesión
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-400">Pronto tendremos productos disponibles.</p>
                    </div>
                @endforelse

            </div>
        </div>

    </div>

    <script>
        function agregarAlCarrito(productoId) {
            fetch(`/catalogo/agregar/${productoId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
@endsection
