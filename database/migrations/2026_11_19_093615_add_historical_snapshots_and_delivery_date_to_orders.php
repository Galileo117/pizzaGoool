<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_email')->nullable();
            $table->timestamp('entregado_at')->nullable();
        });

        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->string('producto_nombre')->nullable();
            $table->string('categoria_nombre')->nullable();
        });

        DB::table('pedidos')
            ->leftJoin('clientes', 'clientes.usuario_id', '=', 'pedidos.usuario_id')
            ->leftJoin('usuarios', 'usuarios.id', '=', 'pedidos.usuario_id')
            ->select(
                'pedidos.id',
                'pedidos.estado',
                'pedidos.updated_at',
                'clientes.nombres',
                'clientes.apellidos',
                'usuarios.email',
            )
            ->orderBy('pedidos.id')
            ->chunk(500, function ($pedidos): void {
                foreach ($pedidos as $pedido) {
                    $nombreCliente = trim(implode(' ', array_filter([
                        $pedido->nombres,
                        $pedido->apellidos,
                    ])));

                    DB::table('pedidos')
                        ->where('id', $pedido->id)
                        ->update([
                            'cliente_nombre' => $nombreCliente !== '' ? $nombreCliente : null,
                            'cliente_email' => $pedido->email,
                            'entregado_at' => $pedido->estado === 'entregado' ? $pedido->updated_at : null,
                        ]);
                }
            });

        DB::table('detalle_pedidos')
            ->leftJoin('productos', 'productos.id', '=', 'detalle_pedidos.producto_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->select(
                'detalle_pedidos.id',
                'productos.nombre AS producto_nombre',
                'categorias.nombre AS categoria_nombre',
            )
            ->orderBy('detalle_pedidos.id')
            ->chunk(500, function ($detalles): void {
                foreach ($detalles as $detalle) {
                    DB::table('detalle_pedidos')
                        ->where('id', $detalle->id)
                        ->update([
                            'producto_nombre' => $detalle->producto_nombre,
                            'categoria_nombre' => $detalle->categoria_nombre,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_pedidos', function (Blueprint $table) {
            $table->dropColumn(['producto_nombre', 'categoria_nombre']);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['cliente_nombre', 'cliente_email', 'entregado_at']);
        });
    }
};
