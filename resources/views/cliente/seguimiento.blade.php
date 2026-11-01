{{-- Seguimiento del pedido propietario; la ruta verifica que pertenezca al usuario autenticado. --}}
@extends('layouts.cliente')

@section('title', 'Seguimiento - Pizza Goool')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 sm:py-8 lg:py-10">

        {{-- ==============================================================
         ENCABEZADO
    ============================================================== --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-truck-fast text-goool-yellow" aria-hidden="true"></i>
                Seguimiento del Pedido
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-goool-green" aria-hidden="true"></i>
                Mantente informado sobre el estado de tu pedido
            </p>
        </div>

        {{-- ==============================================================
         TARJETA PRINCIPAL
    ============================================================== --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

            {{-- Encabezado del pedido --}}
            <div class="bg-goool-green px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-receipt text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-lg">
                            Pedido #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                        </h2>
                        <p class="text-white/80 text-sm flex items-center gap-2">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Badge de estado --}}
                <span
                    class="inline-flex items-center gap-2 bg-goool-yellow text-goool-green-dark px-4 py-2 rounded-xl font-bold text-sm shadow-sm self-start sm:self-center">
                    <i class="fa-solid fa-circle text-[8px]" aria-hidden="true"></i>
                    {{ ucfirst($pedido->estado) }}
                </span>
            </div>

            {{-- Contenido del pedido --}}
            <div class="p-5 sm:p-6">

                {{-- Resumen del pedido --}}
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-list" aria-hidden="true"></i>
                        Detalle del Pedido
                    </h3>

                    <div class="space-y-3 divide-y divide-gray-100">
                        @foreach ($pedido->detallePedidos as $detalle)
                            <div class="flex justify-between items-center pt-3 first:pt-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-goool-green/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-pizza-slice text-goool-green text-sm" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-800">{{ $detalle->producto->nombre }}</span>
                                        <span class="text-gray-400 text-sm ml-2">x{{ $detalle->cantidad }}</span>
                                    </div>
                                </div>
                                <span class="font-bold text-goool-green">
                                    Bs. {{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Información de entrega --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                    <div>
                        <span
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-truck" aria-hidden="true"></i>
                            Tipo de Entrega
                        </span>
                        <p class="font-medium text-gray-800 mt-0.5">
                            @if ($pedido->tipo_entrega === 'recoger_local')
                                <i class="fa-solid fa-store text-goool-green mr-1.5" aria-hidden="true"></i>
                                Recoger en Local
                            @else
                                <i class="fa-solid fa-truck text-goool-orange mr-1.5" aria-hidden="true"></i>
                                Delivery a Domicilio
                            @endif
                        </p>
                    </div>

                    <div>
                        <span
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-money-bill-wave" aria-hidden="true"></i>
                            Método de Pago
                        </span>
                        <p class="font-medium text-gray-800 mt-0.5">
                            @if ($pedido->metodo_pago === 'qr')
                                <i class="fa-solid fa-qrcode text-goool-orange mr-1.5" aria-hidden="true"></i>
                                Código QR
                            @else
                                <i class="fa-solid fa-cash-register text-goool-green mr-1.5" aria-hidden="true"></i>
                                Efectivo
                            @endif
                        </p>
                    </div>

                    @if ($pedido->direccion)
                        <div class="sm:col-span-2">
                            <span
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                Dirección de Entrega
                            </span>
                            <p class="font-medium text-gray-800 mt-0.5">
                                {{ $pedido->direccion }}
                                @if ($pedido->referencia)
                                    <span class="text-gray-500 text-sm block">Ref: {{ $pedido->referencia }}</span>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Barra de progreso de estado --}}
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-route" aria-hidden="true"></i>
                        Progreso del Pedido
                    </h3>

                    @php
                        $estados = ['pendiente', 'preparacion', 'listo', 'en_reparto', 'entregado'];
                        $estadoActual = strtolower($pedido->estado);
                        $indiceActual = array_search($estadoActual, $estados);
                    @endphp

                    <div class="relative">
                        {{-- Barra de progreso --}}
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                            <div class="bg-goool-green h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ (($indiceActual + 1) / count($estados)) * 100 }}%">
                            </div>
                        </div>

                        {{-- Estados --}}
                        <div class="grid grid-cols-5 gap-1 text-center">
                            @foreach ($estados as $index => $estado)
                                @php
                                    $isCompleted = $index <= $indiceActual;
                                    $isCurrent = $index === $indiceActual;
                                @endphp

                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 rounded-full transition-all duration-300
                                    {{ $isCompleted ? 'bg-goool-green text-white' : 'bg-gray-200 text-gray-400' }}
                                    {{ $isCurrent ? 'ring-4 ring-goool-green/30 scale-110' : '' }}">
                                        @if ($isCompleted && !$isCurrent)
                                            <i class="fa-solid fa-check text-xs" aria-hidden="true"></i>
                                        @elseif($isCurrent)
                                            <i class="fa-solid fa-spinner fa-spin text-xs" aria-hidden="true"></i>
                                        @else
                                            <i class="fa-regular fa-circle text-xs" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                    <span
                                        class="text-[10px] font-medium mt-1.5
                                    {{ $isCompleted ? 'text-goool-green' : 'text-gray-400' }}
                                    {{ $isCurrent ? 'font-bold' : '' }}">
                                        {{ ucfirst($estado) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-center pt-4 border-t-2 border-goool-green/20">
                    <span class="text-lg font-bold text-gray-800">Total del Pedido</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                        Bs. {{ number_format($pedido->total, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ==============================================================
         ACCIONES ADICIONALES
    ============================================================== --}}
        <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">

            <a href="{{ route('cliente.catalogo') }}"
                class="inline-flex items-center justify-center gap-2 bg-goool-green/10 text-goool-green px-6 py-3 rounded-xl font-semibold hover:bg-goool-green hover:text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-goool-green focus:ring-offset-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Seguir Comprando
            </a>

            @if (in_array(strtolower($pedido->estado), ['entregado', 'cancelado']))
                <a href="#"
                    class="inline-flex items-center justify-center gap-2 bg-goool-yellow text-goool-green-dark px-6 py-3 rounded-xl font-bold hover:bg-yellow-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2">
                    <i class="fa-solid fa-star" aria-hidden="true"></i>
                    Calificar Pedido
                </a>
            @endif

            @if (strtolower($pedido->estado) === 'pendiente')
                <form action="{{ route('cliente.cancelar_pedido', $pedido->id) }}" method="POST" class="inline"
                    data-confirm-title="Cancelar pedido"
                    data-confirm-message="¿Estás seguro de que deseas cancelar este pedido?"
                    data-confirm-confirm-label="Sí, cancelar pedido">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Cancelar Pedido
                    </button>
                </form>
            @endif

        </div>
    </div>
@endsection
