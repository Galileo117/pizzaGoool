<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Verificamos si el índice ya existe antes de crearlo
            if (!Schema::hasIndex('pagos', 'pagos_pedido_id_unique')) {
                $table->unique('pedido_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Verificamos si existe antes de intentar borrarlo
            if (Schema::hasIndex('pagos', 'pagos_pedido_id_unique')) {
                $table->dropUnique(['pedido_id']);
            }
        });
    }
};
