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

    /**
     * Registro de nuevos clientes.
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:usuarios,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'telefono' => 'nullable|string|max:20',
                'device_name' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol' => 'cliente',
        ]);

        $token = $usuario->createToken($request->device_name ?? 'app-movil')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
            ],
        ], 'Usuario registrado correctamente.', 201);
    }

    /**
     * Login: devuelve un token de acceso.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return $this->unauthorizedResponse('Las credenciales no coinciden.');
        }

        // Revocar tokens anteriores del mismo dispositivo
        $deviceName = $request->device_name ?? 'app-movil';
        $usuario->tokens()->where('name', $deviceName)->delete();

        $token = $usuario->createToken($deviceName)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
            ],
        ], 'Login exitoso.');
    }

    /**
     * Logout: revoca el token actual.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Sesión cerrada correctamente.');
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function user(Request $request)
    {
        return $this->successResponse([
            'usuario' => [
                'id' => $request->user()->id,
                'nombre' => $request->user()->nombre,
                'email' => $request->user()->email,
                'rol' => $request->user()->rol,
                'telefono' => $request->user()->telefono,
                'created_at' => $request->user()->created_at,
            ],
        ], 'Datos del usuario.');
    }
}
