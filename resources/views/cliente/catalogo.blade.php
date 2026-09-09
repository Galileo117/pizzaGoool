@extends('layouts.app')

@section('title', 'Catálogo - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-goool-green"><i class="fa-solid fa-book-open"></i> Nuestro Catálogo</h1>

            <!-- Botón de carrito -->
            <a href="#"
                class="bg-goool-yellow text-goool-green-dark px-4 py-2 rounded-lg font-bold hover:bg-goool-yellow-dark">
                <i class="fa-solid fa-cart-shopping"></i> Carrito (0)
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($productos as $producto)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-goool-green-light h-48 flex items-center justify-center">
                        @if ($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                                class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-pizza-slice text-6xl text-white"></i>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-bold text-lg text-goool-green-dark">{{ $producto->nombre }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $producto->descripcion }}</p>

                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-goool-green">Bs. {{ $producto->precio }}</span>
                            <button
                                class="bg-goool-orange text-white px-3 py-2 rounded-lg text-sm hover:bg-goool-yellow-dark">
                                <i class="fa-solid fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
