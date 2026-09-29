@extends('layouts.admin')

@section('title', 'Usuarios - Panel de Administración')
@section('page_title', 'Gestión de Usuarios')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Listado de Usuarios</h2>
            <p class="text-gray-600 text-sm">Visualiza todos los usuarios del sistema.</p>
        </div>

        <!-- Filtro por rol -->
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex items-center gap-2">
            <select name="rol" class="border border-gray-300 rounded-lg p-2 text-sm focus:border-goool-green focus:outline-none">
                <option value="">Todos los roles</option>
                <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>Clientes</option>
                <option value="cajero" {{ request('rol') == 'cajero' ? 'selected' : '' }}>Cajeros</option>
                <option value="repartidor" {{ request('rol') == 'repartidor' ? 'selected' : '' }}>Repartidores</option>
                <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administradores</option>
            </select>
            <button type="submit" class="bg-goool-green text-white px-4 py-2 rounded-lg text-sm hover:bg-goool-green-dark transition">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Teléfono</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Rol</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Registrado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $usuario->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->telefono ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $colores = [
                                    'admin' => 'bg-red-100 text-red-700',
                                    'cajero' => 'bg-blue-100 text-blue-700',
                                    'repartidor' => 'bg-purple-100 text-purple-700',
                                    'cliente' => 'bg-green-100 text-green-700',
                                ];
                                $color = $colores[$usuario->rol] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="{{ $color }} text-xs font-bold px-2 py-1 rounded-full">
                                {{ ucfirst($usuario->rol) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $usuario->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-users text-4xl mb-2"></i>
                            <p>No hay usuarios registrados.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $usuarios->links() }}
        </div>
    </div>
@endsection