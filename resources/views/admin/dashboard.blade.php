{{-- Resumen de actividad para usuarios con rol de administrador. --}}
@extends('layouts.admin')

@section('title', 'Dashboard - Panel de Administración')
@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-goool-green-dark">Bienvenido, {{ auth()->user()->nombres }}</h2>
        <p class="text-gray-600">Aquí tienes un resumen de la actividad de Pizza Goool.</p>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pedidos -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-goool-green">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Total Pedidos</p>
                    <p class="text-3xl font-bold text-goool-green-dark">{{ $totalPedidos ?? 0 }}</p>
                </div>
                <i class="fa-solid fa-receipt text-4xl text-goool-green/30"></i>
            </div>
        </div>

        <!-- Ventas Totales -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-goool-yellow">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Ventas Totales</p>
                    <p class="text-3xl font-bold text-goool-green-dark">Bs. {{ number_format($ventasTotales ?? 0, 2) }}</p>
                </div>
                <i class="fa-solid fa-money-bill-wave text-4xl text-goool-yellow/30"></i>
            </div>
        </div>

        <!-- Total Productos -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-goool-orange">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Productos</p>
                    <p class="text-3xl font-bold text-goool-green-dark">{{ $totalProductos ?? 0 }}</p>
                </div>
                <i class="fa-solid fa-pizza-slice text-4xl text-goool-orange/30"></i>
            </div>
        </div>

        <!-- Total Clientes -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-semibold">Clientes</p>
                    <p class="text-3xl font-bold text-goool-green-dark">{{ $totalClientes ?? 0 }}</p>
                </div>
                <i class="fa-solid fa-users text-4xl text-blue-500/30"></i>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-goool-green-dark mb-4">Accesos Rápidos</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.productos.index') }}"
                class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg hover:border-goool-green hover:bg-goool-green-light/10 transition">
                <i class="fa-solid fa-pizza-slice text-3xl text-goool-green mb-2"></i>
                <span class="font-semibold text-gray-700">Gestionar Productos</span>
            </a>
            <a href="{{ route('admin.categorias.index') }}"
                class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg hover:border-goool-green hover:bg-goool-green-light/10 transition">
                <i class="fa-solid fa-tags text-3xl text-goool-green mb-2"></i>
                <span class="font-semibold text-gray-700">Gestionar Categorías</span>
            </a>
            <a href="{{ route('admin.pedidos.index') }}"
                class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg hover:border-goool-green hover:bg-goool-green-light/10 transition">
                <i class="fa-solid fa-receipt text-3xl text-goool-green mb-2"></i>
                <span class="font-semibold text-gray-700">Ver Pedidos</span>
            </a>
            <a href="{{ route('admin.usuarios.index') }}"
                class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg hover:border-goool-green hover:bg-goool-green-light/10 transition">
                <i class="fa-solid fa-users text-3xl text-goool-green mb-2"></i>
                <span class="font-semibold text-gray-700">Ver Usuarios</span>
            </a>
        </div>
    </div>
@endsection
