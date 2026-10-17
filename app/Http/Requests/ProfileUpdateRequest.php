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
            'telefono' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/', Rule::unique(Usuario::class)->ignore($this->user()->id)],
        ];
    }
}
