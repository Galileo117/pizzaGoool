<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Usuarios de prueba
        Usuario::updateOrCreate(['email' => 'admin@goool.com'], [
            'nombre' => 'Pizza Goool',
            'email' => 'admin@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'admin',
            'telefono' => '71234567'
        ]);

        Usuario::updateOrCreate(['email' => 'cajero@goool.com'], [
            'nombre' => 'Cajero Juan',
            'email' => 'cajero@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'cajero',
            'telefono' => '71234568'
        ]);

        Usuario::updateOrCreate(['email' => 'repartidor@goool.com'], [
            'nombre' => 'Carlos',
            'email' => 'repartidor@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'repartidor',
            'telefono' => '71234569'
        ]);

        Usuario::updateOrCreate(['email' => 'cliente@goool.com'], [
            'nombre' => 'Maria Lopez',
            'email' => 'cliente@goool.com',
            'password' => Hash::make('12345678'),
            'rol' => 'cliente',
            'telefono' => '71234570'
        ]);

        // Crear Categorías
        $pizzas = Categoria::updateOrCreate(['nombre' => 'Pizzas'], ['descripcion' => 'Pizzas artesanales al horno de leña']);
        $bebidas = Categoria::updateOrCreate(['nombre' => 'Bebidas'], ['descripcion' => 'Refrescos y bebidas heladas']);
        $promos = Categoria::updateOrCreate(['nombre' => 'Promociones'], ['descripcion' => 'Combos especiales']);

        // Crear Productos (Pizzas)
        Producto::updateOrCreate(['nombre' => 'Pizza Napolitana'], [
            'categoria_id' => $pizzas->id,
            'nombre' => 'Pizza Napolitana',
            'descripcion' => 'Salsa de tomate, mozzarella, aceitunas y orégano.',
            'precio' => 45.00,
            'imagen' => null,
            'disponible' => true
        ]);

        Producto::updateOrCreate(['nombre' => 'Pizza Pepperoni'], [
            'categoria_id' => $pizzas->id,
            'nombre' => 'Pizza Pepperoni',
            'descripcion' => 'Salsa de tomate, mozzarella y pepperoni.',
            'precio' => 55.00,
            'imagen' => null,
            'disponible' => true
        ]);

        Producto::updateOrCreate(['nombre' => 'Pizza Hawaiana'], [
            'categoria_id' => $pizzas->id,
            'nombre' => 'Pizza Hawaiana',
            'descripcion' => 'Salsa de tomate, mozzarella, jamón y piña.',
            'precio' => 50.00,
            'imagen' => null,
            'disponible' => true
        ]);

        Producto::updateOrCreate(['nombre' => 'Coca-Cola 500ml'], [
            'categoria_id' => $bebidas->id,
            'nombre' => 'Coca-Cola 500ml',
            'descripcion' => 'Bebida gaseosa helada.',
            'precio' => 10.00,
            'imagen' => null,
            'disponible' => true
        ]);

        Producto::updateOrCreate(['nombre' => 'Combo Familiar'], [
            'categoria_id' => $promos->id,
            'nombre' => 'Combo Familiar',
            'descripcion' => '2 pizzas medianas + 1 gaseosa 1.5L.',
            'precio' => 90.00,
            'imagen' => null,
            'disponible' => true
        ]);
    }
}
