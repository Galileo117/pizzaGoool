{{-- Cola de pedidos para caja; filtros y estados corresponden a Cajero\PedidoController. --}}
@extends('layouts.cajero')

@section('title', 'Gestionar Pedidos - Panel de Cajero')
@section('page_title', 'Gestionar Pedidos')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Listado de Pedidos</h2>
            <p class="text-gray-600 text-sm">Visualiza y actualiza el estado de los pedidos.</p>
        </div>

        <form method="GET" action="{{ route('cajero.pedidos.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <select name="estado"
                class="flex-1 sm:flex-none border border-gray-300 rounded-lg p-2 text-sm focus:border-goool-green focus:outline-none">
                <option value="">Todos (activos)</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                <option value="preparacion" {{ request('estado') == 'preparacion' ? 'selected' : '' }}>En Preparación
                </option>
                <option value="listo" {{ request('estado') == 'listo' ? 'selected' : '' }}>Listos</option>
                <option value="en_reparto" {{ request('estado') == 'en_reparto' ? 'selected' : '' }}>En Reparto</option>
                <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregados</option>
                <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelados</option>
            </select>
            <button type="submit"
                class="bg-goool-green text-white px-4 py-2 rounded-lg text-sm hover:bg-goool-green-dark transition whitespace-nowrap">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </form>
    </div>

    {{-- Tabla (escritorio) --}}
    <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600"># Pedido</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-goool-green">
                                #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold text-gray-800">
                                Bs. {{ number_format($pedido->total, 2) }}
                                @if ($pedido->esDelivery())
                                    <span class="block text-xs font-normal text-gray-500">
                                        Envío: Bs. {{ number_format($pedido->costo_envio, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <i
                                    class="fa-solid {{ $pedido->esDelivery() ? 'fa-truck-fast' : 'fa-store' }} text-goool-green"></i>
                                {{ $pedido->esDelivery() ? 'Delivery' : 'Recoger' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $colores = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-700',
                                        'preparacion' => 'bg-blue-100 text-blue-700',
                                        'listo' => 'bg-green-100 text-green-700',
                                        'en_reparto' => 'bg-purple-100 text-purple-700',
                                        'entregado' => 'bg-gray-100 text-gray-700',
                                        'cancelado' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $colores[$pedido->estado] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="{{ $color }} text-xs font-bold px-2 py-1 rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('cajero.pedidos.show', $pedido->id) }}"
                                    class="bg-goool-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-goool-green-dark transition">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-receipt text-4xl mb-2"></i>
                                <p>No hay pedidos para mostrar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">{{ $pedidos->links() }}</div>
    </div>

    {{-- Tarjetas (móvil) --}}
    <div class="md:hidden space-y-4">
        @forelse($pedidos as $pedido)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-white font-bold text-sm">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-gray-200 text-xs">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
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
                    <span class="{{ $color }} text-xs font-bold px-2 py-1 rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                </div>

                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Cliente</span>
                        <span class="font-bold text-gray-800">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Total</span>
                        <span class="text-right font-bold text-goool-green">
                            Bs. {{ number_format($pedido->total, 2) }}
                            @if ($pedido->esDelivery())
                                <span class="block text-xs font-normal text-gray-500">
                                    Envío: Bs. {{ number_format($pedido->costo_envio, 2) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Tipo</span>
                        <span class="text-gray-700">
                            <i
                                class="fa-solid {{ $pedido->esDelivery() ? 'fa-truck-fast' : 'fa-store' }} text-goool-green"></i>
                            {{ $pedido->esDelivery() ? 'Delivery' : 'Recoger' }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-4 py-3">
                    <a href="{{ route('cajero.pedidos.show', $pedido->id) }}"
                        class="block w-full bg-goool-green text-white text-center font-bold py-2.5 rounded-lg hover:bg-goool-green-dark transition">
                        <i class="fa-solid fa-eye"></i> Ver Detalle
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-receipt text-4xl mb-2"></i>
                <p>No hay pedidos para mostrar.</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $pedidos->links() }}</div>
    </div>
@endsection
