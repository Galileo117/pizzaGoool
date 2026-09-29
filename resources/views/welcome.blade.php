@extends('layouts.cliente')

@section('title', 'Inicio - Pizza Goool')

@section('content')

    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-goool-green to-goool-green-dark">

        {{-- Patrón de puntos decorativo --}}
        <div class="pointer-events-none absolute right-8 top-8 h-32 w-32 opacity-20">
            <div class="grid grid-cols-4 gap-4">
                @for ($i = 0; $i < 16; $i++)
                    <div class="h-3 w-3 rounded-full bg-white"></div>
                @endfor
            </div>
        </div>
        <div class="pointer-events-none absolute bottom-8 left-8 h-32 w-32 opacity-20">
            <div class="grid grid-cols-4 gap-4">
                @for ($i = 0; $i < 16; $i++)
                    <div class="h-3 w-3 rounded-full bg-goool-yellow"></div>
                @endfor
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-16 sm:py-24 relative z-10">
            <div class="text-center">

                <div class="inline-block bg-goool-yellow/20 rounded-full px-6 py-2 mb-6">
                    <span class="text-goool-yellow font-semibold text-sm">
                        <i class="fa-solid fa-futbol mr-2"></i>
                        ¡Un golazo de sabor!
                    </span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white mb-6">
                    Pizza <span class="text-goool-yellow">Goool</span>
                </h1>

                <p class="text-white/80 text-lg sm:text-xl mt-4 max-w-2xl mx-auto mb-8">
                    La mejor pizza con pasión por el fútbol. Pide tu favorita y recíbela caliente en la puerta de tu casa.
                </p>

                @auth
                    @if (auth()->user()->rol === 'cliente')
                        <div
                            class="inline-flex items-center gap-3 bg-white/10 text-white px-6 py-3 rounded-xl font-semibold backdrop-blur-sm">
                            <i class="fa-solid fa-circle-check text-goool-yellow"></i>
                            <span>¡Hola, {{ auth()->user()->nombre }}! ¿Qué te gustaría pedir hoy?</span>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                            <a href="{{ route('cliente.catalogo') }}"
                                class="inline-flex items-center gap-3 bg-goool-yellow text-goool-green-dark font-bold py-4 px-8 rounded-xl hover:bg-yellow-400 transition-all duration-200 shadow-lg hover:shadow-xl text-lg">
                                <i class="fa-solid fa-book-open"></i>
                                Ver Catálogo
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-3 bg-goool-yellow text-goool-green-dark font-bold py-4 px-8 rounded-xl hover:bg-yellow-400 transition-all duration-200 shadow-lg hover:shadow-xl text-lg">
                            <i class="fa-solid fa-user-plus"></i>
                            Crear Cuenta
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-3 bg-white text-goool-green font-bold py-4 px-8 rounded-xl hover:bg-gray-100 transition-all duration-200 shadow-lg hover:shadow-xl text-lg">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Iniciar Sesión
                        </a>
                    </div>
                @endauth

            </div>
        </div>
    </section>

    {{-- CATEGORÍAS --}}
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-goool-green-dark">
                    Nuestras <span class="text-goool-yellow">Categorías</span>
                </h2>
                <p class="text-gray-500 mt-2">Explora nuestro menú por categorías</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @php
                    $categorias = \App\Models\Categoria::all();
                @endphp

                @foreach ($categorias as $categoria)
                    <a href="{{ route('cliente.catalogo') }}"
                        class="group bg-gradient-to-br from-goool-green/5 to-goool-yellow/5 border-2 border-gray-100 rounded-2xl p-6 text-center hover:border-goool-yellow hover:shadow-lg transition-all duration-300">

                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-goool-green text-white rounded-full flex items-center justify-center group-hover:bg-goool-yellow group-hover:text-goool-green-dark transition-all duration-300">
                            @if (str_contains(strtolower($categoria->nombre), 'pizza'))
                                <i class="fa-solid fa-pizza-slice text-2xl"></i>
                            @elseif (str_contains(strtolower($categoria->nombre), 'bebida'))
                                <i class="fa-solid fa-glass-water text-2xl"></i>
                            @elseif (str_contains(strtolower($categoria->nombre), 'promo'))
                                <i class="fa-solid fa-tags text-2xl"></i>
                            @else
                                <i class="fa-solid fa-utensils text-2xl"></i>
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-800 group-hover:text-goool-green transition">
                            {{ $categoria->nombre }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $categoria->productos->count() ?? 0 }} productos
                        </p>
                    </a>
                @endforeach
            </div>

        </div>
    </section>

    {{-- PRODUCTOS DESTACADOS--}}
    <section class="py-12 sm:py-16 bg-gray-50 relative overflow-hidden">

        {{-- Patrón decorativo --}}
        <div class="pointer-events-none absolute right-8 top-12 h-24 w-24 opacity-20">
            <div class="grid grid-cols-4 gap-3">
                @for ($i = 0; $i < 16; $i++)
                    <div class="h-2 w-2 rounded-full bg-goool-green"></div>
                @endfor
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-goool-green-dark flex items-center gap-3">
                        <i class="fa-solid fa-star text-goool-yellow"></i>
                        Productos Destacados
                    </h2>
                    <p class="text-gray-500 mt-1">Los favoritos de nuestros clientes</p>
                </div>
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
                            class="relative h-44 sm:h-48 bg-gradient-to-br from-goool-green-light/20 to-goool-green-light/5 flex items-center justify-center overflow-hidden">
                            @if ($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            @else
                                <i
                                    class="fa-solid fa-pizza-slice text-6xl text-goool-green/20 group-hover:scale-110 transition-transform duration-300"></i>
                            @endif

                            @if ($producto->disponible)
                                <span
                                    class="absolute top-3 right-3 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Disponible
                                </span>
                            @endif
                        </div>

                        <div class="p-4 sm:p-5">
                            <h3 class="text-lg font-bold text-gray-800 line-clamp-1">{{ $producto->nombre }}</h3>
                            <p class="text-gray-500 text-sm mt-1.5 line-clamp-2 min-h-[2.5rem]">
                                {{ $producto->descripcion }}</p>

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                <span class="text-xl font-extrabold text-goool-green">
                                    Bs. {{ number_format($producto->precio, 2) }}
                                </span>

                                @auth
                                    @if (auth()->user()->rol === 'cliente' && $producto->disponible)
                                        <button onclick="agregarAlCarrito({{ $producto->id }})"
                                            class="inline-flex items-center gap-1.5 bg-goool-orange hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                                            <i class="fa-solid fa-cart-plus"></i> Agregar
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center gap-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                                            <i class="fa-solid fa-lock"></i> Iniciar Sesión
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center gap-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                                        <i class="fa-solid fa-lock"></i> Iniciar Sesión
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
    </section>

    {{-- CÓMO FUNCIONA--}}
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-goool-green-dark">
                    ¿Cómo <span class="text-goool-yellow">funciona</span>?
                </h2>
                <p class="text-gray-500 mt-2">Pide tu pizza en 3 simples pasos</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Paso 1 --}}
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-6 bg-goool-green text-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-extrabold">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Elige tu pizza</h3>
                    <p class="text-gray-500">Explora nuestro catálogo y selecciona tus favoritas.</p>
                </div>

                {{-- Paso 2 --}}
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-6 bg-goool-yellow text-goool-green-dark rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-extrabold">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Confirma tu pedido</h3>
                    <p class="text-gray-500">Elige el método de pago y la dirección de entrega.</p>
                </div>

                {{-- Paso 3 --}}
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-6 bg-goool-orange text-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-extrabold">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">¡Disfruta!</h3>
                    <p class="text-gray-500">Recibe tu pizza caliente en la puerta de tu casa.</p>
                </div>

            </div>

        </div>
    </section>

    {{-- CTA FINAL--}}
    <section class="py-12 sm:py-16 bg-goool-green-dark relative overflow-hidden">

        <div class="pointer-events-none absolute right-8 top-8 h-32 w-32 opacity-10">
            <div class="grid grid-cols-4 gap-4">
                @for ($i = 0; $i < 16; $i++)
                    <div class="h-3 w-3 rounded-full bg-goool-yellow"></div>
                @endfor
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                ¿Listo para pedir tu <span class="text-goool-yellow">pizza</span>?
            </h2>
            <p class="text-white/80 text-lg mb-8 max-w-xl mx-auto">
                No esperes más. ¡Pide ahora y recibe tu pizza caliente en la puerta de tu casa!
            </p>

            <a href="{{ route('cliente.catalogo') }}"
                class="inline-flex items-center gap-3 bg-goool-yellow text-goool-green-dark font-bold py-4 px-10 rounded-xl hover:bg-yellow-400 transition-all duration-200 shadow-lg hover:shadow-xl text-lg">
                <i class="fa-solid fa-pizza-slice"></i>
                Pedir Ahora
            </a>
        </div>
    </section>

    @push('scripts')
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
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else if (data.redirect) window.location.href = data.redirect;
                    })
                    .catch(err => console.error('Error:', err));
            }
        </script>
    @endpush
@endsection
