<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportesExport;
use App\Http\Controllers\Controller;
use App\Services\Admin\ReportesService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Muestra los reportes administrativos y exporta sus datos en PDF o Excel.
 */
class ReporteController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const REPORTES = [
        'resumen' => 'Resumen de ventas',
        'productos' => 'Productos y categorías',
        'pedidos' => 'Pedidos y entregas',
        'pagos' => 'Pagos',
        'detalle' => 'Detalle de ventas',
    ];

    public function index(Request $request, ReportesService $reportes): View
    {
        [$fechaInicio, $fechaFin, $tipo] = $this->filtros($request, true);

        $datos = match ($tipo) {
            'resumen' => [
                'resumen' => $reportes->resumen($fechaInicio, $fechaFin),
                'ventasPorDia' => $reportes->ventasDiarias($fechaInicio, $fechaFin)->get(),
                'productosDestacados' => $reportes->productosMasVendidos($fechaInicio, $fechaFin)
                    ->limit(5)
                    ->get(),
            ],
            'productos' => [
                'ventasPorCategoria' => $reportes->ventasPorCategoria($fechaInicio, $fechaFin)->get(),
                'productos' => $reportes->productosMasVendidos($fechaInicio, $fechaFin)
                    ->paginate(25)
                    ->withQueryString(),
            ],
            'pedidos' => [
                'pedidosPorEstado' => $reportes->pedidosPorEstado($fechaInicio, $fechaFin)->get(),
                'pedidosPorTipo' => $reportes->pedidosPorTipoEntrega($fechaInicio, $fechaFin)->get(),
            ],
            'pagos' => [
                'pagosPorMetodoYEstado' => $reportes->pagosPorMetodoYEstado($fechaInicio, $fechaFin)->get(),
            ],
            'detalle' => [
                'detalleVentas' => $reportes->detalleVentas($fechaInicio, $fechaFin)
                    ->paginate(25)
                    ->withQueryString(),
            ],
        };

        return view('admin.reportes.index', [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'tipoReporte' => $tipo,
            'nombreReporte' => self::REPORTES[$tipo],
            'reportesDisponibles' => self::REPORTES,
            ...$datos,
        ]);
    }

    public function pdf(Request $request, ReportesService $reportes): Response
    {
        [$fechaInicio, $fechaFin, $tipo] = $this->filtros($request, true);
        $datos = match ($tipo) {
            'resumen' => [
                'resumen' => $reportes->resumen($fechaInicio, $fechaFin),
                'ventasPorDia' => $reportes->ventasDiarias($fechaInicio, $fechaFin)->get(),
                'productosDestacados' => $reportes->productosMasVendidos($fechaInicio, $fechaFin)
                    ->limit(10)
                    ->get(),
            ],
            'productos' => [
                'ventasPorCategoria' => $reportes->ventasPorCategoria($fechaInicio, $fechaFin)->get(),
                'productos' => $reportes->productosMasVendidos($fechaInicio, $fechaFin)->get(),
            ],
            'pedidos' => [
                'pedidosPorEstado' => $reportes->pedidosPorEstado($fechaInicio, $fechaFin)->get(),
                'pedidosPorTipo' => $reportes->pedidosPorTipoEntrega($fechaInicio, $fechaFin)->get(),
            ],
            'pagos' => [
                'pagosPorMetodoYEstado' => $reportes->pagosPorMetodoYEstado($fechaInicio, $fechaFin)->get(),
            ],
            'detalle' => [
                'detalleVentas' => $reportes->detalleVentas($fechaInicio, $fechaFin)->get(),
            ],
        };

        $pdf = Pdf::loadView('admin.reportes.pdf', [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'tipoReporte' => $tipo,
            'nombreReporte' => self::REPORTES[$tipo],
            ...$datos,
        ])->setPaper('a4', $tipo === 'detalle' ? 'landscape' : 'portrait');

        return $pdf->download('reporte-'.$tipo.'-'.$fechaInicio.'-al-'.$fechaFin.'.pdf');
    }

    public function excel(Request $request, ReportesService $reportes): BinaryFileResponse
    {
        [$fechaInicio, $fechaFin] = $this->filtros($request);

        return Excel::download(
            new ReportesExport($reportes, $fechaInicio, $fechaFin),
            'reporte-pizza-goool-'.$fechaInicio.'-al-'.$fechaFin.'.xlsx',
        );
    }

    /**
     * @return array{0: string, 1: string, 2?: string}
     */
    private function filtros(Request $request, bool $incluirTipo = false): array
    {
        $reglas = [
            'fecha_inicio' => ['nullable', 'date_format:Y-m-d'],
            'fecha_fin' => ['nullable', 'date_format:Y-m-d'],
        ];

        if ($incluirTipo) {
            $reglas['reporte'] = ['nullable', 'in:'.implode(',', array_keys(self::REPORTES))];
        }

        $filtros = $request->validate($reglas);
        $fechaInicio = $filtros['fecha_inicio'] ?? now()->subDays(30)->toDateString();
        $fechaFin = $filtros['fecha_fin'] ?? now()->toDateString();

        if (CarbonImmutable::parse($fechaInicio)->greaterThan(CarbonImmutable::parse($fechaFin))) {
            throw ValidationException::withMessages([
                'fecha_fin' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            ]);
        }

        $resultado = [$fechaInicio, $fechaFin];

        if ($incluirTipo) {
            $resultado[] = $filtros['reporte'] ?? 'resumen';
        }

        return $resultado;
    }
}
