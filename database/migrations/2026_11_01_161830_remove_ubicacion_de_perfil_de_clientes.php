<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Archiva ubicación de perfil antes de retirarla; las coordenadas del pedido no cambian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_ubicaciones_archivadas', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->primary();
            $table->string('direccion_entrega', 255)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->timestamp('archived_at')->useCurrent();
        });

        DB::table('clientes_ubicaciones_archivadas')->insertUsing(
            ['cliente_id', 'direccion_entrega', 'latitud', 'longitud'],
            DB::table('clientes')
                ->select('id', 'direccion_entrega', 'latitud', 'longitud')
                ->where(function ($query) {
                    $query->whereNotNull('direccion_entrega')
                        ->orWhereNotNull('latitud')
                        ->orWhereNotNull('longitud');
                })
        );

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['direccion_entrega', 'latitud', 'longitud']);
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('direccion_entrega', 255)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
        });

        DB::table('clientes_ubicaciones_archivadas')
            ->orderBy('cliente_id')
            ->chunkById(100, function ($ubicacionesArchivadas) {
                foreach ($ubicacionesArchivadas as $ubicacionArchivada) {
                    DB::table('clientes')
                        ->where('id', $ubicacionArchivada->cliente_id)
                        ->update([
                            'direccion_entrega' => $ubicacionArchivada->direccion_entrega,
                            'latitud' => $ubicacionArchivada->latitud,
                            'longitud' => $ubicacionArchivada->longitud,
                        ]);
                }
            }, 'cliente_id');

        Schema::drop('clientes_ubicaciones_archivadas');
    }
};
