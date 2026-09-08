<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->decimal('monto', 10, 2);
            $table->string('metodo'); // qr, efectivo
            $table->string('estado_pago'); // pendiente, pagado, verificado
            $table->string('codigo_qr')->nullable();
            $table->timestamps();

            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
