<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('repartidor_id')->nullable()->after('usuario_id');
            $table->foreign('repartidor_id')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['repartidor_id']);
            $table->dropColumn('repartidor_id');
        });
    }
};