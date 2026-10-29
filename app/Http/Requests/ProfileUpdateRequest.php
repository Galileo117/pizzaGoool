<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        // Obtenemos el ID del cliente actual para ignorarlo en la validación de teléfono único
        $clienteId = $this->user()->cliente ? $this->user()->cliente->id : null;

        return [
            'nombres' => ['required', 'string', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:150',
                Rule::unique(Usuario::class)->ignore($this->user()->id)
            ],
            // El teléfono ahora es único en la tabla 'clientes'
            'telefono' => [
                'nullable',
                'string',
                'regex:/^[67]\d{7}$/',
                Rule::unique('clientes', 'telefono')->ignore($clienteId)
            ],
            // Nueva regla para la dirección
            'direccion_entrega' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.max' => 'El nombre no puede tener más de 50 caracteres.',
            'nombres.regex' => 'El nombre solo debe contener letras.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no pueden tener más de 50 caracteres.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras.',

            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Este correo ya está en uso.',

            'telefono.regex' => 'El teléfono debe ser un celular boliviano válido (8 dígitos, empezar con 6 o 7).',
            'telefono.unique' => 'Este teléfono ya está registrado en otra cuenta.',

            'direccion_entrega.max' => 'La dirección no puede tener más de 255 caracteres.',
        ];
    }
}
