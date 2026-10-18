@extends('layouts.admin')

@section('title', 'Editar Usuario - Panel de Administración')
@section('page_title', 'Editar Usuario')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.usuarios.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        <h2 class="text-xl font-bold text-goool-green-dark mt-2 break-words">Editar Usuario: {{ $usuario->nombres }}
            {{ $usuario->apellidos }}</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="space-y-5" id="form-usuario">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombres <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres) }}" required
                        maxlength="50"
                        class="w-full border @error('nombres') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 focus:border-goool-green focus:outline-none transition-colors">
                    @error('nombres')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Apellidos <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}" required
                        maxlength="50"
                        class="w-full border @error('apellidos') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 focus:border-goool-green focus:outline-none transition-colors">
                    @error('apellidos')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                        maxlength="150"
                        class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 focus:border-goool-green focus:outline-none transition-colors">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" minlength="8" maxlength="15"
                        class="w-full border @error('telefono') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 focus:border-goool-green focus:outline-none transition-colors"
                        placeholder="Ej: 71234567">
                    @error('telefono')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Rol <span
                            class="text-red-500">*</span></label>
                    <select name="rol" required
                        class="w-full border @error('rol') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 focus:border-goool-green focus:outline-none transition-colors">
                        <option value="admin" {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}>Administrador
                        </option>
                        <option value="cajero" {{ old('rol', $usuario->rol) == 'cajero' ? 'selected' : '' }}>Cajero
                        </option>
                        <option value="repartidor" {{ old('rol', $usuario->rol) == 'repartidor' ? 'selected' : '' }}>
                            Repartidor</option>
                        <option value="cliente" {{ old('rol', $usuario->rol) == 'cliente' ? 'selected' : '' }}>Cliente
                        </option>
                    </select>
                    @error('rol')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div
                class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm flex items-start gap-2">
                <i class="fa-solid fa-info-circle mt-0.5"></i>
                <span>Deja los campos de contraseña vacíos si no quieres cambiarla.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg p-3 pr-10 focus:border-goool-green focus:outline-none transition-colors">
                        <button type="button" onclick="togglePassword('password', 'eye-password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-goool-green focus:outline-none">
                            <i class="fa-solid fa-eye" id="eye-password"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:border-goool-green focus:outline-none">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirmation')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-goool-green focus:outline-none">
                            <i class="fa-solid fa-eye" id="eye-confirmation"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition flex items-center justify-center gap-2 w-full sm:w-auto">
                    <i class="fa-solid fa-save"></i> Actualizar Usuario
                </button>
                <a href="{{ route('admin.usuarios.index') }}"
                    class="bg-gray-100 text-gray-700 font-bold px-6 py-3 rounded-lg hover:bg-gray-200 transition text-center w-full sm:w-auto">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endsection
