<?php

namespace App\Services\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportesService
{
    /**
     * Resume pedidos y ventas completadas para un rango de fechas.
     *
     * @return array<string, int|float>
     */
    public function resumen(string $fechaInicio, string $fechaFin): array
    {
        $pedidos = $this->pedidosEnPeriodo($fechaInicio, $fechaFin);
        $totales = (clone $pedidos)
            ->selectRaw('COUNT(*) AS pedidos_registrados')
            ->selectRaw("SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) AS pedidos_cancelados")
            ->selectRaw("SUM(CASE WHEN estado NOT IN ('entregado', 'cancelado') THEN 1 ELSE 0 END) AS pedidos_activos")
            ->first();
        $ventas = $this->pedidosEntregadosEnPeriodo($fechaInicio, $fechaFin)
            ->selectRaw('COUNT(*) AS pedidos_completados')
            ->selectRaw('SUM(total) AS ventas_completadas')
            ->selectRaw('SUM(costo_envio) AS costos_envio_completados')
            ->first();

        $duracionEnDias = (int) CarbonImmutable::parse($fechaInicio)
            ->diffInDays(CarbonImmutable::parse($fechaFin)) + 1;
        $periodoAnteriorFin = CarbonImmutable::parse($fechaInicio)->subDay()->toDateString();
        $periodoAnteriorInicio = CarbonImmutable::parse($fechaInicio)->subDays($duracionEnDias)->toDateString();
        $ventasAnteriores = $this->ventasCompletadas($periodoAnteriorInicio, $periodoAnteriorFin);
        $ventasActuales = (float) ($ventas->ventas_completadas ?? 0);

        return [
            'pedidos_registrados' => (int) ($totales->pedidos_registrados ?? 0),
            'pedidos_completados' => (int) ($ventas->pedidos_completados ?? 0),
            'pedidos_cancelados' => (int) ($totales->pedidos_cancelados ?? 0),
            'pedidos_activos' => (int) ($totales->pedidos_activos ?? 0),
            'ventas_completadas' => $ventasActuales,
            'costos_envio_completados' => (float) ($ventas->costos_envio_completados ?? 0),
            'ticket_promedio' => $this->pedidosEntregadosEnPeriodo($fechaInicio, $fechaFin)->avg('total') ?? 0,
            'ventas_periodo_anterior' => $ventasAnteriores,
            'variacion_porcentual' => $ventasAnteriores > 0
                ? (($ventasActuales - $ventasAnteriores) / $ventasAnteriores) * 100
                : 0,
        ];
    }

    public function ventasDiarias(string $fechaInicio, string $fechaFin): Builder
    {
        return $this->pedidosEntregadosEnPeriodo($fechaInicio, $fechaFin)
            ->selectRaw('DATE(created_at) AS fecha')
            ->selectRaw('COUNT(*) AS total_pedidos')
            ->selectRaw('SUM(total) AS total_ventas')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->orderBy('total_pedidos');
    }

    public function ventasDiariasParaGrafico(string $fechaInicio, string $fechaFin): Collection
    {
        $ventasPorFecha = $this->ventasDiarias($fechaInicio, $fechaFin)->get()->keyBy('fecha');
        $fecha = CarbonImmutable::parse($fechaInicio)->startOfDay();
        $fechaFin = CarbonImmutable::parse($fechaFin)->startOfDay();
        $serie = collect();

        while ($fecha->lessThanOrEqualTo($fechaFin)) {
            $fechaFormateada = $fecha->toDateString();
            $venta = $ventasPorFecha->get($fechaFormateada);

            $serie->push([
                'fecha' => $fechaFormateada,
                'total_pedidos' => (int) ($venta->total_pedidos ?? 0),
                'total_ventas' => (float) ($venta->total_ventas ?? 0),
            ]);

            $fecha = $fecha->addDay();
        }

        return $serie;
    }

    public function productosMasVendidos(string $fechaInicio, string $fechaFin): Builder
    {
        return DB::table('detalle_pedidos')
            ->join('pedidos', 'pedidos.id', '=', 'detalle_pedidos.pedido_id')
            ->where('pedidos.estado', 'entregado')
            ->whereBetween('pedidos.created_at', $this->rangoFecha($fechaInicio, $fechaFin))
            ->select(
                'detalle_pedidos.producto_nombre AS producto',
                'detalle_pedidos.categoria_nombre AS categoria',
            )
            ->selectRaw('SUM(detalle_pedidos.cantidad) AS unidades_vendidas')
            ->selectRaw('SUM(detalle_pedidos.cantidad * detalle_pedidos.precio_unitario) AS ingresos')
            ->groupBy('detalle_pedidos.producto_nombre', 'detalle_pedidos.categoria_nombre')
            ->orderByDesc('unidades_vendidas')
            ->orderBy('detalle_pedidos.producto_nombre');
    }

    public function ventasPorCategoria(string $fechaInicio, string $fechaFin): Builder
    {
        return DB::table('detalle_pedidos')
            ->join('pedidos', 'pedidos.id', '=', 'detalle_pedidos.pedido_id')
            ->where('pedidos.estado', 'entregado')
            ->whereBetween('pedidos.created_at', $this->rangoFecha($fechaInicio, $fechaFin))
            ->select('detalle_pedidos.categoria_nombre AS categoria')
            ->selectRaw('COUNT(DISTINCT detalle_pedidos.producto_id) AS productos_vendidos')
            ->selectRaw('SUM(detalle_pedidos.cantidad) AS unidades_vendidas')
            ->selectRaw('SUM(detalle_pedidos.cantidad * detalle_pedidos.precio_unitario) AS ingresos')
            ->groupBy('detalle_pedidos.categoria_nombre')
            ->orderByDesc('ingresos')
            ->orderBy('detalle_pedidos.categoria_nombre');
    }

