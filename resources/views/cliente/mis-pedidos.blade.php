{{-- Historial del cliente autenticado, con detalle y acciones permitidas por estado. --}}
@extends('layouts.cliente')

@section('title', 'Mis Pedidos - Pizza Goool')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-receipt text-goool-yellow"></i>
                Mis Pedidos
            </h1>
            <p class="text-gray-500 mt-1">Aquí puedes ver el historial de tus pedidos.</p>
        </div>

        @forelse($pedidos as $pedido)
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-goool-green px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-white font-bold text-lg">
                            <i class="fa-solid fa-receipt"></i> Pedido #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-gray-200 text-sm">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @php
                        $colores = [
                            'pendiente' => 'bg-yellow-400 text-yellow-900',
                            'preparacion' => 'bg-blue-400 text-blue-900',
                            'listo' => 'bg-green-400 text-green-900',
                            'en_reparto' => 'bg-purple-400 text-purple-900',
                            'entregado' => 'bg-gray-300 text-gray-800',
                            'cancelado' => 'bg-red-400 text-red-900',
                        ];
                        $color = $colores[$pedido->estado] ?? 'bg-gray-300 text-gray-800';
                    @endphp
                    <span class="{{ $color }} px-4 py-2 rounded-full font-bold text-sm">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="space-y-2">
                        @foreach ($pedido->detallePedidos as $detalle)
                            <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                                <span class="text-gray-700">
                                    <i class="fa-solid fa-pizza-slice text-goool-green"></i>
                                    {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                    <span class="text-gray-400">x{{ $detalle->cantidad }}</span>
                                </span>
                                <span class="font-semibold text-gray-800">
                                    Bs. {{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                        <div class="flex gap-4 text-sm">
                            <span class="text-gray-500">
                                <i
                                    class="fa-solid {{ $pedido->tipo_entrega === 'contraentrega' ? 'fa-truck-fast' : 'fa-store' }} text-goool-green"></i>
                                {{ $pedido->tipo_entrega === 'contraentrega' ? 'Delivery' : 'Recoger' }}
                            </span>
                            <span class="text-gray-500">
                                <i
                                    class="fa-solid {{ $pedido->metodo_pago === 'qr' ? 'fa-qrcode' : 'fa-money-bill-wave' }} text-goool-green"></i>
                                {{ ucfirst($pedido->metodo_pago) }}
                            </span>
                        </div>
                        <span class="text-2xl font-extrabold text-goool-green">
                            Bs. {{ number_format($pedido->total, 2) }}
                        </span>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('cliente.seguimiento', $pedido->id) }}"
                            class="inline-flex items-center gap-2 bg-goool-green text-white font-bold px-4 py-2 rounded-lg hover:bg-goool-green-dark transition text-sm">
                            <i class="fa-solid fa-eye"></i> Ver Seguimiento
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-12 text-center">
                <i class="fa-solid fa-receipt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-500">No tienes pedidos aún</h3>
                <p class="text-gray-400 mt-2">¡Empieza a pedir tus pizzas favoritas!</p>
                <a href="{{ route('cliente.catalogo') }}"
                    class="inline-block mt-6 bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
                    <i class="fa-solid fa-book-open"></i> Ver Catálogo
                </a>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $pedidos->links() }}
        </div>
    </div>
@endsection
