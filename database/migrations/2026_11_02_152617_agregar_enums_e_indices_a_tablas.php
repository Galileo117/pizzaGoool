<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'cajero', 'repartidor', 'cliente') NOT NULL");
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente', 'preparacion', 'listo', 'en_reparto', 'entregado', 'cancelado') DEFAULT 'pendiente'");
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN metodo_pago ENUM('efectivo', 'qr') NOT NULL");
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN tipo_entrega ENUM('local', 'delivery') NOT NULL");
            DB::statement("ALTER TABLE pagos MODIFY COLUMN metodo ENUM('efectivo', 'qr') NOT NULL");
            DB::statement("ALTER TABLE pagos MODIFY COLUMN estado_pago ENUM('pendiente', 'pagado', 'verificado') DEFAULT 'pendiente'");
        }

        Schema::table('pedidos', function (Blueprint $table) {
            if (! Schema::hasIndex('pedidos', 'pedidos_estado_index')) {
                $table->index('estado');
            }
            if (! Schema::hasIndex('pedidos', 'pedidos_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasIndex('productos', 'productos_categoria_id_index')) {
                $table->index('categoria_id');
            }
            if (! Schema::hasIndex('productos', 'productos_disponible_index')) {
                $table->index('disponible');
            }
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE usuarios MODIFY COLUMN rol VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE pedidos MODIFY COLUMN estado VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE pedidos MODIFY COLUMN metodo_pago VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE pedidos MODIFY COLUMN tipo_entrega VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE pagos MODIFY COLUMN metodo VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE pagos MODIFY COLUMN estado_pago VARCHAR(255) NOT NULL');
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['categoria_id']);
            $table->dropIndex(['disponible']);
        });
    }
};
