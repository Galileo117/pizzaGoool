<?php

namespace App\Exports;

use App\Exports\Reportes\CategoriasSheet;
use App\Exports\Reportes\DetalleVentasSheet;
use App\Exports\Reportes\ModalidadesEntregaSheet;
use App\Exports\Reportes\PagosSheet;
use App\Exports\Reportes\PedidosSheet;
use App\Exports\Reportes\ProductosSheet;
use App\Exports\Reportes\ResumenSheet;
use App\Exports\Reportes\VentasDiariasSheet;
use App\Services\Admin\ReportesService;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportesExport implements Export, WithMultipleSheets
{
    public function __construct(
        private ReportesService $reportes,
        private string $fechaInicio,
        private string $fechaFin,
    ) {}

    /**
     * @return array<int, Export>
     */
    public function sheets(): array
    {
        return [
            new ResumenSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new VentasDiariasSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new ProductosSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new CategoriasSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new PedidosSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new ModalidadesEntregaSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new PagosSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
            new DetalleVentasSheet($this->reportes, $this->fechaInicio, $this->fechaFin),
        ];
    }
}
