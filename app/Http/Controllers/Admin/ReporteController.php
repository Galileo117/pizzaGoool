<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas (por defecto: últimos 30 días)
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        // Pedidos válidos (excluye cancelados)
        $pedidosValidos = Pedido::whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado']);

        // Métricas principales
        $totalVentas = (clone $pedidosValidos)->sum('total');
        $totalPedidos = (clone $pedidosValidos)->count();
        $ticketPromedio = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;

        // Productos más vendidos
        $productosMasVendidos = DetallePedido::select(
            'producto_id',
            DB::raw('SUM(cantidad) as total_vendido'),
            DB::raw('SUM(cantidad * precio_unitario) as total_ingresos')
        )
            ->whereHas('pedido', function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
                    ->whereNotIn('estado', ['cancelado']);
            })
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get();

        // Ventas por día
        $ventasPorDia = Pedido::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as total_ventas')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'desc')
            ->get();

        // Ventas por método de pago
        $ventasPorMetodo = Pedido::select(
            'metodo_pago',
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as total_ventas')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado'])
            ->groupBy('metodo_pago')
            ->get();

        return view('admin.reportes.index', compact(
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'totalPedidos',
            'ticketPromedio',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasPorMetodo'
        ));
    }
    public function pdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $pedidosValidos = Pedido::whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado']);

        $totalVentas = (clone $pedidosValidos)->sum('total');
        $totalPedidos = (clone $pedidosValidos)->count();
        $ticketPromedio = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;

        $productosMasVendidos = DetallePedido::select(
            'producto_id',
            DB::raw('SUM(cantidad) as total_vendido'),
            DB::raw('SUM(cantidad * precio_unitario) as total_ingresos')
        )
            ->whereHas('pedido', function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
                    ->whereNotIn('estado', ['cancelado']);
            })
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(10)
            ->get();

        $ventasPorDia = Pedido::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as total_ventas')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'desc')
            ->get();

        $ventasPorMetodo = Pedido::select(
            'metodo_pago',
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as total_ventas')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->whereNotIn('estado', ['cancelado'])
            ->groupBy('metodo_pago')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf', compact(
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'totalPedidos',
            'ticketPromedio',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasPorMetodo'
        ));

        return $pdf->download('reporte-ventas-' . $fechaInicio . '-al-' . $fechaFin . '.pdf');
    }
}
