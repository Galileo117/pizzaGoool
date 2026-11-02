<?php

namespace App\Exports\Reportes;

use App\Services\Admin\ReportesService;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ModalidadesEntregaSheet extends ReporteSheet implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function query(): Builder
    {
        return $this->reportes->pedidosPorTipoEntrega($this->fechaInicio, $this->fechaFin);
    }

    public function headings(): array
    {
        return [
            'Modalidad de entrega',
            'Cantidad de pedidos',
            'Ventas completadas (Bs.)',
            'Envíos cobrados (Bs.)',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => '#,##0.00',
            'D' => '#,##0.00',
        ];
    }

    /**
     * @param  object  $fila
     */
    public function map(mixed $fila): array
    {
        return [
            $fila->tipo_entrega === 'delivery' ? 'Delivery' : 'Recojo en local',
            (int) $fila->total_pedidos,
            (float) $fila->ventas_completadas,
            (float) $fila->costos_envio,
        ];
    }

    public function title(): string
    {
        return 'Entrega';
    }
}
