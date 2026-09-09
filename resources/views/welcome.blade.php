@extends('layouts.app')

@section('title', 'Inicio - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-green-700">¡Un golazo de sabor!</h1>
            <p class="text-gray-600 mt-4">Pide tu pizza favorita y recíbela en la puerta de tu casa.</p>

            @auth
                @if (auth()->user()->rol === 'cliente')
                    <a href="{{ route('cliente.catalogo') }}"
                        class="mt-6 inline-block bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-yellow-600">
                        Ver Catálogo
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                    class="mt-6 inline-block bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-yellow-600">
                    Crear Cuenta
                </a>
            @endauth
        </div>
    </div>
@endsection
