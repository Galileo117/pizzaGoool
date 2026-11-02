{{-- Centro de reportes administrativos con filtros comunes y exportación del período seleccionado. --}}
@extends('layouts.admin')

@section('title', 'Reportes - Panel de Administración')
@section('page_title', 'Reportes')

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="mb-1 text-sm font-semibold uppercase tracking-wide text-goool-green">Administración</p>
            <h2 class="text-xl font-bold text-goool-green-dark">{{ $nombreReporte }}</h2>
            <p class="mt-1 text-sm text-gray-600">Consulta el desempeño de las ventas web y los pedidos del período.</p>
        </div>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:flex">
            <a href="{{ route('admin.reportes.pdf', ['reporte' => $tipoReporte, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-red-700">
                <i class="fa-solid fa-file-pdf"></i>
                Descargar PDF
            </a>
            <a href="{{ route('admin.reportes.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-goool-green px-4 py-3 font-bold text-white shadow-sm transition hover:bg-goool-green-dark">
                <i class="fa-solid fa-file-excel"></i>
                Descargar Excel
            </a>
        </div>
    </div>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-6" aria-label="Filtros de reportes">
        <form method="GET" action="{{ route('admin.reportes.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <input type="hidden" name="reporte" value="{{ $tipoReporte }}">
            <div>
                <label for="fecha_inicio" class="mb-1 block text-sm font-semibold text-gray-700">Desde</label>
                <input id="fecha_inicio" type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                    class="min-w-0 w-full rounded-lg border border-gray-300 p-3 focus:border-goool-green focus:outline-none focus:ring-2 focus:ring-goool-green/20">
            </div>
            <div>
                <label for="fecha_fin" class="mb-1 block text-sm font-semibold text-gray-700">Hasta</label>
                <input id="fecha_fin" type="date" name="fecha_fin" value="{{ $fechaFin }}"
                    class="min-w-0 w-full rounded-lg border border-gray-300 p-3 focus:border-goool-green focus:outline-none focus:ring-2 focus:ring-goool-green/20">
            </div>
            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-goool-green px-4 py-3 font-bold text-white transition hover:bg-goool-green-dark">
                <i class="fa-solid fa-filter"></i>
                Aplicar filtros
            </button>
            <a href="{{ route('admin.reportes.index', ['reporte' => $tipoReporte]) }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 font-semibold text-gray-700 transition hover:bg-gray-100">
                <i class="fa-solid fa-rotate-left"></i>
                Restablecer
            </a>
        </form>
        @if ($errors->any())
            <p class="mt-3 text-sm font-medium text-red-600">{{ $errors->first() }}</p>
        @endif
    </section>

    @php
        $iconosReportes = [
            'resumen' => 'fa-chart-line',
            'productos' => 'fa-pizza-slice',
            'pedidos' => 'fa-receipt',
            'pagos' => 'fa-credit-card',
            'detalle' => 'fa-list-check',
        ];
    @endphp
    <nav class="mb-6 grid grid-cols-1 gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5"
        aria-label="Tipos de reportes">
        @foreach ($reportesDisponibles as $clave => $etiqueta)
            <a href="{{ route('admin.reportes.index', ['reporte' => $clave, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                @class([
                    'inline-flex min-w-0 items-center justify-center gap-2 rounded-lg px-3 py-3 text-center text-sm font-bold transition sm:px-4',
                    'bg-goool-green text-white shadow-sm' => $tipoReporte === $clave,
                    'text-gray-600 hover:bg-gray-100 hover:text-goool-green-dark' => $tipoReporte !== $clave,
                ])
                @if ($tipoReporte === $clave) aria-current="page" @endif>
                <i class="fa-solid {{ $iconosReportes[$clave] }}"></i>
                {{ $etiqueta }}
            </a>
        @endforeach
    </nav>

    @if ($tipoReporte === 'resumen')
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
            @foreach ([
                ['Ventas Totales', 'Bs. '.number_format($resumen['ventas_completadas'], 2), 'fa-money-bill-wave', 'border-goool-green', 'text-goool-green-dark', 'bg-goool-green-light/30'],
                ['Envíos cobrados', 'Bs. '.number_format($resumen['costos_envio_completados'], 2), 'fa-truck-fast', 'border-goool-orange', 'text-goool-orange', 'bg-orange-100'],
                ['Pedidos Entregados', number_format($resumen['pedidos_completados']), 'fa-receipt', 'border-blue-500', 'text-blue-600', 'bg-blue-100'],
                ['Pedidos Activos', number_format($resumen['pedidos_activos']), 'fa-clock', 'border-goool-yellow', 'text-yellow-600', 'bg-yellow-100'],
                ['Pedidos Cancelados', number_format($resumen['pedidos_cancelados']), 'fa-circle-xmark', 'border-red-500', 'text-red-600', 'bg-red-100'],
            ] as [$etiqueta, $valor, $icono, $borde, $color, $fondo])
                <article class="min-w-0 rounded-xl border border-gray-200 border-l-4 {{ $borde }} bg-white p-4 shadow-md sm:p-5">
                    <div class="relative min-h-20 min-w-0">
                        <p class="break-words pr-12 text-xs font-semibold text-gray-500 sm:text-sm">{{ $etiqueta }}</p>
                        <p class="mt-3 break-words text-2xl font-bold leading-tight {{ $color }} sm:text-2xl 2xl:text-3xl">{{ $valor }}</p>
                        <span class="absolute right-0 top-0 flex h-10 w-10 items-center justify-center rounded-full {{ $fondo }} {{ $color }} sm:h-12 sm:w-12">
                            <i class="fa-solid {{ $icono }} text-xl sm:text-2xl"></i>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mb-6 grid min-w-0 grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-chart-line mr-2"></i>VENTAS POR DÍA</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="relative h-64 min-w-0">
                        <canvas id="graficoVentas" aria-label="Gráfico de ventas completadas por día"></canvas>
                    </div>
                </div>
            </section>
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-chart-bar mr-2"></i>PRODUCTOS MÁS VENDIDOS</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="relative h-64 min-w-0">
                        <canvas id="graficoProductos" aria-label="Gráfico de unidades vendidas por producto"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <div class="mb-6 grid min-w-0 grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-chart-pie mr-2"></i>COMPARACIÓN DEL PERÍODO</h3>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-6">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Ticket promedio</dt>
                        <dd class="mt-1 text-xl font-bold text-gray-800">Bs. {{ number_format($resumen['ticket_promedio'], 2) }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm text-gray-500">Período anterior</dt>
                        <dd class="mt-1 text-xl font-bold text-gray-800">Bs. {{ number_format($resumen['ventas_periodo_anterior'], 2) }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 sm:col-span-2">
                        <dt class="text-sm text-gray-500">Variación frente al período anterior</dt>
                        <dd class="mt-1 text-xl font-bold {{ $resumen['variacion_porcentual'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($resumen['variacion_porcentual'], 2) }}%
                        </dd>
                    </div>
                </dl>
            </section>
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-trophy mr-2"></i>TOP PRODUCTOS</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3">Producto</th>
                                <th class="px-5 py-3 text-right">Unidades</th>
                                <th class="px-5 py-3 text-right">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($productosDestacados as $producto)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $producto->producto }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($producto->unidades_vendidas) }}</td>
                                    <td class="px-5 py-3 text-right font-semibold">Bs. {{ number_format($producto->ingresos, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No hay ventas completadas en este período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
            <div class="bg-goool-green px-4 py-3 sm:px-6">
                <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-calendar-days mr-2"></i>DETALLE DIARIO DE VENTAS</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-5 py-3">Fecha</th><th class="px-5 py-3 text-right">Pedidos entregados</th><th class="px-5 py-3 text-right">Ventas completadas</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($ventasPorDia as $dia)
                            <tr>
                                <td class="px-5 py-3">{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-right">{{ number_format($dia->total_pedidos) }}</td>
                                <td class="px-5 py-3 text-right font-semibold">Bs. {{ number_format($dia->total_ventas, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No hay ventas completadas en este período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($tipoReporte === 'productos')
        <div class="mb-6 grid min-w-0 grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-tags mr-2"></i>VENTAS POR CATEGORÍA</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-5 py-3">Categoría</th><th class="px-5 py-3 text-right">Productos</th><th class="px-5 py-3 text-right">Unidades</th><th class="px-5 py-3 text-right">Ingresos</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ventasPorCategoria as $categoria)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $categoria->categoria }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($categoria->productos_vendidos) }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($categoria->unidades_vendidas) }}</td>
                                    <td class="px-5 py-3 text-right font-semibold">Bs. {{ number_format($categoria->ingresos, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No hay ventas por categoría en este período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6">
                    <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-pizza-slice mr-2"></i>PRODUCTOS MÁS VENDIDOS</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-5 py-3">Producto</th><th class="px-5 py-3">Categoría</th><th class="px-5 py-3 text-right">Unidades</th><th class="px-5 py-3 text-right">Ingresos</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($productos as $producto)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $producto->producto }}</td>
                                    <td class="px-5 py-3">{{ $producto->categoria }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($producto->unidades_vendidas) }}</td>
                                    <td class="px-5 py-3 text-right font-semibold">Bs. {{ number_format($producto->ingresos, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No hay productos vendidos en este período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 px-5 py-3">{{ $productos->links() }}</div>
            </section>
        </div>
    @elseif ($tipoReporte === 'pedidos')
        <div class="mb-6 grid min-w-0 grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6"><h3 class="text-sm font-bold text-white"><i class="fa-solid fa-list-check mr-2"></i>PEDIDOS POR ESTADO</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-5 py-3">Estado</th><th class="px-5 py-3 text-right">Pedidos</th><th class="px-5 py-3 text-right">Monto de pedidos</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pedidosPorEstado as $fila)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ ucfirst(str_replace('_', ' ', $fila->estado)) }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($fila->total_pedidos) }}</td>
                                    <td class="px-5 py-3 text-right">Bs. {{ number_format($fila->monto_asociado, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No hay pedidos en este período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="bg-goool-green px-4 py-3 sm:px-6"><h3 class="text-sm font-bold text-white"><i class="fa-solid fa-truck-fast mr-2"></i>MODALIDAD DE ENTREGA</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-5 py-3">Modalidad</th><th class="px-5 py-3 text-right">Pedidos</th><th class="px-5 py-3 text-right">Ventas completadas</th><th class="px-5 py-3 text-right">Envíos cobrados</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pedidosPorTipo as $fila)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $fila->tipo_entrega === 'delivery' ? 'Delivery' : 'Recojo en local' }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($fila->total_pedidos) }}</td>
                                    <td class="px-5 py-3 text-right">Bs. {{ number_format($fila->ventas_completadas, 2) }}</td>
                                    <td class="px-5 py-3 text-right">Bs. {{ number_format($fila->costos_envio, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No hay pedidos en este período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    @elseif ($tipoReporte === 'pagos')
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
            <div class="bg-goool-green px-4 py-3 sm:px-6"><h3 class="text-sm font-bold text-white"><i class="fa-solid fa-credit-card mr-2"></i>PAGOS POR MÉTODO Y ESTADO</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-5 py-3">Método</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3 text-right">Registros</th><th class="px-5 py-3 text-right">Monto registrado</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pagosPorMetodoYEstado as $pago)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $pago->metodo === 'qr' ? 'Código QR' : ucfirst($pago->metodo) }}</td>
                                <td class="px-5 py-3">{{ ucfirst($pago->estado_pago) }}</td>
                                <td class="px-5 py-3 text-right">{{ number_format($pago->total_pagos) }}</td>
                                <td class="px-5 py-3 text-right font-semibold">Bs. {{ number_format($pago->monto_registrado, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No hay pagos registrados en este período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($tipoReporte === 'detalle')
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
            <div class="bg-goool-green px-4 py-3 sm:px-6"><h3 class="text-sm font-bold text-white"><i class="fa-solid fa-file-lines mr-2"></i>DETALLE DE VENTAS</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Pedido / fecha</th><th class="px-4 py-3">Cliente</th><th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-right">Cantidad</th><th class="px-4 py-3 text-right">Precio unitario</th>
                            <th class="px-4 py-3 text-right">Subtotal</th><th class="px-4 py-3">Pago registrado</th><th class="px-4 py-3">Entrega</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($detalleVentas as $fila)
                            <tr>
                                <td class="px-4 py-3">#{{ $fila->pedido_id }}<br><span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($fila->fecha)->format('d/m/Y H:i') }}</span></td>
                                <td class="px-4 py-3">{{ trim(($fila->cliente_nombres ?? '').' '.($fila->cliente_apellidos ?? '')) ?: 'Cliente' }}<br><span class="text-xs text-gray-500">{{ $fila->cliente_email }}</span></td>
                                <td class="px-4 py-3">{{ $fila->producto }}<br><span class="text-xs text-gray-500">{{ $fila->categoria }}</span></td>
                                <td class="px-4 py-3 text-right">{{ number_format($fila->cantidad) }}</td>
                                <td class="px-4 py-3 text-right">Bs. {{ number_format($fila->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Bs. {{ number_format($fila->subtotal, 2) }}</td>
                                <td class="px-4 py-3">{{ $fila->estados_pago ? str_replace(',', ', ', $fila->estados_pago) : 'Sin registro' }}</td>
                                <td class="px-4 py-3">{{ in_array($fila->tipo_entrega, ['delivery', 'contraentrega'], true) ? 'Delivery' : 'Recojo en local' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay ventas completadas en este período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-5 py-3">{{ $detalleVentas->links() }}</div>
        </section>
    @endif

    @if ($tipoReporte === 'resumen')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ventasDiarias = @json($ventasPorDia);
            const productosGrafico = @json($productosDestacados);
            const ventasChart = document.getElementById('graficoVentas');
            const productosChart = document.getElementById('graficoProductos');

            if (ventasChart) {
                new Chart(ventasChart, {
                    type: 'line',
                    data: {
                        labels: ventasDiarias.map((fila) => fila.fecha),
                        datasets: [{
                            label: 'Ventas completadas (Bs.)',
                            data: ventasDiarias.map((fila) => Number(fila.total_ventas)),
                            borderColor: '#1b5e20',
                            backgroundColor: 'rgba(27, 94, 32, 0.12)',
                            fill: true,
                            tension: 0.3,
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } },
                });
            }

            if (productosChart) {
                new Chart(productosChart, {
                    type: 'bar',
                    data: {
                        labels: productosGrafico.map((fila) => fila.producto),
                        datasets: [{
                            label: 'Unidades vendidas',
                            data: productosGrafico.map((fila) => Number(fila.unidades_vendidas)),
                            backgroundColor: '#1b5e20',
                            borderRadius: 6,
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                });
            }
        </script>
    @endif
@endsection
