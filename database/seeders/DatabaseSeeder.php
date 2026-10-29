<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Empleado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Administrador
        $adminUser = Usuario::create([
            'email' => 'admin@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'admin',
        ]);
        Empleado::create([
            'usuario_id' => $adminUser->id,
            'nombres' => 'Admin',
            'apellidos' => 'Pizza Goool',
            'telefono' => '71234567',
            'cargo' => 'Administrador',
        ]);

        // 2. Crear Cajero
        $cajeroUser = Usuario::create([
            'email' => 'cajero@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'cajero',
        ]);
        Empleado::create([
            'usuario_id' => $cajeroUser->id,
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'telefono' => '71234568',
            'cargo' => 'Cajero',
        ]);

        // 3. Crear Repartidores
        $repartidor1User = Usuario::create([
            'email' => 'repartidor1@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'repartidor',
        ]);
        $rep1 = Repartidor::create([
            'usuario_id' => $repartidor1User->id,
            'nombres' => 'Carlos',
            'apellidos' => 'Gomez',
            'telefono' => '71234569',
            'ci' => '1234567-LP',
            'vehiculo' => 'Moto',
            'placa' => '4021-ABC',
            'licencia_conducir' => 'LIC-123456',
        ]);

        $repartidor2User = Usuario::create([
            'email' => 'repartidor2@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'repartidor',
        ]);
        $rep2 = Repartidor::create([
            'usuario_id' => $repartidor2User->id,
            'nombres' => 'Luis',
            'apellidos' => 'Mamani',
            'telefono' => '71234570',
            'ci' => '7654321-LP',
            'vehiculo' => 'Moto',
            'placa' => '5022-XYZ',
            'licencia_conducir' => 'LIC-654321',
        ]);

        // 4. Crear Clientes
        $clientes = [];
        $nombresClientes = [
            ['Maria', 'Lopez'],
            ['Pedro', 'Quispe'],
            ['Ana', 'Flores'],
            ['Jorge', 'Condori'],
            ['Lucia', 'Rojas']
        ];

        foreach ($nombresClientes as $index => $nombre) {
            $user = Usuario::create([
                'email' => 'cliente' . ($index + 1) . '@goool.com',
                'password' => Hash::make('12345678'),
                'rol' => 'cliente',
            ]);

            $clientes[] = Cliente::create([
                'usuario_id' => $user->id,
                'nombres' => $nombre[0],
                'apellidos' => $nombre[1],
                'telefono' => '7000000' . $index,
                'direccion_entrega' => 'Zona ' . ['Sopocachi', 'Miraflores', 'San Pedro', 'Obrajes', 'Calacoto'][$index] . ', Calle ' . fake()->numberBetween(1, 20),
            ]);
        }

        // 5. Crear Categorias y Productos
        $catPizzas = Categoria::create(['nombre' => 'Pizzas', 'descripcion' => 'Pizzas artesanales']);
        $catBebidas = Categoria::create(['nombre' => 'Bebidas', 'descripcion' => 'Refrescos y bebidas heladas']);
        $catPromos = Categoria::create(['nombre' => 'Promociones', 'descripcion' => 'Combos especiales']);

        $productos = [
            ['nombre' => 'Pizza Goool (Sabor de la Casa)', 'precio' => 102.00, 'categoria_id' => $catPizzas->id, 'descripcion' => 'Contiene jamón, chorizo, salame, pimiento morrón...', 'imagen' => 'pizzas/goool.jpg'],
            ['nombre' => 'Pizza Hawaiana', 'precio' => 130.50, 'categoria_id' => $catPizzas->id, 'descripcion' => 'La clásica combinación dulce y salada...', 'imagen' => 'pizzas/hawaiana.jpg'],
            ['nombre' => 'Pizza Cuatro Quesos', 'precio' => 63.00, 'categoria_id' => $catPizzas->id, 'descripcion' => 'Una mezcla cremosa e intensa...', 'imagen' => 'pizzas/cuatro_quesos.jpg'],
            ['nombre' => 'Coca-Cola 500ml', 'precio' => 10.00, 'categoria_id' => $catBebidas->id, 'descripcion' => 'Bebida gaseosa helada.', 'imagen' => 'bebidas/coca.jpg'],
            ['nombre' => 'Combo Familiar', 'precio' => 90.00, 'categoria_id' => $catPromos->id, 'descripcion' => '2 pizzas medianas + 1 gaseosa 1.5L.', 'imagen' => 'promos/combo.jpg'],
        ];

        $productosCreados = [];
        foreach ($productos as $prod) {
            $productosCreados[] = Producto::create([
                'categoria_id' => $prod['categoria_id'],
                'nombre' => $prod['nombre'],
                'descripcion' => $prod['descripcion'],
                'precio' => $prod['precio'],
                'imagen' => $prod['imagen'],
                'disponible' => true,
            ]);
        }

        // 6. Crear Pedidos Historicos (Para los reportes)
        for ($i = 0; $i < 40; $i++) {
            $cliente = $clientes[array_rand($clientes)];
            $esDelivery = rand(0, 1) === 1;
            $metodoPago = rand(0, 1) === 1 ? 'qr' : 'efectivo';

            $estados = ['entregado', 'entregado', 'entregado', 'cancelado', 'en_reparto', 'pendiente'];
            $estado = $estados[array_rand($estados)];

            $fechaPedido = now()->subDays(rand(0, 30))->subHours(rand(0, 5));

            $pedido = Pedido::create([
                'usuario_id' => $cliente->usuario_id,
                'repartidor_id' => ($estado === 'en_reparto' || ($estado === 'entregado' && $esDelivery)) ? (rand(0, 1) ? $rep1->usuario_id : $rep2->usuario_id) : null,
                'total' => 0,
                'estado' => $estado,
                'metodo_pago' => $metodoPago,
                'tipo_entrega' => $esDelivery ? 'contraentrega' : 'recoger_local',
                'direccion' => $esDelivery ? $cliente->direccion_entrega : null,
                'referencia' => $esDelivery ? 'Portón verde' : null,
                'latitud' => $esDelivery ? -16.5 + (rand(-100, 100) / 1000) : null,
                'longitud' => $esDelivery ? -68.15 + (rand(-100, 100) / 1000) : null,
                'created_at' => $fechaPedido,
                'updated_at' => $fechaPedido,
            ]);

            $total = 0;
            $cantidadProductos = rand(1, 3);
            $productosUsados = array_rand($productosCreados, $cantidadProductos);

            if (!is_array($productosUsados)) {
                $productosUsados = [$productosUsados];
            }

            foreach ($productosUsados as $index) {
                $producto = $productosCreados[$index];
                $cantidad = rand(1, 2);
                $subtotal = $producto->precio * $cantidad;
                $total += $subtotal;

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                ]);
            }

            $pedido->update(['total' => $total]);

            Pago::create([
                'pedido_id' => $pedido->id,
                'monto' => $total,
                'metodo' => $metodoPago,
                'estado_pago' => $estado === 'entregado' ? 'pagado' : 'pendiente',
                'created_at' => $fechaPedido,
                'updated_at' => $fechaPedido,
            ]);
        }
    }
}
