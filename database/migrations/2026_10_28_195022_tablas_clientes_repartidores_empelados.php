<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabla para Clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombres', 50); // 50 para nombres compuestos (Ej: María del Carmen)
            $table->string('apellidos', 50);
            $table->string('telefono', 15)->nullable(); // 15 por si a futuro ponen +591
            $table->string('direccion_entrega', 255)->nullable();
            $table->timestamps();
        });

        // 2. Tabla para Repartidores
        Schema::create('repartidores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombres', 50);
            $table->string('apellidos', 50);
            $table->string('telefono', 15);

            // CI: 15 caracteres permite guardar "1234567-LP" o "1234567-LP-1B"
            $table->string('ci', 15)->unique();

            $table->string('vehiculo', 30)->default('Moto');
            $table->string('placa', 15)->unique(); // Placa única por vehículo
            $table->string('licencia_conducir', 20)->unique(); //licencia de conducir unica 
            $table->timestamps();
        });

        // 3. Tabla para Empleados (Cajero y Admin)
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nombres', 50);
            $table->string('apellidos', 50);
            $table->string('telefono', 15)->nullable();
            $table->enum('cargo', ['Cajero', 'Administrador'])->default('Cajero');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('empleados');
        Schema::dropIfExists('repartidores');
        Schema::dropIfExists('clientes');
    }
};
