<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Registra cuentas web y crea su perfil según las reglas del formulario.
 */
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres' => ['required', 'string', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefono' => ['nullable', 'string', 'regex:/^[67]\d{7}$/', 'unique:clientes,telefono'],
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.regex' => 'El nombre solo puede contener letras.',
            'nombres.max' => 'El nombre no puede exceder los 50 caracteres.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'apellidos.regex' => 'El apellido solo puede contener letras.',
            'apellidos.max' => 'El apellido no puede exceder los 50 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'telefono.regex' => 'El teléfono debe ser un celular boliviano válido (8 dígitos, empezar con 6 o 7).',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Usar transacción para asegurar integridad de datos
        $user = DB::transaction(function () use ($request) {

            // 1. Crear el usuario para el login
            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'cliente',
            ]);

            // 2. Crear el perfil de cliente asociado
            Cliente::create([
                'usuario_id' => $usuario->id,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
            ]);

            return $usuario;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('cliente.home'));
    }
}
