<?php

namespace App\Exports\Reportes;

use App\Services\Admin\ReportesService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumenSheet extends ReporteSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function collection(): Collection
    {
        return $this->reportes->resumenParaExcel($this->fechaInicio, $this->fechaFin)
            ->map(fn (array $fila) => [$fila['indicador'], $fila['valor']]);
    }

    public function headings(): array
    {
        return ['Indicador', 'Resultado'];
    }

    public function title(): string
    {
        return 'Resumen';
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('B3')->getNumberFormat()->setFormatCode('#,##0.00 "Bs."');
        $sheet->getStyle('B4:B7')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $sheet->getStyle('B8:B9')->getNumberFormat()->setFormatCode('#,##0.00 "Bs."');
        $sheet->getStyle('B10')->getNumberFormat()->setFormatCode('0.00"%"');

        return parent::styles($sheet);
    }
}
