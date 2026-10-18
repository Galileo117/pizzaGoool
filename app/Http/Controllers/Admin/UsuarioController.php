<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::orderBy('created_at', 'desc');

        if ($request->has('rol') && $request->rol !== '') {
            $query->where('rol', $request->rol);
        }

        $usuarios = $query->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'email' => 'required|string|email|max:150|unique:usuarios,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:admin,cajero,repartidor,cliente',
            'telefono' => 'nullable|regex:/^[0-9]+$/|min:8|max:15|unique:usuarios,telefono',
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.max' => 'El nombre no puede superar los 50 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no pueden superar los 50 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'rol.required' => 'Debes seleccionar un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'telefono.regex' => 'El teléfono solo debe contener números, sin espacios ni símbolos.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max' => 'El teléfono no puede tener más de 15 dígitos.',
            'telefono.unique' => 'Este número de teléfono ya está registrado.',
        ]);

        Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'telefono' => $request->telefono,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            'email' => 'required|string|email|max:150|unique:usuarios,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:admin,cajero,repartidor,cliente',
            'telefono' => 'nullable|regex:/^[0-9]+$/|min:8|max:15|unique:usuarios,telefono,' . $usuario->id,
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.max' => 'El nombre no puede superar los 50 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no pueden superar los 50 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
            'email.unique' => 'Este correo ya está registrado en otro usuario.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'rol.required' => 'Debes seleccionar un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'telefono.regex' => 'El teléfono solo debe contener números, sin espacios ni símbolos.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max' => 'El teléfono no puede tener más de 15 dígitos.',
            'telefono.unique' => 'Este número de teléfono ya está registrado.',
        ]);

        $data = [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'rol' => $request->rol,
            'telefono' => $request->telefono,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === (int) auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
