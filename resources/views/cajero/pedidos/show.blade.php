{{-- Detalle operativo para caja: transición válida y asignación del repartidor. --}}
@extends('layouts.cajero')

@section('title', 'Detalle del Pedido - Panel de Cajero')
@section('page_title', 'Detalle del Pedido')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('cajero.pedidos.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        @php
            $colores = [
                'pendiente' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                'preparacion' => 'bg-blue-100 text-blue-700 border-blue-300',
                'listo' => 'bg-green-100 text-green-700 border-green-300',
                'en_reparto' => 'bg-purple-100 text-purple-700 border-purple-300',
                'entregado' => 'bg-gray-100 text-gray-700 border-gray-300',
                'cancelado' => 'bg-red-100 text-red-700 border-red-300',
            ];
            $color = $colores[$pedido->estado] ?? 'bg-gray-100 text-gray-700 border-gray-300';
        @endphp
        <span class="{{ $color }} px-4 py-2 rounded-full font-bold text-sm border-2">
            <i class="fa-solid fa-circle-dot"></i> {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
        </span>
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
                            <p class="text-xs text-gray-200 uppercase">Total</p>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200">
                    <h3 class="text-gray-500 text-xs font-semibold mb-2 uppercase tracking-wider">
                        <i class="fa-solid fa-truck-fast"></i> Tipo de Entrega
                    </h3>
                    <p class="font-bold text-gray-800">
                        {{ $pedido->tipo_entrega === 'contraentrega' ? 'Delivery a Domicilio' : 'Recoger en Local' }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200">
                    <h3 class="text-gray-500 text-xs font-semibold mb-2 uppercase tracking-wider">
                        <i class="fa-solid fa-money-bill-wave"></i> Método de Pago
                    </h3>
                    <p class="font-bold text-gray-800">
                        {{ ucfirst($pedido->metodo_pago) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
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
                            <p class="font-bold text-gray-800">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $pedido->usuario->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <p class="flex items-center gap-2 text-gray-700">
                        <i class="fa-solid fa-phone text-goool-green w-4"></i>
                        {{ $pedido->usuario->telefono_contacto ?? '—' }}
                    </p>

                    @if ($pedido->tipo_entrega === 'contraentrega')
                        <hr class="border-gray-200">
                        <p class="text-gray-500 text-xs font-semibold uppercase">Dirección de Entrega</p>
                        <p class="text-gray-700">{{ $pedido->direccion }}</p>
                        @if ($pedido->referencia)
                            <p class="text-gray-500 text-xs">Referencia: {{ $pedido->referencia }}</p>
                        @endif
                    @endif
                </div>
            </div>

            @if ($pedido->repartidor)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-purple-200">
                    <div class="bg-purple-500 px-6 py-3">
                        <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-motorcycle"></i> REPARTIDOR ASIGNADO
                        </h3>
                    </div>
                    <div class="p-5">
                        <p class="font-bold text-gray-800">{{ $pedido->repartidor->nombre_completo }}</p>
                        <p class="text-xs text-gray-500">{{ $pedido->repartidor->telefono_contacto ?? 'Sin teléfono' }}</p>
                    </div>
                </div>
            @endif

            <!-- Acciones -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-6 py-3">
                    <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-gear"></i> ACCIONES</h3>
                </div>
                <div class="p-5 space-y-3">

                    @if ($pedido->estado === 'pendiente')
                        <form action="{{ route('cajero.pedidos.cambiar_estado', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="preparacion">
                            <button type="submit"
                                class="w-full bg-blue-500 text-white font-bold py-3 rounded-lg hover:bg-blue-600 transition">
                                <i class="fa-solid fa-pizza-slice"></i> Aceptar y Preparar
                            </button>
                        </form>
                    @endif

                    @if ($pedido->estado === 'preparacion')
                        <form action="{{ route('cajero.pedidos.cambiar_estado', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="listo">
                            <button type="submit"
                                class="w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition">
                                <i class="fa-solid fa-check-circle"></i> Marcar como Listo
                            </button>
                        </form>
                    @endif

                    {{-- LISTO + RECOGER: Entregar directamente --}}
                    @if ($pedido->estado === 'listo' && $pedido->tipo_entrega === 'recoger_local')
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded-lg text-sm">
                            <i class="fa-solid fa-store"></i>
                            Este pedido es para <strong>Recoger en Local</strong>. El cliente lo retirará en el local.
                        </div>

                        <form action="{{ route('cajero.pedidos.cambiar_estado', $pedido->id) }}" method="POST"
                            onsubmit="return confirm('¿Confirmas que el cliente retiró el pedido?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="entregado">
                            <button type="submit"
                                class="w-full bg-gray-600 text-white font-bold py-3 rounded-lg hover:bg-gray-700 transition">
                                <i class="fa-solid fa-check-circle"></i> Marcar como Entregado (Retirado)
                            </button>
                        </form>
                    @endif

                    {{-- LISTO + DELIVERY: Asignar repartidor --}}
                    @if ($pedido->estado === 'listo' && $pedido->tipo_entrega === 'contraentrega')
                        <form action="{{ route('cajero.pedidos.asignar_repartidor', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Asignar Repartidor</label>
                            <select name="repartidor_id" required
                                class="w-full border border-gray-300 rounded-lg p-3 mb-3 focus:border-goool-green focus:outline-none">
                                <option value="">-- Seleccionar repartidor --</option>
                                @foreach ($repartidores as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->nombre_completo }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="w-full bg-purple-500 text-white font-bold py-3 rounded-lg hover:bg-purple-600 transition">
                                <i class="fa-solid fa-truck-fast"></i> Asignar y Enviar
                            </button>
                        </form>
                    @endif

                    @if ($pedido->estado === 'en_reparto')
                        <div class="bg-purple-50 border border-purple-200 text-purple-800 p-3 rounded-lg text-sm">
                            <i class="fa-solid fa-motorcycle"></i>
                            El pedido está en camino. El repartidor confirmará la entrega desde su panel.
                        </div>
                    @endif

                    {{-- CORREGIDO: Solo permitir cancelar si está pendiente o en preparación --}}
                    @if (in_array($pedido->estado, ['pendiente', 'preparacion']))
                        <hr class="border-gray-200">
                        <button type="button" onclick="abrirModalCancelar()"
                            class="w-full bg-red-500 text-white font-bold py-3 rounded-lg hover:bg-red-600 transition">
                            <i class="fa-solid fa-xmark"></i> Cancelar Pedido
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CANCELAR PEDIDO --}}
    <div id="modal-cancelar" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="cerrarModalCancelar()"></div>
        <div class="relative flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-4xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">¿Cancelar este pedido?</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Esta acción no se puede deshacer y el cliente será notificado de la cancelación.
                </p>

                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModalCancelar()"
                        class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-300 transition">
                        Volver
                    </button>
                    <form action="{{ route('cajero.pedidos.cambiar_estado', $pedido->id) }}" method="POST"
                        class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" value="cancelado">
                        <button type="submit"
                            class="w-full bg-red-500 text-white font-bold py-3 rounded-lg hover:bg-red-600 transition">
                            Sí, cancelar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function abrirModalCancelar() {
            document.getElementById('modal-cancelar').classList.remove('hidden');
        }

        function cerrarModalCancelar() {
            document.getElementById('modal-cancelar').classList.add('hidden');
        }
    </script>
@endsection
