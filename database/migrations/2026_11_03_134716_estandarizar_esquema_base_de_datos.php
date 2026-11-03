<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Corregir tabla pedidos (Agregar costo_envio si no existe)
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'costo_envio')) {
                $table->decimal('costo_envio', 10, 2)->default(0)->after('total');
            }
        });

        // 2. Corregir tabla repartidores (licencia_conducir debe ser string, no relación)
        Schema::table('repartidores', function (Blueprint $table) {
            // Forzamos a que sea string por si se creó mal
            $table->string('licencia_conducir', 255)->change();
        });

        // 3. Asegurar que pagos tenga el campo 'metodo' (no metodo_pago)
        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'metodo')) {
                $table->string('metodo', 50)->after('monto');
            }
        });

        // 4. Asegurar Soft Deletes en las tablas que lo necesitan
        if (!Schema::hasColumn('usuarios', 'deleted_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('productos', 'deleted_at')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('categorias', 'deleted_at')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // No hacemos nada en el down para evitar conflictos
    }
};
