@extends('layouts.app')

@section('title', 'Seguimiento - Pizza Goool')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-goool-green mb-6"><i class="fa-solid fa-truck-fast"></i> Seguimiento del Pedido</h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold">Pedido #{{ $pedido->id }}</h2>
                    <p class="text-gray-600">Total: Bs. {{ $pedido->total }}</p>
                </div>
                <span class="bg-goool-yellow text-goool-green-dark px-4 py-2 rounded-lg font-bold">
                    {{ ucfirst($pedido->estado) }}
                </span>
            </div>
            
            <div class="space-y-4">
                @foreach($pedido->detallePedidos as $detalle)
                    <div class="flex justify-between border-b pb-4">
                        <span class="font-semibold">{{ $detalle->producto->nombre }} x {{ $detalle->cantidad }}</span>
                        <span>Bs. {{ $detalle->precio_unitario * $detalle->cantidad }}</span>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                <h3 class="font-bold mb-2">Estado del Pedido:</h3>
                <div class="flex gap-4">
                    <span class="text-goool-green"><i class="fa-solid fa-circle-check"></i> Pendiente</span>
                    <span class="text-gray-400"><i class="fa-solid fa-circle"></i> En Preparación</span>
                    <span class="text-gray-400"><i class="fa-solid fa-circle"></i> Listo</span>
                    <span class="text-gray-400"><i class="fa-solid fa-circle"></i> En Reparto</span>
                    <span class="text-gray-400"><i class="fa-solid fa-circle"></i> Entregado</span>
                </div>
            </div>
        </div>
    </div>
@endsection