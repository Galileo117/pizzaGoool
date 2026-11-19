<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pagos')
            ->join('pedidos', 'pedidos.id', '=', 'pagos.pedido_id')
            ->where('pedidos.estado', 'entregado')
            ->where('pagos.estado_pago', 'pendiente')
            ->update(['pagos.estado_pago' => 'pagado']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
