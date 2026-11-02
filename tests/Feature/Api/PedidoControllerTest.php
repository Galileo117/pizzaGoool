<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;

function crearClienteParaPedidoApi(): array
{
    $cliente = Usuario::create([
        'email' => 'api-cliente@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);

    Cliente::create([
        'usuario_id' => $cliente->id,
        'nombres' => 'Cliente',
        'apellidos' => 'API',
    ]);

    $categoria = Categoria::create(['nombre' => 'Pizzas API']);
    $producto = Producto::create([
        'categoria_id' => $categoria->id,
        'nombre' => 'Pizza API',
        'descripcion' => 'Producto de prueba',
        'precio' => '25.00',
        'disponible' => true,
    ]);

    return compact('cliente', 'producto');
}

it('saves the configured delivery fee and keeps the API delivery contract', function () {
    ['cliente' => $cliente, 'producto' => $producto] = crearClienteParaPedidoApi();
    Configuracion::guardar('costo_envio', '7.50');

    $response = $this->withToken($cliente->createToken('pedido-api')->plainTextToken)
        ->postJson('/api/pedidos', [
            'items' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
            ],
            'metodo_pago' => 'efectivo',
            'tipo_entrega' => 'contraentrega',
            'direccion' => 'Calle de prueba 123',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.total', 32.5)
        ->assertJsonPath('data.costo_envio', 7.5)
        ->assertJsonPath('data.tipo_entrega', 'contraentrega');

    $this->assertDatabaseHas('pedidos', [
        'usuario_id' => $cliente->id,
        'tipo_entrega' => 'delivery',
        'total' => '32.50',
        'costo_envio' => '7.50',
    ]);
});

it('stores API pickup orders without a delivery fee', function () {
    ['cliente' => $cliente, 'producto' => $producto] = crearClienteParaPedidoApi();
    Configuracion::guardar('costo_envio', '7.50');

    $response = $this->withToken($cliente->createToken('pedido-api')->plainTextToken)
        ->postJson('/api/pedidos', [
            'items' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
            ],
            'metodo_pago' => 'efectivo',
            'tipo_entrega' => 'recoger_local',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.total', 25)
        ->assertJsonPath('data.costo_envio', 0)
        ->assertJsonPath('data.tipo_entrega', 'recoger_local');

    $pedido = Pedido::query()->sole();

    expect($pedido->tipo_entrega)->toBe('local')
        ->and((float) $pedido->costo_envio)->toBe(0.0);
});
