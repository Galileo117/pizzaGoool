<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

function crearClienteDePrueba(array $datos = []): Usuario
{
    $usuario = Usuario::create([
        'email' => $datos['email'] ?? uniqid('cliente', true).'@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);

    Cliente::create([
        'usuario_id' => $usuario->id,
        'nombres' => $datos['nombres'] ?? 'Ana',
        'apellidos' => $datos['apellidos'] ?? 'Lopez',
        'telefono' => $datos['telefono'] ?? null,
    ]);

    return $usuario;
}

function crearProductoDePrueba(string $precio = '20.00'): Producto
{
    $categoria = Categoria::create(['nombre' => 'Pizzas']);

    return Producto::create([
        'categoria_id' => $categoria->id,
        'nombre' => 'Pizza de prueba',
        'descripcion' => 'Producto usado por las pruebas.',
        'precio' => $precio,
        'disponible' => true,
    ]);
}

function crearPedidoDePrueba(Usuario $usuario, Producto $producto): Pedido
{
    $pedido = Pedido::create([
        'usuario_id' => $usuario->id,
        'total' => '20.00',
        'estado' => 'pendiente',
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'recoger_local',
    ]);

    DetallePedido::create([
        'pedido_id' => $pedido->id,
        'producto_id' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => '20.00',
    ]);

    Pago::create([
        'pedido_id' => $pedido->id,
        'monto' => '20.00',
        'metodo' => 'efectivo',
        'estado_pago' => 'pendiente',
    ]);

    return $pedido;
}

test('api registration creates a customer profile with the documented name field', function () {
    $response = $this->postJson('/api/register', [
        'nombre' => 'Ana',
        'apellidos' => 'Lopez',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'telefono' => '71234567',
        'device_name' => 'test-app',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.usuario.nombres', 'Ana')
        ->assertJsonPath('data.usuario.apellidos', 'Lopez')
        ->assertJsonPath('data.usuario.nombre_completo', 'Ana Lopez')
        ->assertJsonPath('data.usuario.telefono', '71234567');

    $usuario = Usuario::where('email', 'ana@example.com')->firstOrFail();

    $this->assertDatabaseHas('clientes', [
        'usuario_id' => $usuario->id,
        'nombres' => 'Ana',
        'apellidos' => 'Lopez',
        'telefono' => '71234567',
    ]);

    $login = $this->postJson('/api/login', [
        'email' => 'ana@example.com',
        'password' => 'password123',
        'device_name' => 'test-app',
    ])->assertOk()->assertJsonPath('data.usuario.nombre_completo', 'Ana Lopez');

    $this->withToken($login->json('data.token'))
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('data.usuario.nombre_completo', 'Ana Lopez');
});

test('api orders use stored prices and serialize customer and payment profiles', function () {
    $usuario = crearClienteDePrueba();
    $producto = crearProductoDePrueba('12.50');

    Sanctum::actingAs($usuario);

    $response = $this->postJson('/api/pedidos', [
        'items' => [
            ['producto_id' => $producto->id, 'cantidad' => 2],
        ],
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'recoger_local',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.total', 25)
        ->assertJsonPath('data.usuario.nombre_completo', 'Ana Lopez')
        ->assertJsonPath('data.pago.monto', 25)
        ->assertJsonPath('data.detalle.0.precio_unitario', 12.5);

    $this->assertDatabaseHas('pedidos', [
        'usuario_id' => $usuario->id,
        'total' => '25.00',
    ]);

    $this->assertDatabaseCount('pagos', 1);

    $repartidorUsuario = Usuario::create([
        'email' => 'repartidor@example.com',
        'password' => 'password123',
        'rol' => 'repartidor',
    ]);

    Repartidor::create([
        'usuario_id' => $repartidorUsuario->id,
        'nombres' => 'Luis',
        'apellidos' => 'Repartidor',
        'telefono' => '71234569',
        'ci' => '1234567-LP',
        'placa' => '1234-ABC',
        'licencia_conducir' => 'LIC-123456',
    ]);

    $pedido = Pedido::firstOrFail();
    $pedido->update([
        'repartidor_id' => $repartidorUsuario->id,
        'estado' => 'en_reparto',
        'tipo_entrega' => 'contraentrega',
    ]);

    $this->getJson('/api/pedidos/'.$pedido->id)
        ->assertOk()
        ->assertJsonPath('data.repartidor.nombres', 'Luis')
        ->assertJsonPath('data.repartidor.id', $repartidorUsuario->id);
});

test('web checkout refreshes cart prices before the order is confirmed', function () {
    $usuario = crearClienteDePrueba();
    $producto = crearProductoDePrueba('25.00');

    $this->actingAs($usuario)->withSession([
        'carrito' => [
            $producto->id => [
                'producto_id' => $producto->id,
                'nombre' => 'Nombre antiguo',
                'precio' => '10.00',
                'cantidad' => 2,
            ],
        ],
    ])->get(route('cliente.checkout'))
        ->assertOk()
        ->assertSessionHas('carrito.'.$producto->id.'.precio', '25.00');

    $this->post(route('cliente.procesar_pedido'), [
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'recoger_local',
    ])->assertRedirect();

    $pedido = Pedido::firstOrFail();

    $this->assertEquals(50.0, (float) $pedido->total);
    $this->assertEquals(25.0, (float) $pedido->detallePedidos()->firstOrFail()->precio_unitario);
});

test('customer profile has no delivery location and checkout does not reuse profile addresses', function () {
    $usuario = crearClienteDePrueba();
    $migration = require database_path('migrations/2026_11_01_161830_remove_ubicacion_de_perfil_de_clientes.php');

    $migration->down();

    DB::table('clientes')
        ->where('id', $usuario->cliente->id)
        ->update([
            'direccion_entrega' => 'Dirección antigua del perfil',
            'latitud' => -16.5,
            'longitud' => -68.15,
        ]);

    $migration->up();

    expect(Schema::hasColumns('clientes', ['direccion_entrega', 'latitud', 'longitud']))->toBeFalse();
    expect(Schema::hasTable('clientes_ubicaciones_archivadas'))->toBeTrue();

    $this->assertDatabaseHas('clientes_ubicaciones_archivadas', [
        'cliente_id' => $usuario->cliente->id,
        'direccion_entrega' => 'Dirección antigua del perfil',
        'latitud' => -16.5,
        'longitud' => -68.15,
    ]);

    $migration->down();

    $this->assertDatabaseHas('clientes', [
        'id' => $usuario->cliente->id,
        'direccion_entrega' => 'Dirección antigua del perfil',
        'latitud' => -16.5,
        'longitud' => -68.15,
    ]);

    $migration->up();

    $this->actingAs($usuario)
        ->get(route('cliente.perfil'))
        ->assertOk()
        ->assertDontSee('mapa-perfil')
        ->assertDontSee('direccion_entrega')
        ->assertDontSee('Dirección de entrega por defecto');

    $this->actingAs($usuario)->patch('/profile', [
        'nombres' => 'Ana',
        'apellidos' => 'Lopez',
        'email' => $usuario->email,
        'telefono' => null,
        'direccion_entrega' => 'No guardar esta dirección',
        'latitud' => 95,
        'longitud' => 200,
    ])->assertRedirect(route('cliente.perfil'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('clientes_ubicaciones_archivadas', [
        'cliente_id' => $usuario->cliente->id,
        'direccion_entrega' => 'Dirección antigua del perfil',
    ]);

    $producto = crearProductoDePrueba();

    $this->withSession([
        'carrito' => [
            $producto->id => [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
            ],
        ],
    ])->get(route('cliente.checkout'))
        ->assertOk()
        ->assertDontSee('Dirección antigua del perfil')
        ->assertSee('value=""', false);
});

test('products and user accounts linked to orders cannot be deleted', function () {
    $administrador = Usuario::create([
        'email' => 'admin@example.com',
        'password' => 'password123',
        'rol' => 'admin',
    ]);
    $cliente = crearClienteDePrueba();
    $producto = crearProductoDePrueba();
    $pedido = crearPedidoDePrueba($cliente, $producto);

    $this->actingAs($administrador)
        ->delete(route('admin.productos.destroy', $producto))
        ->assertSessionHas('error');

    $this->delete(route('admin.usuarios.destroy', $cliente))
        ->assertSessionHas('error');

    expect(fn () => $producto->delete())
        ->toThrow(QueryException::class);

    expect(fn () => $cliente->delete())
        ->toThrow(QueryException::class);

    $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    $this->assertDatabaseHas('detalle_pedidos', ['pedido_id' => $pedido->id]);
    $this->assertDatabaseHas('pedidos', ['id' => $pedido->id]);
});

test('a pedido accepts only the existing business state transitions', function () {
    $pedido = new Pedido([
        'estado' => 'pendiente',
        'tipo_entrega' => 'recoger_local',
    ]);

    expect($pedido->puedeCambiarEstado('preparacion'))->toBeTrue()
        ->and($pedido->puedeCambiarEstado('entregado'))->toBeFalse();

    $pedido->estado = 'listo';

    expect($pedido->puedeCambiarEstado('entregado'))->toBeTrue()
        ->and($pedido->puedeCambiarEstado('en_reparto'))->toBeFalse();

    $pedido->tipo_entrega = 'contraentrega';

    expect($pedido->puedeCambiarEstado('en_reparto'))->toBeTrue();
});

test('cashiers cannot skip order states', function () {
    $cajero = Usuario::create([
        'email' => 'cajero@example.com',
        'password' => 'password123',
        'rol' => 'cajero',
    ]);
    $cliente = crearClienteDePrueba();
    $producto = crearProductoDePrueba();
    $pedido = crearPedidoDePrueba($cliente, $producto);

    $this->actingAs($cajero)
        ->patch(route('cajero.pedidos.cambiar_estado', $pedido), ['estado' => 'entregado'])
        ->assertSessionHas('error');

    $this->assertSame('pendiente', $pedido->refresh()->estado);

    $this->patch(route('cajero.pedidos.cambiar_estado', $pedido), ['estado' => 'preparacion'])
        ->assertSessionHas('success');

    $this->patch(route('cajero.pedidos.cambiar_estado', $pedido), ['estado' => 'listo'])
        ->assertSessionHas('success');

    $this->patch(route('cajero.pedidos.cambiar_estado', $pedido), ['estado' => 'entregado'])
        ->assertSessionHas('success');
});

test('a pedido can have only one payment and each profile is unique per user', function () {
    $usuario = crearClienteDePrueba();
    $producto = crearProductoDePrueba();
    $pedido = crearPedidoDePrueba($usuario, $producto);

    expect(fn () => Pago::create([
        'pedido_id' => $pedido->id,
        'monto' => '20.00',
        'metodo' => 'efectivo',
        'estado_pago' => 'pendiente',
    ]))->toThrow(QueryException::class);

    expect(fn () => Cliente::create([
        'usuario_id' => $usuario->id,
        'nombres' => 'Duplicado',
        'apellidos' => 'Perfil',
    ]))->toThrow(QueryException::class);
});

test('customer phone numbers are unique when present', function () {
    crearClienteDePrueba(['telefono' => '71234567']);

    expect(fn () => crearClienteDePrueba(['telefono' => '71234567']))
        ->toThrow(QueryException::class);
});

test('database seeder creates one payment per order with matching totals', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('pedidos', 40);
    $this->assertDatabaseCount('pagos', 40);

    foreach (Pedido::with('detallePedidos')->get() as $pedido) {
        $totalCentavos = $pedido->detallePedidos->sum(
            fn (DetallePedido $detalle) => (int) round((float) $detalle->precio_unitario * 100)
                * $detalle->cantidad
        );

        $this->assertEquals($totalCentavos / 100, (float) $pedido->total);
    }
});
