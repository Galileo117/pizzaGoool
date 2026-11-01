<?php

namespace App\Exports\Reportes;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithFreezePane;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTabColor;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class ReporteSheet extends DefaultValueBinder implements ShouldAutoSize, WithCustomValueBinder, WithFreezePane, WithStyles, WithTabColor
{
    public function freezePane(): string
    {
        return 'A2';
    }

    public function tabColor(): string
    {
        return '1B5E20';
    }

    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1B5E20'],
                ],
                'alignment' => [
                    'vertical' => 'center',
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => 'medium',
                        'color' => ['rgb' => 'FBC02D'],
                    ],
                ],
            ],
        ];
    }
}
