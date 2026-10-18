@extends('layouts.admin')

@section('title', 'Reportes de Ventas - Panel de Administración')
@section('page_title', 'Reportes de Ventas')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Reportes de Ventas</h2>
            <p class="text-gray-600 text-sm">Analiza las ventas del negocio por rango de fechas.</p>
        </div>
        <a href="{{ route('admin.reportes.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
            target="_blank"
            class="bg-red-500 text-white font-bold px-5 py-3 rounded-lg hover:bg-red-600 transition flex items-center gap-2 w-full sm:w-auto justify-center shadow-sm">
            <i class="fa-solid fa-file-pdf"></i> Exportar PDF
        </a>
    </div>

    <!-- Filtro por fechas -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200 mb-6">
        <form method="GET" action="{{ route('admin.reportes.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
            </div>
            <div>
                <button type="submit"
                    class="w-full bg-goool-green text-white font-bold py-3 rounded-lg hover:bg-goool-green-dark transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
            <div>
                {{-- Botón de limpiar filtros --}}
                <a href="{{ route('admin.reportes.index') }}"
                    class="w-full bg-gray-100 text-gray-700 border border-gray-200 font-bold py-3 rounded-lg hover:bg-gray-200 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Métricas principales -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-goool-green">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm font-semibold">Ventas Totales</p>
                    <p class="text-2xl sm:text-3xl font-bold text-goool-green-dark">Bs. {{ number_format($totalVentas, 2) }}
                    </p>
                </div>
                <div class="bg-goool-green-light/30 p-2 sm:p-3 rounded-full flex-shrink-0">
                    <i class="fa-solid fa-money-bill-wave text-xl sm:text-2xl text-goool-green"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm font-semibold">Total Pedidos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $totalPedidos }}</p>
                </div>
                <div class="bg-blue-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                    <i class="fa-solid fa-receipt text-xl sm:text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-l-4 border-goool-yellow">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm font-semibold">Promedio por Pedido</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-600">Bs. {{ number_format($ticketPromedio, 2) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 italic">Monto promedio por cliente</p>
                </div>
                <div class="bg-yellow-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-xl sm:text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="bg-goool-green px-4 sm:px-6 py-3">
                <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-chart-line"></i> VENTAS POR DÍA</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="h-64 relative">
                    <canvas id="graficoVentas"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="bg-goool-green px-4 sm:px-6 py-3">
                <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-chart-bar"></i> PRODUCTOS MÁS VENDIDOS</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="h-64 relative">
                    <canvas id="graficoProductos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos más vendidos (tabla) y métodos de pago -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Productos más vendidos -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="bg-goool-green px-4 sm:px-6 py-3">
                <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-trophy"></i> TOP PRODUCTOS</h3>
            </div>
            <div class="p-4 sm:p-6">
                @forelse($productosMasVendidos as $item)
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div
                                class="bg-goool-green-light/30 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-pizza-slice text-goool-green"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">
                                    {{ $item->producto->nombre ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->total_vendido }} unidades vendidas</p>
                            </div>
                        </div>
                        <span class="font-bold text-goool-green text-sm whitespace-nowrap ml-2">Bs.
                            {{ number_format($item->total_ingresos, 2) }}</span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-4">No hay ventas en este período.</p>
                @endforelse
            </div>
        </div>

        <!-- Métodos de pago -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="bg-goool-green px-4 sm:px-6 py-3">
                <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-credit-card"></i> MÉTODOS DE PAGO</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="h-64 relative">
                    <canvas id="graficoMetodos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle por día -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="bg-goool-green px-4 sm:px-6 py-3">
            <h3 class="text-white font-bold text-sm"><i class="fa-solid fa-calendar"></i> DETALLE POR DÍA</h3>
        </div>

        {{-- Tabla (escritorio) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Pedidos</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Total Ventas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ventasPorDia as $dia)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                <i class="fa-solid fa-calendar-day text-goool-green"></i>
                                {{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">{{ $dia->total_pedidos }}</td>
                            <td class="px-4 py-3 text-right font-bold text-goool-green">Bs.
                                {{ number_format($dia->total_ventas, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-chart-line text-4xl mb-2"></i>
                                <p>No hay ventas en este período.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tarjetas (móvil) --}}
        <div class="md:hidden p-4 space-y-3">
            @forelse($ventasPorDia as $dia)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">
                            <i class="fa-solid fa-calendar-day text-goool-green"></i>
                            {{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $dia->total_pedidos }} pedidos</p>
                    </div>
                    <span class="font-bold text-goool-green text-sm">Bs. {{ number_format($dia->total_ventas, 2) }}</span>
                </div>
            @empty
                <div class="text-center text-gray-400 py-8">
                    <i class="fa-solid fa-chart-line text-4xl mb-2"></i>
                    <p>No hay ventas en este período.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ventasPorDia = @json($ventasPorDia);
        const productosMasVendidos = @json($productosMasVendidos);
        const ventasPorMetodo = @json($ventasPorMetodo);

        // Gráfico de ventas por día (CON FECHAS CORREGIDAS)
        const ctxVentas = document.getElementById('graficoVentas').getContext('2d');
        new Chart(ctxVentas, {
            type: 'line',
            data: {
                // Se agrega T12:00:00 para evitar desfase por zona horaria
                labels: ventasPorDia.map(v => {
                    const fecha = new Date(v.fecha + 'T12:00:00');
                    return fecha.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit'
                    });
                }).reverse(),
                datasets: [{
                    label: 'Ventas (Bs.)',
                    data: ventasPorDia.map(v => v.total_ventas).reverse(),
                    borderColor: '#1B5E20',
                    backgroundColor: 'rgba(27, 94, 32, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#FBC02D',
                    pointBorderColor: '#1B5E20',
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Bs. ' + value;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de productos más vendidos
        const ctxProductos = document.getElementById('graficoProductos').getContext('2d');
        new Chart(ctxProductos, {
            type: 'bar',
            data: {
                labels: productosMasVendidos.map(p => p.producto ? p.producto.nombre : 'Eliminado'),
                datasets: [{
                    label: 'Unidades vendidas',
                    data: productosMasVendidos.map(p => p.total_vendido),
                    backgroundColor: ['#1B5E20', '#2E7D32', '#FBC02D', '#F57C00', '#9C27B0'],
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de métodos de pago
        const ctxMetodos = document.getElementById('graficoMetodos').getContext('2d');
        new Chart(ctxMetodos, {
            type: 'doughnut',
            data: {
                labels: ventasPorMetodo.map(m => m.metodo_pago === 'qr' ? 'Código QR' : 'Efectivo'),
                datasets: [{
                    data: ventasPorMetodo.map(m => m.total_ventas),
                    backgroundColor: ['#1B5E20', '#FBC02D'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
