<?php

use App\Models\Configuracion;
use App\Models\Usuario;

function crearAdminParaCostoEnvio(): Usuario
{
    return Usuario::create([
        'email' => 'admin-costo-envio@example.com',
        'password' => 'password123',
        'rol' => 'admin',
    ]);
}

it('stores a valid delivery charge with two decimal places', function () {
    $admin = crearAdminParaCostoEnvio();

    $this->actingAs($admin)
        ->post(route('admin.configuracion.guardar_costo_envio'), [
            'costo_envio' => '12.5',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('configuraciones', [
        'clave' => 'costo_envio',
        'valor' => '12.50',
    ]);
});

it('rejects invalid delivery charges without changing the saved value', function (string $amount, string $message) {
    $admin = crearAdminParaCostoEnvio();
    Configuracion::guardar('costo_envio', '8.00');

    $this->actingAs($admin)
        ->post(route('admin.configuracion.guardar_costo_envio'), [
            'costo_envio' => $amount,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors([
            'costo_envio' => $message,
        ]);

    $this->assertDatabaseHas('configuraciones', [
        'clave' => 'costo_envio',
        'valor' => '8.00',
    ]);
})->with([
    'negative amount' => ['-0.01', 'El monto no puede ser negativo.'],
    'amount above limit' => ['10000.00', 'El monto máximo permitido es 9999.99.'],
    'too many decimal places' => ['10.001', 'El monto puede tener como máximo dos decimales.'],
]);
