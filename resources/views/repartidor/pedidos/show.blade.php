@extends('layouts.repartidor')

@section('title', 'Detalle del Pedido - Panel de Repartidor')
@section('page_title', 'Detalle del Pedido')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('repartidor.pedidos.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        @if ($pedido->estado === 'en_reparto')
            <span class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full font-bold text-sm border-2 border-purple-300">
                <i class="fa-solid fa-truck-fast"></i> En Reparto
            </span>
        @else
            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm border-2 border-green-300">
                <i class="fa-solid fa-check"></i> Entregado
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalle del pedido -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-white font-bold text-xl">
                                <i class="fa-solid fa-receipt"></i> Pedido #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                            </h2>
                            <p class="text-gray-200 text-sm mt-1">
                                <i class="fa-solid fa-calendar"></i> {{ $pedido->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right text-white">
                            <p class="text-xs text-gray-200 uppercase">Total a cobrar</p>
                            <p class="text-2xl font-bold">Bs. {{ number_format($pedido->total, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-gray-500 text-sm font-semibold mb-4 uppercase tracking-wider">
                        <i class="fa-solid fa-list"></i> Detalle del Pedido
                    </h3>
                    <div class="space-y-3">
                        @foreach ($pedido->detallePedidos as $detalle)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="bg-goool-green-light/30 w-10 h-10 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-pizza-slice text-goool-green"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $detalle->producto->nombre ?? 'Producto eliminado' }}</p>
                                        <p class="text-xs text-gray-500">Cantidad: {{ $detalle->cantidad }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-800">Bs.
                                    {{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Mapa de ubicación -->
            @if ($pedido->tipo_entrega === 'contraentrega' && $pedido->direccion)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-goool-green px-6 py-3">
                        <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-map-location-dot"></i> UBICACIÓN DE
                            ENTREGA</h3>
                    </div>
                    <div class="p-4">
                        <div id="mapa" class="w-full h-80 rounded-lg border border-gray-200"></div>
                        <p class="text-sm text-gray-600 mt-3">
                            <i class="fa-solid fa-location-dot text-goool-green"></i>
                            <strong>{{ $pedido->direccion }}</strong>
                            @if ($pedido->referencia)
                                <br><span class="text-gray-500 text-xs">Referencia: {{ $pedido->referencia }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel lateral -->
        <div class="space-y-6">
            <!-- Info del cliente -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-6 py-3">
                    <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-user"></i> CLIENTE</h3>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-goool-green-light/30 w-10 h-10 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-goool-green"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $pedido->usuario->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $pedido->usuario->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <a href="tel:{{ $pedido->usuario->telefono ?? '' }}"
                        class="flex items-center gap-2 text-goool-green font-semibold hover:underline">
                        <i class="fa-solid fa-phone w-4"></i>
                        {{ $pedido->usuario->telefono ?? 'Sin teléfono' }}
                    </a>

                    @if ($pedido->tipo_entrega === 'contraentrega')
                        <hr class="border-gray-200">
                        <p class="text-gray-500 text-xs font-semibold uppercase">Dirección de Entrega</p>
                        <p class="text-gray-700">{{ $pedido->direccion }}</p>
                    @endif
                </div>
            </div>

            <!-- Método de pago -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-6 py-3">
                    <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-money-bill-wave"></i> MÉTODO DE PAGO</h3>
                </div>
                <div class="p-5">
                    <p class="text-lg font-bold text-gray-800">
                        @if ($pedido->metodo_pago === 'qr')
                            <i class="fa-solid fa-qrcode text-goool-green"></i> Código QR
                        @else
                            <i class="fa-solid fa-money-bill-wave text-goool-green"></i> Efectivo
                        @endif
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if ($pedido->metodo_pago === 'qr')
                            El cliente ya pagó con QR. Solo entrega el pedido.
                        @else
                            Debes cobrar Bs. {{ number_format($pedido->total, 2) }} al cliente.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Acciones -->
            @if ($pedido->estado === 'en_reparto')
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-goool-green px-6 py-3">
                        <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-gear"></i> ACCIONES</h3>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('repartidor.pedidos.marcar_entregado', $pedido->id) }}" method="POST"
                            onsubmit="return confirm('¿Confirmas que el pedido fue entregado al cliente?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition">
                                <i class="fa-solid fa-check-circle"></i> Marcar como Entregado
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Script para el mapa con Leaflet -->
    @if ($pedido->tipo_entrega === 'contraentrega' && $pedido->direccion)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Coordenadas por defecto (La Paz, Bolivia)
                const lat = -16.5;
                const lng = -68.15;

                // Inicializar el mapa
                const map = L.map('mapa').setView([lat, lng], 15);

                // Cargar los tiles de OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Agregar marcador
                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<strong>Dirección de entrega</strong><br>{{ $pedido->direccion }}')
                    .openPopup();
            });
        </script>
    @endif
@endsection
