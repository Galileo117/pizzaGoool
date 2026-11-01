<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Repartidor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Se cargan las relaciones para evitar consultas extra en la vista
        $query = Usuario::with(['cliente', 'empleado', 'repartidor'])->orderBy('created_at', 'desc');

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
        // Validaciones condicionales según el rol
        $reglas = [
            'email' => 'required|string|email|max:150|unique:usuarios,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:admin,cajero,repartidor,cliente',

            // Validaciones para nombres (comunes a todos)
            'nombres' => 'required|string|max:50|regex:/^[\pL\s]+$/u',
            'apellidos' => 'required|string|max:50|regex:/^[\pL\s]+$/u',
            'telefono' => 'nullable|string|regex:/^[67]\d{7}$/',

            // Validaciones exclusivas para Repartidores
            'ci' => 'required_if:rol,repartidor|nullable|string|max:15|unique:repartidores,ci',
            'placa' => 'required_if:rol,repartidor|nullable|string|max:15|unique:repartidores,placa',
            'licencia_conducir' => 'required_if:rol,repartidor|nullable|string|max:20|unique:repartidores,licencia_conducir',
        ];

        if ($request->rol === 'cliente') {
            $reglas['telefono'] .= '|unique:clientes,telefono';
        }

        $request->validate($reglas, [
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.regex' => 'El nombre solo debe contener letras.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'telefono.regex' => 'El teléfono debe ser un celular válido.',
            'ci.required_if' => 'El CI es obligatorio para repartidores.',
            'placa.required_if' => 'La placa es obligatoria para repartidores.',
            'licencia_conducir.required_if' => 'La licencia es obligatoria para repartidores.',
            'ci.unique' => 'Este CI ya está asignado a otro repartidor.',
            'placa.unique' => 'Esta placa ya está registrada.',
            'licencia_conducir.unique' => 'Esta licencia ya está registrada.',
        ]);

        // Usar transacción para guardar en las tablas correspondientes
        DB::transaction(function () use ($request) {

            // 1. Crear el usuario base
            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
            ]);

            // 2. Crear el perfil según el rol seleccionado
            if ($request->rol === 'cliente') {
                Cliente::create([
                    'usuario_id' => $usuario->id,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'telefono' => $request->telefono,
                ]);
            } elseif ($request->rol === 'repartidor') {
                Repartidor::create([
                    'usuario_id' => $usuario->id,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'telefono' => $request->telefono,
                    'ci' => $request->ci,
                    'vehiculo' => 'Moto', // Por defecto
                    'placa' => $request->placa,
                    'licencia_conducir' => $request->licencia_conducir,
                ]);
            } else {
                // Si es Admin o Cajero, va a la tabla empleados
                Empleado::create([
                    'usuario_id' => $usuario->id,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'telefono' => $request->telefono,
                    'cargo' => ($request->rol === 'admin') ? 'Administrador' : 'Cajero',
                ]);
            }
        });

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario y perfil creados correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        // Cargar las relaciones para tener acceso a los datos
        $usuario->load(['cliente', 'empleado', 'repartidor']);

        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        // Validaciones (similares al store)
        $reglas = [
            'email' => 'required|string|email|max:150|unique:usuarios,email,'.$usuario->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:admin,cajero,repartidor,cliente',
            'nombres' => 'required|string|max:50|regex:/^[\pL\s]+$/u',
            'apellidos' => 'required|string|max:50|regex:/^[\pL\s]+$/u',
            'telefono' => 'nullable|string|regex:/^[67]\d{7}$/',

            'ci' => 'required_if:rol,repartidor|nullable|string|max:15|unique:repartidores,ci,'.($usuario->repartidor ? $usuario->repartidor->id : 'NULL'),
            'placa' => 'required_if:rol,repartidor|nullable|string|max:15|unique:repartidores,placa,'.($usuario->repartidor ? $usuario->repartidor->id : 'NULL'),
            'licencia_conducir' => 'required_if:rol,repartidor|nullable|string|max:20|unique:repartidores,licencia_conducir,'.($usuario->repartidor ? $usuario->repartidor->id : 'NULL'),
        ];

        if ($request->rol === 'cliente') {
            $reglas['telefono'] = [
                'nullable',
                'string',
                'regex:/^[67]\d{7}$/',
                Rule::unique('clientes', 'telefono')->ignore($usuario->cliente?->id),
            ];
        }

        $request->validate($reglas, [
            'email.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nombres.regex' => 'El nombre solo debe contener letras.',
            'telefono.regex' => 'El teléfono debe ser un celular válido.',
            'ci.required_if' => 'El CI es obligatorio para repartidores.',
            'placa.required_if' => 'La placa es obligatoria para repartidores.',
            'licencia_conducir.required_if' => 'La licencia es obligatoria para repartidores.',
        ]);

        DB::transaction(function () use ($request, $usuario) {

            // 1. Actualizar el usuario base
            $usuario->email = $request->email;
            $usuario->rol = $request->rol;

            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            $usuario->save();

            // 2. Actualizar el perfil correspondiente
            if ($request->rol === 'cliente') {
                $usuario->cliente()->updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'nombres' => $request->nombres,
                        'apellidos' => $request->apellidos,
                        'telefono' => $request->telefono,
                    ]
                );
            } elseif ($request->rol === 'repartidor') {
                $usuario->repartidor()->updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'nombres' => $request->nombres,
                        'apellidos' => $request->apellidos,
                        'telefono' => $request->telefono,
                        'ci' => $request->ci,
                        'placa' => $request->placa,
                        'licencia_conducir' => $request->licencia_conducir,
                    ]
                );
            } else {
                $usuario->empleado()->updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'nombres' => $request->nombres,
                        'apellidos' => $request->apellidos,
                        'telefono' => $request->telefono,
                        'cargo' => ($request->rol === 'admin') ? 'Administrador' : 'Cajero',
                    ]
                );
            }
        });

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === (int) auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($usuario->pedidos()->exists() || $usuario->pedidosAsignados()->exists()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No se puede eliminar una cuenta asociada a pedidos; el historial debe conservarse.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
