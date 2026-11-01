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

class DetalleVentasSheet extends ReporteSheet implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    public function query(): Builder
    {
        return $this->reportes->detalleVentas($this->fechaInicio, $this->fechaFin);
    }

    public function headings(): array
    {
        return [
            'Pedido',
            'Fecha del pedido',
            'Cliente',
            'Correo del cliente',
            'Producto',
            'Categoría',
            'Cantidad',
            'Precio unitario (Bs.)',
            'Subtotal (Bs.)',
            'Método de pago',
            'Estados de pago',
            'Entrega',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '0',
            'B' => 'dd/mm/yyyy hh:mm',
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => '#,##0.00',
            'I' => '#,##0.00',
        ];
    }

    /**
     * @param  object  $fila
     */
    public function map(mixed $fila): array
    {
        return [
            $fila->pedido_id,
            Date::dateTimeToExcel(CarbonImmutable::parse($fila->fecha)),
            trim(($fila->cliente_nombres ?? '').' '.($fila->cliente_apellidos ?? '')),
            $fila->cliente_email,
            $fila->producto,
            $fila->categoria,
            (int) $fila->cantidad,
            (float) $fila->precio_unitario,
            (float) $fila->subtotal,
            $fila->metodo_pago === 'qr' ? 'Código QR' : ucfirst($fila->metodo_pago),
            $fila->estados_pago === null
                ? 'Sin registro'
                : implode(', ', array_map('ucfirst', explode(',', $fila->estados_pago))),
            $fila->tipo_entrega === 'contraentrega' ? 'Delivery' : 'Recojo en local',
        ];
    }

    public function title(): string
    {
        return 'Detalle ventas';
    }
}
