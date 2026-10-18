<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', Rule::unique(Usuario::class)->ignore($this->user()->id)],
            // Agregamos min:8 aquí
            'telefono' => ['nullable', 'string', 'min:8', 'max:15', 'regex:/^[0-9]+$/', Rule::unique(Usuario::class)->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.max' => 'El nombre no puede tener más de 100 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no pueden tener más de 100 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Este correo ya está en uso.',
            'telefono.regex' => 'El teléfono solo debe contener números.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max' => 'El teléfono no puede tener más de 15 dígitos.',
            'telefono.unique' => 'Este teléfono ya está registrado.',
        ];
    }
}
