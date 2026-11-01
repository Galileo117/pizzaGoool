{{-- Lista de pedidos asignados al repartidor actual; no incluye pedidos de otras cuentas. --}}
@extends('layouts.repartidor')

@section('title', 'Mis Entregas - Panel de Repartidor')
@section('page_title', 'Mis Entregas')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-goool-green-dark">Mis Pedidos Asignados</h2>
        <p class="text-gray-600 text-sm">Aquí verás los pedidos que debes entregar.</p>
    </div>

    {{-- Vista de tabla (escritorio) --}}
    <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600"># Pedido</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Dirección</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-goool-green">
                                #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                <p class="font-semibold">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $pedido->usuario->telefono_contacto ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $pedido->direccion ?? 'Recoger en local' }}
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-800">Bs. {{ number_format($pedido->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($pedido->estado === 'en_reparto')
                                    <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-full">
                                        <i class="fa-solid fa-truck-fast"></i> En Reparto
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">
                                        <i class="fa-solid fa-check"></i> Entregado
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('repartidor.pedidos.show', $pedido->id) }}"
                                    class="bg-goool-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-goool-green-dark transition">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-motorcycle text-4xl mb-2"></i>
                                <p>No tienes pedidos asignados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">{{ $pedidos->links() }}</div>
    </div>

    {{-- Vista de tarjetas (móvil) --}}
    <div class="md:hidden space-y-4">
        @forelse($pedidos as $pedido)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-goool-green px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-white font-bold text-sm">
                            <i class="fa-solid fa-receipt"></i> #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-gray-200 text-xs">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if ($pedido->estado === 'en_reparto')
                        <span class="bg-purple-400 text-purple-900 text-xs font-bold px-2 py-1 rounded-full">En
                            Reparto</span>
                    @else
                        <span class="bg-green-400 text-green-900 text-xs font-bold px-2 py-1 rounded-full">Entregado</span>
                    @endif
                </div>

                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-semibold uppercase">Cliente</span>
                        <span
                            class="text-sm font-bold text-gray-800">{{ $pedido->usuario->nombre_completo ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-semibold uppercase">Teléfono</span>
                        <span class="text-sm text-gray-700">{{ $pedido->usuario->telefono_contacto ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-semibold uppercase">Total</span>
                        <span class="text-sm font-bold text-goool-green">Bs. {{ number_format($pedido->total, 2) }}</span>
                    </div>

                    <hr class="border-gray-100">

                    <a href="{{ route('repartidor.pedidos.show', $pedido->id) }}"
                        class="w-full bg-goool-green text-white text-center font-bold py-2.5 rounded-lg hover:bg-goool-green-dark transition inline-block">
                        <i class="fa-solid fa-eye"></i> Ver Detalle
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-motorcycle text-4xl mb-2"></i>
                <p>No tienes pedidos asignados.</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $pedidos->links() }}</div>
    </div>
@endsection
