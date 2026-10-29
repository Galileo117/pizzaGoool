<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NOTA: Las modificaciones a la tabla 'usuarios' fueron eliminadas
        // porque ahora los datos personales viven en tablas separadas (clientes, etc.)

        // 1. Tabla Categorías
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('nombre', 100)->change();
        });

        // 2. Tabla Productos
        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre', 100)->change();
        });

        // 3. Tabla Pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado', 20)->change();
            $table->string('metodo_pago', 20)->change();
            $table->string('tipo_entrega', 20)->change();
        });

        // 4. Tabla Pagos
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('metodo', 20)->change();
            $table->string('estado_pago', 20)->change();
        });

        // 5. Tabla Configuraciones
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->string('clave', 50)->change();
        });
    }

    public function down(): void
    {
        // Revertir cambios de las otras tablas
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('nombre', 255)->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre', 255)->change();
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado', 255)->change();
            $table->string('metodo_pago', 255)->change();
            $table->string('tipo_entrega', 255)->change();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->string('metodo', 255)->change();
            $table->string('estado_pago', 255)->change();
        });

        Schema::table('configuraciones', function (Blueprint $table) {
            $table->string('clave', 255)->change();
        });
    }
};
