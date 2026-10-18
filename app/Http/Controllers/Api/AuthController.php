<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombres' => 'required|string|max:100',
                'apellidos' => 'nullable|string|max:100',
                'email' => 'required|string|email|max:150|unique:usuarios,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'telefono' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]{8,15}$/',
                    'unique:usuarios,telefono',
                ],
                'device_name' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }

        $usuario = Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol' => 'cliente',
        ]);

        $deviceName = $request->device_name ?? 'app-movil';

        $token = $usuario
            ->createToken($deviceName)
            ->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'nombre_completo' => trim(
                    $usuario->nombres . ' ' . ($usuario->apellidos ?? '')
                ),
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
            ],
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

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return $this->unauthorizedResponse(
                'Las credenciales no coinciden.'
            );
        }

        $deviceName = $request->device_name ?? 'app-movil';

        $usuario->tokens()
            ->where('name', $deviceName)
            ->delete();

        $token = $usuario
            ->createToken($deviceName)
            ->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'nombre_completo' => trim(
                    $usuario->nombres . ' ' . ($usuario->apellidos ?? '')
                ),
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
            ],
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
        $usuario = $request->user();

        return $this->successResponse([
            'usuario' => [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'nombre_completo' => trim(
                    $usuario->nombres . ' ' . ($usuario->apellidos ?? '')
                ),
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
                'created_at' => $usuario->created_at,
            ],
        ], 'Datos del usuario.');
    }
}
