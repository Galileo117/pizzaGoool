<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refuerza cardinalidades y evita que borrar productos/cuentas destruya historial.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->restrictOnDelete();
        });

        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->restrictOnDelete();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->unique('pedido_id');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->unique('usuario_id');
            $table->unique('telefono');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->unique('usuario_id');
        });

        Schema::table('repartidores', function (Blueprint $table) {
            $table->unique('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('repartidores', function (Blueprint $table) {
            $table->dropUnique(['usuario_id']);
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropUnique(['usuario_id']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['usuario_id']);
            $table->dropUnique(['telefono']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique(['pedido_id']);
        });

        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->cascadeOnDelete();
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }
};
