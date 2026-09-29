@extends('layouts.repartidor')

@section('title', 'Dashboard - Panel de Repartidor')
@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-goool-green-dark">Bienvenido, {{ auth()->user()->nombre }}</h2>
        <p class="text-gray-600">Aquí verás tus pedidos asignados para entregar.</p>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Pedidos Asignados -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Pedidos Asignados</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $asignados ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fa-solid fa-truck-fast text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Entregados Hoy -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Entregados Hoy</p>
                    <p class="text-3xl font-bold text-green-600">{{ $entregadosHoy ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fa-solid fa-calendar-check text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Entregados Totales -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Entregados Totales</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $entregadosTotales ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-check-double text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acceso rápido -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-goool-green-dark mb-4">Acceso Rápido</h3>
        <a href="{{ route('repartidor.pedidos.index') }}" class="inline-flex items-center gap-3 bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
            <i class="fa-solid fa-motorcycle"></i>
            Ver Mis Entregas
        </a>
    </div>
@endsection