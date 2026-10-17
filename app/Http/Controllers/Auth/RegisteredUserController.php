<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres' => ['required', 'string', 'max:100', 'min:2'],
            'apellidos' => ['required', 'string', 'max:100', 'min:2'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefono' => ['nullable', 'string', 'max:15', 'unique:usuarios,telefono', 'regex:/^[0-9]+$/'],
        ], [
            // Mensajes de error personalizados
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.min' => 'El nombre debe tener al menos 2 letras.',
            'nombres.max' => 'El nombre no puede exceder los 100 caracteres.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'apellidos.min' => 'El apellido debe tener al menos 2 letras.',
            'apellidos.max' => 'El apellido no puede exceder los 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo no puede exceder los 150 caracteres.',
            'email.unique' => 'Este correo ya está registrado.',
            'telefono.max' => 'El teléfono no puede tener más de 15 dígitos.',
            'telefono.unique' => 'Este teléfono ya está registrado en otra cuenta.',
            'telefono.regex' => 'El teléfono solo debe contener números.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol' => 'cliente',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('cliente.home'));
    }
}
