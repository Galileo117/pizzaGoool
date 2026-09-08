<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('total', 10, 2);
            $table->string('estado'); // pendiente, preparacion, listo, en_reparto, entregado, cancelado
            $table->string('metodo_pago'); // qr, efectivo
            $table->string('tipo_entrega'); // recoger_local, contraentrega
            $table->string('direccion')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
