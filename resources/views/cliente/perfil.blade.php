@extends('layouts.cliente')

@section('title', 'Mi Perfil - Pizza Goool')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8 sm:py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-user text-goool-yellow"></i>
                Mi Perfil
            </h1>
            <p class="text-gray-500 mt-1">Administra tu información personal y tu cuenta.</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div
                class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5 text-lg"></i>
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">Perfil actualizado correctamente.</p>
                </div>
            </div>
        @endif

        {{-- Tarjeta del usuario --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden mb-8 relative">
            {{-- Fondo decorativo --}}
            <div class="h-32 bg-gradient-to-r from-goool-green to-goool-green-dark w-full absolute top-0 left-0"></div>

            <div class="px-6 sm:px-10 pt-16 pb-8 relative">
                <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6">

                    {{-- Avatar (Totalmente redondo) --}}
                    <div
                        class="w-28 h-28 rounded-full bg-white p-1.5 shadow-lg relative z-10 -mt-16 sm:-mt-20 flex-shrink-0">
                        <div
                            class="w-full h-full rounded-full bg-goool-yellow text-goool-green-dark flex items-center justify-center text-4xl font-extrabold overflow-hidden">
                            {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="text-center sm:text-left flex-1 sm:pb-2">
                        <h2 class="text-2xl font-extrabold text-gray-800">{{ auth()->user()->nombres }}
                            {{ auth()->user()->apellidos }}</h2>
                        <p class="text-gray-500 text-sm font-medium">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div
                            class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-goool-green text-xl flex-shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase font-bold tracking-wider">Teléfono</p>
                            <p class="font-bold text-gray-800">{{ auth()->user()->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div
                            class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-goool-green text-xl flex-shrink-0">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase font-bold tracking-wider">Miembro desde</p>
                            <p class="font-bold text-gray-800">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario para actualizar información --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-goool-green"></i>
                <h2 class="font-bold text-gray-800">Actualizar Información</h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nombres" class="block text-sm font-bold text-gray-700 mb-1">Nombres <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nombres" name="nombres"
                            value="{{ old('nombres', auth()->user()->nombres) }}" required maxlength="100"
                            class="w-full border-2 @error('nombres') border-red-500 @else border-gray-200 @enderror rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                        @error('nombres')
                            <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apellidos" class="block text-sm font-bold text-gray-700 mb-1">Apellidos <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="apellidos" name="apellidos"
                            value="{{ old('apellidos', auth()->user()->apellidos) }}" required maxlength="100"
                            class="w-full border-2 @error('apellidos') border-red-500 @else border-gray-200 @enderror rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                        @error('apellidos')
                            <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico <span
                                class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', auth()->user()->email) }}" required maxlength="150"
                            class="w-full border-2 @error('email') border-red-500 @else border-gray-200 @enderror rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                            value="{{ old('telefono', auth()->user()->telefono) }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); validarTelefono(this)" minlength="8"
                            maxlength="15"
                            class="w-full border-2 @error('telefono') border-red-500 @else border-gray-200 @enderror rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200"
                            placeholder="Ej: 71234567">

                        {{-- Mensaje de error dinámico (Vanilla JS) --}}
                        <p id="telefono-error" class="text-red-500 text-xs mt-1 font-medium hidden">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> El teléfono debe tener al menos 8 dígitos.
                        </p>

                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-goool-green text-white font-bold px-6 py-3 rounded-xl hover:bg-goool-green-dark transition-all duration-200 w-full sm:w-auto flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Cerrar sesión --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-gray-800">Cerrar sesión</h2>
                    <p class="text-sm text-gray-500">Cierra la sesión de tu cuenta en este dispositivo.</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="bg-red-50 text-red-600 border border-red-100 font-bold px-6 py-3 rounded-xl hover:bg-red-500 hover:text-white transition-all duration-200 w-full sm:w-auto flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Script para validar el teléfono en tiempo real --}}
    <script>
        function validarTelefono(input) {
            const errorMsg = document.getElementById('telefono-error');
            // Si el campo está vacío, no mostramos error (porque es opcional)
            if (input.value.length === 0) {
                errorMsg.classList.add('hidden');
                return;
            }
            // Si tiene menos de 8 dígitos, mostramos el error
            if (input.value.length < 8) {
                errorMsg.classList.remove('hidden');
            } else {
                errorMsg.classList.add('hidden');
            }
        }
    </script>
@endsection