    public function pedidosPorEstado(string $fechaInicio, string $fechaFin): Builder
    {
        return $this->pedidosEnPeriodo($fechaInicio, $fechaFin)
            ->select('estado')
            ->selectRaw('COUNT(*) AS total_pedidos')
            ->selectRaw('SUM(total) AS monto_asociado')
            ->groupBy('estado')
            ->orderBy('estado');
    }

    public function pedidosPorTipoEntrega(string $fechaInicio, string $fechaFin): Builder
    {
        return $this->pedidosEntregadosEnPeriodo($fechaInicio, $fechaFin)
            ->selectRaw("CASE WHEN tipo_entrega IN ('delivery', 'contraentrega') THEN 'delivery' ELSE 'local' END AS tipo_entrega")
            ->selectRaw('COUNT(*) AS total_pedidos')
            ->selectRaw('SUM(total) AS ventas_completadas')
            ->selectRaw('SUM(costo_envio) AS costos_envio')
            ->groupByRaw("CASE WHEN tipo_entrega IN ('delivery', 'contraentrega') THEN 'delivery' ELSE 'local' END")
            ->orderBy('tipo_entrega');
    }

    public function pagosPorMetodoYEstado(string $fechaInicio, string $fechaFin): Builder
    {
        return DB::table('pagos')
            ->join('pedidos', 'pedidos.id', '=', 'pagos.pedido_id')
            ->whereBetween('pedidos.created_at', $this->rangoFecha($fechaInicio, $fechaFin))
            ->select('pagos.metodo', 'pagos.estado_pago')
            ->selectRaw('COUNT(*) AS total_pagos')
            ->selectRaw('SUM(pagos.monto) AS monto_registrado')
            ->groupBy('pagos.metodo', 'pagos.estado_pago')
            ->orderBy('pagos.metodo')
            ->orderBy('pagos.estado_pago');
    }

    public function detalleVentas(string $fechaInicio, string $fechaFin): Builder
    {
        $pagosPorPedido = DB::table('pagos')
            ->select('pedido_id')
            ->selectRaw('GROUP_CONCAT(DISTINCT estado_pago) AS estados_pago')
            ->groupBy('pedido_id');

        return DB::table('pedidos')
            ->join('detalle_pedidos', 'pedidos.id', '=', 'detalle_pedidos.pedido_id')
            ->leftJoinSub($pagosPorPedido, 'pagos_resumen', 'pagos_resumen.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', 'entregado')
            ->whereBetween('pedidos.created_at', $this->rangoFecha($fechaInicio, $fechaFin))
            ->select(
                'pedidos.id AS pedido_id',
                'pedidos.created_at AS fecha',
                'pedidos.cliente_nombre',
                'pedidos.cliente_email',
                'detalle_pedidos.producto_nombre AS producto',
                'detalle_pedidos.categoria_nombre AS categoria',
                'detalle_pedidos.cantidad',
                'detalle_pedidos.precio_unitario',
                'pedidos.metodo_pago',
                'pedidos.tipo_entrega',
                'pagos_resumen.estados_pago',
            )
            ->selectRaw('(detalle_pedidos.cantidad * detalle_pedidos.precio_unitario) AS subtotal')
            ->orderBy('pedidos.created_at')
            ->orderBy('pedidos.id')
            ->orderBy('detalle_pedidos.id');
    }

    /**
     * @return Collection<int, array{indicador: string, valor: int|float|string}>
     */
    public function resumenParaExcel(string $fechaInicio, string $fechaFin): Collection
    {
        $resumen = $this->resumen($fechaInicio, $fechaFin);

        return collect([
            ['indicador' => 'Período', 'valor' => $fechaInicio.' al '.$fechaFin],
            ['indicador' => 'Ventas completadas', 'valor' => $resumen['ventas_completadas']],
            ['indicador' => 'Pedidos registrados', 'valor' => $resumen['pedidos_registrados']],
            ['indicador' => 'Pedidos entregados', 'valor' => $resumen['pedidos_completados']],
            ['indicador' => 'Pedidos activos', 'valor' => $resumen['pedidos_activos']],
            ['indicador' => 'Pedidos cancelados', 'valor' => $resumen['pedidos_cancelados']],
            ['indicador' => 'Ticket promedio', 'valor' => $resumen['ticket_promedio']],
            ['indicador' => 'Ventas del período anterior', 'valor' => $resumen['ventas_periodo_anterior']],
            ['indicador' => 'Variación vs. período anterior (%)', 'valor' => $resumen['variacion_porcentual']],
            ['indicador' => 'Costos de envío cobrados', 'valor' => $resumen['costos_envio_completados']],
        ]);
    }

    public function ventasCompletadas(string $fechaInicio, string $fechaFin): float
    {
        return (float) $this->pedidosEntregadosEnPeriodo($fechaInicio, $fechaFin)->sum('total');
    }

    private function pedidosEnPeriodo(string $fechaInicio, string $fechaFin): Builder
    {
        return DB::table('pedidos')->whereBetween('created_at', $this->rangoFecha($fechaInicio, $fechaFin));
    }

    private function pedidosEntregadosEnPeriodo(string $fechaInicio, string $fechaFin): Builder
    {
        return $this->pedidosEnPeriodo($fechaInicio, $fechaFin)
            ->where('estado', 'entregado');
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function rangoFecha(string $fechaInicio, string $fechaFin): array
    {
        return [
            CarbonImmutable::parse($fechaInicio)->startOfDay()->toDateTimeString(),
            CarbonImmutable::parse($fechaFin)->endOfDay()->toDateTimeString(),
        ];
    }
}
