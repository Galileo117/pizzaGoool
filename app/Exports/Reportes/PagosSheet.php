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

class PagosSheet extends ReporteSheet implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function query(): Builder
    {
        return $this->reportes->pagosPorMetodoYEstado($this->fechaInicio, $this->fechaFin);
    }

    public function headings(): array
    {
        return ['Método de pago', 'Estado de pago', 'Cantidad de registros', 'Monto registrado (Bs.)'];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => '#,##0.00',
        ];
    }

    /**
     * @param  object  $fila
     */
    public function map(mixed $fila): array
    {
        return [
            $fila->metodo === 'qr' ? 'Código QR' : ucfirst($fila->metodo),
            ucfirst($fila->estado_pago),
            (int) $fila->total_pagos,
            (float) $fila->monto_registrado,
        ];
    }

    public function title(): string
    {
        return 'Pagos';
    }
}
