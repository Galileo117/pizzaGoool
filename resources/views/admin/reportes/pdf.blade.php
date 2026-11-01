{{-- Plantilla imprimible del reporte; conservar sus datos en sincronía con el reporte HTML. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas - Pizza Goool</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.5;
        }
        
        /* Cabecera */
        .header {
            border-bottom: 3px solid #1B5E20;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 90px;
        }
        .logo-cell img {
            height: 70px;
        }
        .title-cell h1 {
            color: #1B5E20;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .title-cell p {
            margin: 3px 0 0;
            color: #666;
            font-size: 11px;
        }
        .period-cell {
            text-align: right;
            color: #666;
            font-size: 10px;
        }
        .period-cell strong {
            color: #222;
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        /* Sección de título */
        .section-title {
            background: #f0f0f0;
            color: #1B5E20;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 18px;
            margin-bottom: 0;
            border-left: 4px solid #1B5E20;
        }
        
        /* Métricas */
        .metricas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .metricas-table td {
            width: 33.33%;
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            vertical-align: middle;
        }
        .metrica-label {
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .metrica-value {
            color: #1B5E20;
            font-size: 18px;
            font-weight: bold;
        }
        
        /* Tablas */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #d0d0d0;
        }
        table.data thead th {
            background: #e8e8e8;
            color: #222;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #1B5E20;
        }
        table.data thead th.text-center {
            text-align: center;
        }
        table.data thead th.text-right {
            text-align: right;
        }
        table.data tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            font-size: 10.5px;
        }
        table.data tbody td.text-center {
            text-align: center;
        }
        table.data tbody td.text-right {
            text-align: right;
        }
        table.data tbody td.text-bold {
            font-weight: bold;
            color: #1B5E20;
        }
        table.data tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        /* Bloque total final */
        .total-final {
            margin-top: 20px;
            border: 2px solid #1B5E20;
            background: #f9f9f9;
            padding: 12px 20px;
        }
        .total-final table {
            width: 100%;
        }
        .total-final td {
            padding: 3px 0;
        }
        .total-final .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
            font-weight: bold;
        }
        .total-final .value {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E20;
            text-align: right;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .sin-datos {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }

        /* Nota explicativa */
        .nota {
            font-size: 9px;
            color: #777;
            margin-top: 3px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('img/logo.png') }}" alt="Pizza Goool">
                </td>
                <td class="title-cell">
                    <h1>Pizza Goool</h1>
                    <p>Reporte de Ventas</p>
                </td>
                <td class="period-cell">
                    <strong>Período del Reporte</strong>
                    {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- MÉTRICAS PRINCIPALES -->
    <div class="section-title">Resumen General</div>
    <table class="metricas-table">
        <tr>
            <td>
                <div class="metrica-label">Ventas Totales</div>
                <div class="metrica-value">Bs. {{ number_format($totalVentas, 2) }}</div>
            </td>
            <td>
                <div class="metrica-label">Total Pedidos</div>
                <div class="metrica-value">{{ $totalPedidos }}</div>
            </td>
            <td>
                <div class="metrica-label">Promedio por Pedido</div>
                <div class="metrica-value">Bs. {{ number_format($ticketPromedio, 2) }}</div>
                <div class="nota">Monto promedio que gasta cada cliente</div>
            </td>
        </tr>
    </table>

    <!-- PRODUCTOS MÁS VENDIDOS -->
    <div class="section-title">Productos Más Vendidos</div>
    <table class="data">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Unidades Vendidas</th>
                <th class="text-right">Ingresos Generados</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosMasVendidos as $item)
                <tr>
                    <td>{{ $item->producto->nombre ?? 'Producto eliminado' }}</td>
                    <td class="text-center">{{ $item->total_vendido }}</td>
                    <td class="text-right text-bold">Bs. {{ number_format($item->total_ingresos, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="sin-datos">No hay datos en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- MÉTODOS DE PAGO -->
    <div class="section-title">Ventas por Método de Pago</div>
    <table class="data">
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th class="text-center">Cantidad de Pedidos</th>
                <th class="text-right">Total Recaudado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorMetodo as $metodo)
                <tr>
                    <td>{{ $metodo->metodo_pago === 'qr' ? 'Código QR' : 'Efectivo' }}</td>
                    <td class="text-center">{{ $metodo->total_pedidos }}</td>
                    <td class="text-right text-bold">Bs. {{ number_format($metodo->total_ventas, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="sin-datos">No hay datos en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- DETALLE POR DÍA -->
    <div class="section-title">Detalle Diario de Ventas</div>
    <table class="data">
        <thead>
            <tr>
                <th>Fecha</th>
                <th class="text-center">Pedidos Realizados</th>
                <th class="text-right">Ventas del Día</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorDia as $dia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $dia->total_pedidos }}</td>
                    <td class="text-right text-bold">Bs. {{ number_format($dia->total_ventas, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="sin-datos">No hay datos en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTAL FINAL -->
    <div class="total-final">
        <table>
            <tr>
                <td class="label">Total General del Período</td>
                <td class="value">Bs. {{ number_format($totalVentas, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Reporte generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }} — Sistema Web Pizza Goool
    </div>

</body>
</html>