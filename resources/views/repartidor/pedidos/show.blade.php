{{-- Detalle de entrega asignada, incluida dirección y ubicación capturadas en el pedido. --}}
@extends('layouts.repartidor')

@section('title', 'Detalle del Pedido - Panel de Repartidor')
@section('page_title', 'Detalle del Pedido')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #mapa-repartidor {
            height: 280px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <a href="{{ route('repartidor.pedidos.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        @if ($pedido->estado === 'en_reparto')
            <span
                class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full font-bold text-sm border-2 border-purple-300 self-start">
                <i class="fa-solid fa-truck-fast"></i> En Reparto
            </span>
        @elseif($pedido->estado === 'entregado')
            <span
                class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm border-2 border-green-300 self-start">
                <i class="fa-solid fa-check"></i> Entregado
            </span>
        @elseif($pedido->estado === 'cancelado')
            <span
                class="bg-red-100 text-red-700 px-4 py-2 rounded-full font-bold text-sm border-2 border-red-300 self-start">
                <i class="fa-solid fa-xmark"></i> Cancelado / Fallido
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                        class="bg-goool-green-light/30 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
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
                    <div class="mt-5 ml-auto max-w-xs space-y-2 border-t border-gray-200 pt-4 text-sm">
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>Productos</span>
                            <span>Bs. {{ number_format(max(0, (float) $pedido->total - (float) $pedido->costo_envio), 2) }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>Costo de envío</span>
                            <span>Bs. {{ number_format($pedido->costo_envio, 2) }}</span>
                        </div>
                        <div class="flex justify-between gap-4 border-t border-gray-200 pt-2 font-bold text-gray-800">
                            <span>Total a cobrar</span>
                            <span>Bs. {{ number_format($pedido->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($pedido->esDelivery() && $pedido->direccion)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-goool-green px-6 py-3 flex justify-between items-center">
                        <h3 class="text-white font-bold text-sm">
                            <i class="fa-solid fa-map-location-dot"></i> UBICACIÓN DE ENTREGA
                        </h3>
                        @if ($pedido->latitud && $pedido->longitud)
                            <span class="bg-goool-yellow text-goool-green-dark text-xs font-bold px-2 py-1 rounded-full">
                                <i class="fa-solid fa-circle-check"></i> Ubicación marcada
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-800">
                                <i class="fa-solid fa-location-dot text-goool-green"></i>
                                <strong>{{ $pedido->direccion }}</strong>
                            </p>
                            @if ($pedido->referencia)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fa-solid fa-info-circle"></i> Referencia: {{ $pedido->referencia }}
                                </p>
                            @endif
                        </div>

                        @if ($pedido->latitud && $pedido->longitud)
                            <div id="mapa-repartidor" class="mb-4 border-2 border-gray-200"></div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $pedido->latitud }},{{ $pedido->longitud }}"
                                    target="_blank"
                                    class="inline-flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition">
                                    <i class="fa-solid fa-map"></i> Abrir en Google Maps
                                </a>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm mb-4">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                El cliente no marcó la ubicación exacta en el mapa.
                            </div>

                            @if ($pedido->usuario && $pedido->usuario->telefono_contacto)
                                <a href="tel:{{ $pedido->usuario->telefono_contacto }}"
                                    class="inline-flex items-center justify-center gap-2 bg-goool-green hover:bg-goool-green-dark text-white font-bold py-3 px-4 rounded-lg transition w-full">
                                    <i class="fa-solid fa-phone"></i> Llamar al cliente
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-6 py-3">
                    <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-user"></i> CLIENTE</h3>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="bg-goool-green-light/30 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-goool-green"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 truncate">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ $pedido->usuario->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    @if ($pedido->usuario && $pedido->usuario->telefono_contacto)
                        <div class="grid grid-cols-2 gap-2">
                            <a href="tel:{{ $pedido->usuario->telefono_contacto }}"
                                class="flex items-center justify-center gap-2 bg-goool-green text-white font-semibold px-2 py-2 rounded-lg hover:bg-goool-green-dark transition">
                                <i class="fa-solid fa-phone"></i> Llamar
                            </a>
                            <a href="https://wa.me/591{{ $pedido->usuario->telefono_contacto }}?text=Hola%20{{ $pedido->usuario->cliente->nombres ?? '' }},%20soy%20tu%20repartidor%20de%20Pizza%20Goool.%20Estoy%20en%20camino%20a%20tu%20domicilio."
                                target="_blank"
                                class="flex items-center justify-center gap-2 bg-green-500 text-white font-semibold px-2 py-2 rounded-lg hover:bg-green-600 transition">
                                <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp
                            </a>
                        </div>
                    @endif
                </div>
            </div>

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

            @if ($pedido->estado === 'en_reparto')
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-goool-green px-6 py-3">
                        <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-gear"></i> ACCIONES</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <button type="button" onclick="abrirModalEntregar()"
                            class="w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition">
                            <i class="fa-solid fa-check-circle"></i> Marcar como Entregado
                        </button>

                        <button type="button" onclick="abrirModalFallido()"
                            class="w-full bg-red-500 text-white font-bold py-3 rounded-lg hover:bg-red-600 transition">
                            <i class="fa-solid fa-triangle-exclamation"></i> No se pudo entregar
                        </button>
                    </div>
                </div>

                {{-- Modal Entregar --}}
                <div id="modal-entregar" class="fixed inset-0 z-50 hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="cerrarModalEntregar()"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-check-circle text-4xl text-green-500"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Confirmar entrega?</h3>
                            <p class="text-gray-500 text-sm mb-6">Confirma que el pedido fue entregado correctamente al
                                cliente.</p>
                            <div class="flex gap-3">
                                <button type="button" onclick="cerrarModalEntregar()"
                                    class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                                <form action="{{ route('repartidor.pedidos.marcar_entregado', $pedido->id) }}"
                                    method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition">Sí,
                                        entregar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Fallido --}}
                <div id="modal-fallido" class="fixed inset-0 z-50 hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="cerrarModalFallido()"></div>
                    <div class="relative flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-4xl text-red-500"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Reportar pedido fallido?</h3>
                            <p class="text-gray-500 text-sm mb-4">Indica el motivo por el cual no se pudo entregar el
                                pedido.</p>

                            <form action="{{ route('repartidor.pedidos.marcar_fallido', $pedido->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="motivo" required
                                    class="w-full border border-gray-300 rounded-lg p-3 mb-4 focus:border-goool-green focus:outline-none">
                                    <option value="">-- Seleccionar motivo --</option>
                                    <option value="Cliente no contesta">Cliente no contesta</option>
                                    <option value="Dirección incorrecta">Dirección incorrecta</option>
                                    <option value="Cliente no se encuentra">Cliente no se encuentra</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <div class="flex gap-3">
                                    <button type="button" onclick="cerrarModalFallido()"
                                        class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                                    <button type="submit"
                                        class="flex-1 bg-red-500 text-white font-bold py-3 rounded-lg hover:bg-red-600 transition">Confirmar
                                        Fallo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function abrirModalEntregar() {
                        document.getElementById('modal-entregar').classList.remove('hidden');
                    }

                    function cerrarModalEntregar() {
                        document.getElementById('modal-entregar').classList.add('hidden');
                    }

                    function abrirModalFallido() {
                        document.getElementById('modal-fallido').classList.remove('hidden');
                    }

                    function cerrarModalFallido() {
                        document.getElementById('modal-fallido').classList.add('hidden');
                    }
                </script>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($pedido->latitud && $pedido->longitud)
                const lat = {{ $pedido->latitud }};
                const lng = {{ $pedido->longitud }};
                const direccion = @json($pedido->direccion);

                const mapaElement = document.getElementById('mapa-repartidor');
                if (!mapaElement) return;

                const map = L.map('mapa-repartidor').setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<strong>Dirección de entrega</strong><br>' + direccion)
                    .openPopup();
            @endif
        });
    </script>
@endpush
