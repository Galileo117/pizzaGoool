<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Usuarios
        Schema::table('usuarios', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombres');
            $table->string('apellidos', 100)->nullable()->after('nombres');

            // Ajustar longitudes lógicas (Adiós al 255)
            $table->string('email', 150)->change();
            $table->string('telefono', 15)->nullable()->change();
            $table->string('rol', 20)->change();

            // BLINDAR EL TELÉFONO: No permite duplicados
            $table->unique('telefono');
        });

        // 2. Tabla Categorías
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('nombre', 100)->change();
        });

        // 3. Tabla Productos
        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre', 100)->change();
        });

        // 4. Tabla Pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado', 20)->change();
            $table->string('metodo_pago', 20)->change();
            $table->string('tipo_entrega', 20)->change();
        });

        // 5. Tabla Pagos
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('metodo', 20)->change();
            $table->string('estado_pago', 20)->change();
        });

        // 6. Tabla Configuraciones
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->string('clave', 50)->change();
        });
    }

    public function down(): void
    {
        // REVERTIR: Aquí sí ponemos el 255 original para que la BD vuelva a como estaba
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique(['telefono']); // Quitamos la regla de único
            $table->renameColumn('nombres', 'nombre');
            $table->dropColumn('apellidos');
            $table->string('email', 255)->change();
            $table->string('telefono', 255)->nullable()->change();
            $table->string('rol', 255)->change();
        });

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
