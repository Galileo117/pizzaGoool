<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\DetallePedido;
use App\Models\Empleado;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\Usuario;

/**
 * @return array{pedido: Pedido, cliente: Usuario, cajero: Usuario, admin: Usuario, repartidor: Usuario}
 */
function crearPedidoDeliveryParaPaneles(): array
{
    $cliente = Usuario::create([
        'email' => 'cliente-paneles@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);
    Cliente::create([
        'usuario_id' => $cliente->id,
        'nombres' => 'Ana',
        'apellidos' => 'Cliente',
    ]);

    $cajero = Usuario::create([
        'email' => 'cajero-paneles@example.com',
        'password' => 'password123',
        'rol' => 'cajero',
    ]);
    Empleado::create([
        'usuario_id' => $cajero->id,
        'nombres' => 'Carlos',
        'apellidos' => 'Caja',
        'cargo' => 'Cajero',
    ]);

    $admin = Usuario::create([
        'email' => 'admin-paneles@example.com',
        'password' => 'password123',
        'rol' => 'admin',
    ]);
    Empleado::create([
        'usuario_id' => $admin->id,
        'nombres' => 'Alex',
        'apellidos' => 'Admin',
        'cargo' => 'Administrador',
    ]);

    $repartidor = Usuario::create([
        'email' => 'repartidor-paneles@example.com',
        'password' => 'password123',
        'rol' => 'repartidor',
    ]);
    Repartidor::create([
        'usuario_id' => $repartidor->id,
        'nombres' => 'Luis',
        'apellidos' => 'Repartidor',
        'telefono' => '70000000',
        'ci' => '1234567-P',
        'placa' => 'PANEL-1',
        'licencia_conducir' => 'LIC-PANEL-1',
    ]);

    $categoria = Categoria::create(['nombre' => 'Pizzas de prueba']);
    $producto = Producto::create([
        'categoria_id' => $categoria->id,
        'nombre' => 'Pizza de prueba',
        'descripcion' => 'Producto para paneles',
        'precio' => '25.00',
        'disponible' => true,
    ]);
    $pedido = Pedido::create([
        'usuario_id' => $cliente->id,
        'repartidor_id' => $repartidor->id,
        'total' => '32.50',
        'costo_envio' => '7.50',
        'estado' => 'en_reparto',
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'delivery',
        'direccion' => 'Calle de prueba 123',
    ]);
    DetallePedido::create([
        'pedido_id' => $pedido->id,
        'producto_id' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => '25.00',
    ]);

    return compact('pedido', 'cliente', 'cajero', 'admin', 'repartidor');
}

it('shows the saved delivery charge in each order panel', function () {
    ['pedido' => $pedido, 'cliente' => $cliente, 'cajero' => $cajero, 'admin' => $admin, 'repartidor' => $repartidor] = crearPedidoDeliveryParaPaneles();

    $this->actingAs($admin)
        ->get(route('admin.pedidos.index'))
        ->assertOk()
        ->assertSee('Envío: Bs. 7.50');

    $this->actingAs($cajero)
        ->get(route('cajero.pedidos.index'))
        ->assertOk()
        ->assertSee('Envío: Bs. 7.50');

    $this->actingAs($cajero)
        ->get(route('cajero.pedidos.show', $pedido))
        ->assertOk()
        ->assertSee('Costo de envío')
        ->assertSee('Bs. 7.50');

    $this->actingAs($repartidor)
        ->get(route('repartidor.pedidos.show', $pedido))
        ->assertOk()
        ->assertSee('Costo de envío')
        ->assertSee('Bs. 7.50')
        ->assertSee('Total a cobrar');

    $this->get(route('repartidor.pedidos.index'))
        ->assertOk()
        ->assertSee('Envío: Bs. 7.50');

    $this->actingAs($cliente)
        ->get(route('cliente.mis_pedidos'))
        ->assertOk()
        ->assertSee('Envío: Bs. 7.50');

    $this->get(route('cliente.seguimiento', $pedido))
        ->assertOk()
        ->assertSee('Costo de envío')
        ->assertSee('Bs. 7.50');
});

it('adds the configured charge to a web delivery order', function () {
    ['pedido' => $existingOrder, 'cliente' => $cliente] = crearPedidoDeliveryParaPaneles();
    $producto = $existingOrder->detallePedidos()->firstOrFail()->producto;
    Configuracion::guardar('costo_envio', '7.50');

    $this->actingAs($cliente)
        ->withSession([
            'carrito' => [
                $producto->id => [
                    'producto_id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => '25.00',
                    'cantidad' => 1,
                ],
            ],
        ])
        ->post(route('cliente.procesar_pedido'), [
            'metodo_pago' => 'efectivo',
            'tipo_entrega' => 'delivery',
            'direccion' => 'Calle de prueba 123',
        ])
        ->assertRedirect();

    $pedido = Pedido::query()
        ->where('usuario_id', $cliente->id)
        ->latest('id')
        ->firstOrFail();

    $this->assertDatabaseHas('pedidos', [
        'id' => $pedido->id,
        'tipo_entrega' => 'delivery',
        'total' => '32.50',
        'costo_envio' => '7.50',
    ]);
});

it('routes delivery and pickup orders through their correct cashier transitions', function () {
    ['pedido' => $delivery, 'cajero' => $cajero, 'repartidor' => $repartidor] = crearPedidoDeliveryParaPaneles();
    $delivery->update(['estado' => 'listo']);

    $pickupCustomer = Usuario::create([
        'email' => 'cliente-pickup-paneles@example.com',
        'password' => 'password123',
        'rol' => 'cliente',
    ]);
    Cliente::create([
        'usuario_id' => $pickupCustomer->id,
        'nombres' => 'Eva',
        'apellidos' => 'Pickup',
    ]);
    $pickup = Pedido::create([
        'usuario_id' => $pickupCustomer->id,
        'total' => '25.00',
        'costo_envio' => '0.00',
        'estado' => 'listo',
        'metodo_pago' => 'efectivo',
        'tipo_entrega' => 'local',
    ]);

    $this->actingAs($cajero)
        ->patch(route('cajero.pedidos.cambiar_estado', $delivery), [
            'estado' => 'entregado',
        ])
        ->assertRedirect()
        ->assertSessionHas('error', 'La transición solicitada no es válida para este pedido.');

    $this->assertDatabaseHas('pedidos', [
        'id' => $delivery->id,
        'estado' => 'listo',
    ]);

    $this->patch(route('cajero.pedidos.asignar_repartidor', $delivery), [
        'repartidor_id' => $repartidor->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('pedidos', [
        'id' => $delivery->id,
        'estado' => 'en_reparto',
        'repartidor_id' => $repartidor->id,
    ]);

    $this->patch(route('cajero.pedidos.cambiar_estado', $pickup), [
        'estado' => 'entregado',
    ])->assertRedirect();

    $this->assertDatabaseHas('pedidos', [
        'id' => $pickup->id,
        'estado' => 'entregado',
    ]);
});
