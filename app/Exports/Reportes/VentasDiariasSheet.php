<?php

namespace App\Exports\Reportes;

use App\Services\Admin\ReportesService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class VentasDiariasSheet extends ReporteSheet implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function query(): Builder
    {
        return $this->reportes->ventasDiarias($this->fechaInicio, $this->fechaFin);
    }

    public function headings(): array
    {
        return ['Fecha de pedido', 'Pedidos entregados', 'Ventas completadas (Bs.)'];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'dd/mm/yyyy',
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => '#,##0.00',
        ];
    }

    /**
     * @param  object  $fila
     */
    public function map(mixed $fila): array
    {
        return [
            Date::dateTimeToExcel(
                CarbonImmutable::parse($fila->fecha),
            ),
            (int) $fila->total_pedidos,
            (float) $fila->total_ventas,
        ];
    }

    public function title(): string
    {
        return 'Ventas diarias';
    }
}
