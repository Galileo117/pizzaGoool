<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * Gestiona cuentas/token Sanctum y proyecta los datos del perfil de cliente.
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $datos = $request->validate([
                'nombre' => 'required_without:nombres|string|max:50',
                'nombres' => 'required_without:nombre|string|max:50',
                'apellidos' => 'nullable|string|max:50',
                'email' => 'required|string|email|max:150|unique:usuarios,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'telefono' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]{8,15}$/',
                    'unique:clientes,telefono',
                ],
                'device_name' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }

        // Cuenta y perfil se crean juntos para evitar usuarios cliente incompletos.
        $usuario = DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'rol' => 'cliente',
            ]);

            Cliente::create([
                'usuario_id' => $usuario->id,
                'nombres' => filled($datos['nombres'] ?? null)
                    ? $datos['nombres']
                    : $datos['nombre'],
                'apellidos' => $datos['apellidos'] ?? '',
                'telefono' => $datos['telefono'] ?? null,
            ]);

            return $usuario->load('cliente');
        });

        $deviceName = $datos['device_name'] ?? 'app-movil';

        $token = $usuario
            ->createToken($deviceName)
            ->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => $this->datosDeUsuario($usuario),
        ], 'Usuario registrado correctamente.', 201);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:150',
                'password' => 'required|string',
                'device_name' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return $this->unauthorizedResponse(
                'Las credenciales no coinciden.'
            );
        }

        $usuario->loadMissing('cliente');
        $deviceName = $request->device_name ?? 'app-movil';

        $usuario->tokens()
            ->where('name', $deviceName)
            ->delete();

        $token = $usuario
            ->createToken($deviceName)
            ->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => $this->datosDeUsuario($usuario),
        ], 'Login exitoso.');
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return $this->successResponse(
            null,
            'Sesión cerrada correctamente.'
        );
    }

    public function user(Request $request)
    {
        $usuario = $request->user()->loadMissing('cliente');

        return $this->successResponse([
            'usuario' => $this->datosDeUsuario($usuario) + [
                'created_at' => $usuario->created_at,
            ],
        ], 'Datos del usuario.');
    }

    private function datosDeUsuario(Usuario $usuario): array
    {
        $cliente = $usuario->cliente;

        return [
            'id' => $usuario->id,
            'nombres' => $cliente?->nombres,
            'apellidos' => $cliente?->apellidos,
            'nombre_completo' => trim(
                ($cliente?->nombres ?? '').' '.($cliente?->apellidos ?? '')
            ),
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'telefono' => $cliente?->telefono,
        ];
    }
}
