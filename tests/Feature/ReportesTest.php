<?php

use App\Exports\Reportes\DetalleVentasSheet;
use App\Exports\ReportesExport;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Empleado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Services\Admin\ReportesService;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

function crearAdministradorParaReportes(): Usuario
{
    $administrador = Usuario::create([
        'email' => 'admin-reportes@example.com',
        'password' => 'password123',
        'rol' => 'admin',
    ]);

    Empleado::create([
        'usuario_id' => $administrador->id,
        'nombres' => 'Admin',
        'apellidos' => 'Reportes',
        'cargo' => 'Administrador',
    ]);

    return $administrador;
}

/**
 * @return array{pedido: Pedido, producto: Producto}
 */
function crearPedidoParaReporte(string $estado, string $total, string $fecha, string $estadoPago = 'pendiente'): array
{
    $cliente = Usuario::create([
        'email' => uniqid('cliente-reporte', true).'@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);

    Cliente::create([
        'usuario_id' => $cliente->id,
        'nombres' => 'Ana',
        'apellidos' => 'Cliente',
    ]);

    $categoria = Categoria::create([
        'nombre' => uniqid('categoria-reporte-'),
    ]);

    $producto = Producto::create([
        'categoria_id' => $categoria->id,
        'nombre' => 'Pizza de reporte',
        'descripcion' => 'Producto para reportes',
        'precio' => $total,
        'disponible' => true,
    ]);

    $pedido = Pedido::create([
        'usuario_id' => $cliente->id,
        'total' => $total,
        'estado' => $estado,
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'recoger_local',
    ]);
    $pedido->created_at = $fecha;
    $pedido->updated_at = $fecha;
    $pedido->save();

    DetallePedido::create([
        'pedido_id' => $pedido->id,
        'producto_id' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => $total,
    ]);

    Pago::create([
        'pedido_id' => $pedido->id,
        'monto' => $total,
        'metodo' => 'efectivo',
        'estado_pago' => $estadoPago,
    ]);

    return compact('pedido', 'producto');
}

test('admin report summary counts delivered orders as sales and separates the report sections', function () {
    crearPedidoParaReporte('entregado', '50.00', '2026-10-15 12:00:00', 'pagado');
    crearPedidoParaReporte('cancelado', '80.00', '2026-10-16 12:00:00');
    crearPedidoParaReporte('pendiente', '40.00', '2026-10-17 12:00:00');

    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.index', [
            'reporte' => 'resumen',
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
        ]))
        ->assertOk()
        ->assertSee('Ventas completadas')
        ->assertSee('50.00')
        ->assertSee('Productos')
        ->assertSee('Pedidos')
        ->assertSee('Pagos')
        ->assertSee('Detalle de ventas')
        ->assertDontSee('Las ventas se calculan únicamente');
});

test('admin product report only includes products from completed orders', function () {
    $completed = crearPedidoParaReporte('entregado', '50.00', '2026-10-15 12:00:00');
    crearPedidoParaReporte('cancelado', '80.00', '2026-10-16 12:00:00');

    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.index', [
            'reporte' => 'productos',
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
        ]))
        ->assertOk()
        ->assertSee($completed['producto']->nombre)
        ->assertSee('VENTAS POR CATEGORÍA')
        ->assertDontSee('Este informe incluye');
});

test('admin detail report omits explanatory banners from the report content', function () {
    crearPedidoParaReporte('entregado', '50.00', '2026-10-15 12:00:00');

    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.index', [
            'reporte' => 'detalle',
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
        ]))
        ->assertOk()
        ->assertSee('DETALLE DE VENTAS')
        ->assertDontSee('Cada fila corresponde a un producto');
});

test('admin report dates must be valid and ordered', function () {
    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.index', [
            'fecha_inicio' => '2026-10-31',
            'fecha_fin' => '2026-10-01',
        ]))
        ->assertRedirect()
        ->assertSessionHasErrors([
            'fecha_fin' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ]);
});

