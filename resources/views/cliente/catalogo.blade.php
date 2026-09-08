@extends('layouts.app')

@section('title', 'Catálogo - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-green-700 mb-6">Nuestro Catálogo</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($productos as $producto)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-green-100 h-48 flex items-center justify-center">
                        <!-- Si no hay imagen, ponemos un emoji -->
                        <span class="text-6xl">🍕</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg">{{ $producto->nombre }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $producto->descripcion }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-green-700">Bs. {{ $producto->precio }}</span>
                            <button class="bg-yellow-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-yellow-600">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
