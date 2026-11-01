{{-- Versión imprimible de cada informe; usa los mismos filtros y criterios que la pantalla y el Excel. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $nombreReporte }} - Pizza Goool</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #252525; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; line-height: 1.45; }
        h1 { color: #1b5e20; font-size: 21px; margin: 0; }
        h2 { border-left: 4px solid #1b5e20; color: #1b5e20; font-size: 12px; margin: 20px 0 0; padding: 6px 9px; text-transform: uppercase; }
        p { margin: 4px 0; }
        .header { border-bottom: 2px solid #1b5e20; margin-bottom: 14px; padding-bottom: 10px; }
        .header-table, .metrics { width: 100%; }
        .header-table td { vertical-align: middle; }
        .logo-cell { padding-right: 14px; width: 78px; }
        .logo-cell img { height: 58px; }
        .period { color: #555; text-align: right; }
        .metrics { border-collapse: collapse; margin: 10px 0; }
        .metrics td { background: #f8faf8; border: 1px solid #d8e2d8; padding: 9px; width: 25%; }
        .label { color: #64748b; font-size: 8px; text-transform: uppercase; }
        .value { color: #1b5e20; font-size: 15px; font-weight: bold; margin-top: 3px; }
        table.data { border-collapse: collapse; margin-top: 8px; width: 100%; }
        .data th { background: #e8eee8; border-bottom: 2px solid #1b5e20; font-size: 8px; text-align: left; text-transform: uppercase; }
        .data th, .data td { border-bottom: 1px solid #dfe5df; padding: 6px 5px; vertical-align: top; }
        .data tr:nth-child(even) { background: #f8faf8; }
        .right { text-align: right; }
        .empty { color: #64748b; padding: 12px; text-align: center; }
        .footer { border-top: 1px solid #ddd; bottom: -12px; color: #888; font-size: 8px; left: 0; position: fixed; right: 0; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('img/logo.png') }}" alt="Pizza Goool">
                </td>
                <td>
                    <h1>Pizza Goool</h1>
                    <p>{{ $nombreReporte }}</p>
                </td>
                <td class="period">
                    <strong>Período</strong><br>
                    {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al
                    {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </header>

    @if ($tipoReporte === 'resumen')
        <table class="metrics">
            <tr>
                <td><div class="label">Ventas completadas</div><div class="value">Bs. {{ number_format($resumen['ventas_completadas'], 2) }}</div></td>
                <td><div class="label">Pedidos entregados</div><div class="value">{{ number_format($resumen['pedidos_completados']) }}</div></td>
                <td><div class="label">Pedidos activos</div><div class="value">{{ number_format($resumen['pedidos_activos']) }}</div></td>
                <td><div class="label">Pedidos cancelados</div><div class="value">{{ number_format($resumen['pedidos_cancelados']) }}</div></td>
            </tr>
        </table>
        <table class="metrics">
            <tr>
                <td><div class="label">Ticket promedio</div><div class="value">Bs. {{ number_format($resumen['ticket_promedio'], 2) }}</div></td>
                <td><div class="label">Ventas período anterior</div><div class="value">Bs. {{ number_format($resumen['ventas_periodo_anterior'], 2) }}</div></td>
                <td colspan="2"><div class="label">Variación vs. período anterior</div><div class="value">{{ number_format($resumen['variacion_porcentual'], 2) }}%</div></td>
            </tr>
        </table>

        <h2>Ventas diarias</h2>
        <table class="data">
            <thead><tr><th>Fecha</th><th class="right">Pedidos entregados</th><th class="right">Ventas completadas</th></tr></thead>
            <tbody>
                @forelse ($ventasPorDia as $dia)
                    <tr><td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td><td class="right">{{ $dia->total_pedidos }}</td><td class="right">Bs. {{ number_format($dia->total_ventas, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty">No hay ventas completadas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
        <h2>Productos destacados</h2>
        <table class="data">
            <thead><tr><th>Producto</th><th>Categoría</th><th class="right">Unidades</th><th class="right">Ingresos</th></tr></thead>
            <tbody>
                @forelse ($productosDestacados as $producto)
                    <tr><td>{{ $producto->producto }}</td><td>{{ $producto->categoria }}</td><td class="right">{{ $producto->unidades_vendidas }}</td><td class="right">Bs. {{ number_format($producto->ingresos, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="empty">No hay productos vendidos en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($tipoReporte === 'productos')
        <h2>Ventas por categoría</h2>
        <table class="data">
            <thead><tr><th>Categoría</th><th class="right">Productos</th><th class="right">Unidades</th><th class="right">Ingresos completados</th></tr></thead>
            <tbody>
                @forelse ($ventasPorCategoria as $categoria)
                    <tr><td>{{ $categoria->categoria }}</td><td class="right">{{ $categoria->productos_vendidos }}</td><td class="right">{{ $categoria->unidades_vendidas }}</td><td class="right">Bs. {{ number_format($categoria->ingresos, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="empty">No hay ventas por categoría en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
        <h2>Productos más vendidos</h2>
        <table class="data">
            <thead><tr><th>Producto</th><th>Categoría</th><th class="right">Unidades</th><th class="right">Ingresos completados</th></tr></thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr><td>{{ $producto->producto }}</td><td>{{ $producto->categoria }}</td><td class="right">{{ $producto->unidades_vendidas }}</td><td class="right">Bs. {{ number_format($producto->ingresos, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="empty">No hay productos vendidos en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($tipoReporte === 'pedidos')
        <h2>Pedidos por estado</h2>
        <table class="data">
            <thead><tr><th>Estado</th><th class="right">Pedidos</th><th class="right">Monto de pedidos</th></tr></thead>
            <tbody>
                @forelse ($pedidosPorEstado as $fila)
                    <tr><td>{{ ucfirst(str_replace('_', ' ', $fila->estado)) }}</td><td class="right">{{ $fila->total_pedidos }}</td><td class="right">Bs. {{ number_format($fila->monto_asociado, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty">No hay pedidos en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
        <h2>Pedidos por modalidad de entrega</h2>
        <table class="data">
            <thead><tr><th>Modalidad</th><th class="right">Pedidos</th><th class="right">Ventas completadas</th></tr></thead>
            <tbody>
                @forelse ($pedidosPorTipo as $fila)
                    <tr><td>{{ $fila->tipo_entrega === 'contraentrega' ? 'Delivery' : 'Recojo en local' }}</td><td class="right">{{ $fila->total_pedidos }}</td><td class="right">Bs. {{ number_format($fila->ventas_completadas, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty">No hay pedidos en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($tipoReporte === 'pagos')
        <h2>Pagos por método y estado</h2>
        <table class="data">
            <thead><tr><th>Método</th><th>Estado registrado</th><th class="right">Registros</th><th class="right">Monto registrado</th></tr></thead>
            <tbody>
                @forelse ($pagosPorMetodoYEstado as $pago)
                    <tr><td>{{ $pago->metodo === 'qr' ? 'Código QR' : ucfirst($pago->metodo) }}</td><td>{{ ucfirst($pago->estado_pago) }}</td><td class="right">{{ $pago->total_pagos }}</td><td class="right">Bs. {{ number_format($pago->monto_registrado, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="empty">No hay pagos registrados en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($tipoReporte === 'detalle')
        <h2>Detalle de ventas completadas</h2>
        <table class="data">
            <thead><tr><th>Pedido y fecha</th><th>Cliente</th><th>Producto</th><th>Categoría</th><th class="right">Cantidad</th><th class="right">Precio unitario</th><th class="right">Subtotal</th><th>Estados de pago</th><th>Entrega</th></tr></thead>
            <tbody>
                @forelse ($detalleVentas as $fila)
                    <tr>
                        <td>#{{ $fila->pedido_id }}<br>{{ \Carbon\Carbon::parse($fila->fecha)->format('d/m/Y H:i') }}</td>
                        <td>{{ trim(($fila->cliente_nombres ?? '').' '.($fila->cliente_apellidos ?? '')) }}<br>{{ $fila->cliente_email }}</td>
                        <td>{{ $fila->producto }}</td>
                        <td>{{ $fila->categoria }}</td>
                        <td class="right">{{ $fila->cantidad }}</td>
                        <td class="right">Bs. {{ number_format($fila->precio_unitario, 2) }}</td>
                        <td class="right">Bs. {{ number_format($fila->subtotal, 2) }}</td>
                        <td>{{ $fila->estados_pago ? str_replace(',', ', ', $fila->estados_pago) : 'Sin registro' }}</td>
                        <td>{{ $fila->tipo_entrega === 'contraentrega' ? 'Delivery' : 'Recojo en local' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty">No hay ventas completadas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <footer class="footer">Generado el {{ now()->format('d/m/Y H:i') }} — Sistema web Pizza Goool</footer>
</body>
</html>
