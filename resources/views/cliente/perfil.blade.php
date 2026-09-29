@extends('layouts.cliente')

@section('title', 'Mi Perfil - Pizza Goool')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-user text-goool-yellow"></i>
                Mi Perfil
            </h1>
            <p class="text-gray-500 mt-1">Administra tu información personal y tu cuenta.</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6">
                <i class="fa-solid fa-circle-check"></i> Perfil actualizado correctamente.
            </div>
        @endif

        {{-- Tarjeta del usuario --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-goool-green px-6 py-6 flex items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-bold text-xl">{{ auth()->user()->nombre }}</p>
                    <p class="text-gray-200 text-sm">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-2 bg-goool-yellow text-goool-green-dark text-xs font-bold px-3 py-1 rounded-full uppercase">
                        {{ auth()->user()->rol }}
                    </span>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <i class="fa-solid fa-phone text-goool-green text-xl"></i>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Teléfono</p>
                        <p class="font-bold text-gray-800">{{ auth()->user()->telefono ?? 'No registrado' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <i class="fa-solid fa-calendar text-goool-green text-xl"></i>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Miembro desde</p>
                        <p class="font-bold text-gray-800">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario para actualizar información --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="font-bold text-gray-800"><i class="fa-solid fa-pen text-goool-green"></i> Actualizar Información</h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="nombre" class="block text-sm font-bold text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', auth()->user()->nombre) }}" required
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}"
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200">
                </div>

                <button type="submit" class="bg-goool-green text-white font-bold px-6 py-3 rounded-xl hover:bg-goool-green-dark transition">
                    <i class="fa-solid fa-save"></i> Guardar cambios
                </button>
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
                    <button type="submit" class="bg-red-500 text-white font-bold px-6 py-3 rounded-xl hover:bg-red-600 transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection