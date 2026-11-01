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

class PedidosSheet extends ReporteSheet implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function query(): Builder
    {
        return $this->reportes->pedidosPorEstado($this->fechaInicio, $this->fechaFin);
    }

    public function headings(): array
    {
        return ['Estado del pedido', 'Cantidad de pedidos', 'Monto de pedidos (Bs.)'];
    }

    public function columnFormats(): array
    {
        return [
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
            $this->nombreEstado($fila->estado),
            (int) $fila->total_pedidos,
            (float) $fila->monto_asociado,
        ];
    }

    public function title(): string
    {
        return 'Pedidos';
    }

    private function nombreEstado(string $estado): string
    {
        return match ($estado) {
            'pendiente' => 'Pendiente',
            'preparacion' => 'En preparación',
            'listo' => 'Listo',
            'en_reparto' => 'En reparto',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $estado)),
        };
    }
}