test('admin can download the selected report as a PDF', function () {
    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.pdf', [
            'reporte' => 'pedidos',
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
        ]))
        ->assertDownload('reporte-pedidos-2026-10-01-al-2026-10-31.pdf');
});

test('admin can download a workbook with all report sheets for the selected period', function () {
    $completed = crearPedidoParaReporte('entregado', '50.00', '2026-10-15 12:00:00');

    Excel::fake();

    $this->actingAs(crearAdministradorParaReportes())
        ->get(route('admin.reportes.excel', [
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
        ]))
        ->assertOk();

    $sheetNames = [];

    Excel::assertDownloaded(
        'reporte-pizza-goool-2026-10-01-al-2026-10-31.xlsx',
        function (ReportesExport $export) use (&$sheetNames, $completed): bool {
            $sheets = $export->sheets();
            $sheetNames = array_map(fn ($sheet) => $sheet->title(), $sheets);
            $detailSheet = collect($sheets)->first(fn ($sheet) => $sheet instanceof DetalleVentasSheet);
            $detalle = $detailSheet->query()->where('pedidos.id', $completed['pedido']->id)->get();

            return count($sheets) === 8
                && $detalle->count() === 1
                && $detalle->first()->estados_pago === 'pendiente';
        }
    );

    expect($sheetNames)->toBe([
        'Resumen',
        'Ventas diarias',
        'Productos',
        'Categorías',
        'Pedidos',
        'Entrega',
        'Pagos',
        'Detalle ventas',
    ]);
});

test('report sheets generate a valid XLSX workbook', function () {
    $completed = crearPedidoParaReporte('entregado', '50.00', '2026-10-15 12:00:00');
    $categoria = Categoria::findOrFail($completed['producto']->categoria_id);

    $workbook = Excel::raw(
        new ReportesExport(app(ReportesService::class), '2026-10-01', '2026-10-31'),
        Maatwebsite\Excel\Excel::XLSX,
    );

    expect(substr($workbook, 0, 2))->toBe('PK');

    $temporaryFile = tempnam(sys_get_temp_dir(), 'reportes-');
    file_put_contents($temporaryFile, $workbook);

    try {
        $spreadsheet = IOFactory::load($temporaryFile);
        $ventasDiarias = $spreadsheet->getSheetByName('Ventas diarias');
        $resumen = $spreadsheet->getSheetByName('Resumen');

        expect($spreadsheet->getSheetNames())->toBe([
            'Resumen',
            'Ventas diarias',
            'Productos',
            'Categorías',
            'Pedidos',
            'Entrega',
            'Pagos',
            'Detalle ventas',
        ])
            ->and($resumen->getCell('B1')->getValue())->toBe('Resultado')
            ->and($resumen->getStyle('B3')->getNumberFormat()->getFormatCode())->toBe('#,##0.00 "Bs."')
            ->and($ventasDiarias->getAutoFilter()->getRange())->toBe('A1:C2')
            ->and($ventasDiarias->getFreezePane())->toBe('A2')
            ->and($ventasDiarias->getCell('A2')->getStyle()->getNumberFormat()->getFormatCode())->toBe('dd/mm/yyyy')
            ->and((float) $ventasDiarias->getCell('C2')->getValue())->toBe(50.0)
            ->and($spreadsheet->getSheetByName('Categorías')->getCell('A2')->getValue())->toBe($categoria->nombre)
            ->and((float) $spreadsheet->getSheetByName('Categorías')->getCell('D2')->getValue())->toBe(50.0)
            ->and($spreadsheet->getSheetByName('Entrega')->getCell('A2')->getValue())->toBe('Recojo en local')
            ->and((float) $spreadsheet->getSheetByName('Entrega')->getCell('C2')->getValue())->toBe(50.0);
    } finally {
        unlink($temporaryFile);
    }
});

test('non-admin users cannot view or export admin reports', function () {
    $cliente = Usuario::create([
        'email' => 'cliente-reportes@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);

    $this->actingAs($cliente)
        ->get(route('admin.reportes.index'))
        ->assertForbidden();

    $this->get(route('admin.reportes.excel'))
        ->assertForbidden();
});
