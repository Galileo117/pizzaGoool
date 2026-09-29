@extends('layouts.admin')

@section('title', 'Editar Usuario - Panel de Administración')
@section('page_title', 'Editar Usuario')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.usuarios.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        <h2 class="text-xl font-bold text-goool-green-dark mt-2">Editar Usuario: {{ $usuario->nombre }}</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="space-y-5" id="form-usuario">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Rol <span class="text-red-500">*</span></label>
                    <select name="rol" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                        <option value="admin" {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="cajero" {{ old('rol', $usuario->rol) == 'cajero' ? 'selected' : '' }}>Cajero</option>
                        <option value="repartidor" {{ old('rol', $usuario->rol) == 'repartidor' ? 'selected' : '' }}>Repartidor</option>
                        <option value="cliente" {{ old('rol', $usuario->rol) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                    </select>
                    @error('rol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                    @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm">
                <i class="fa-solid fa-info-circle"></i>
                Deja los campos de contraseña vacíos si no quieres cambiarla.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit" class="bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
                    <i class="fa-solid fa-save"></i> Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
@endsection