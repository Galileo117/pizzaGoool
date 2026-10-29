@extends('layouts.admin')

@section('title', 'Usuarios - Panel de Administración')
@section('page_title', 'Gestión de Usuarios')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Listado de Usuarios</h2>
            <p class="text-gray-600 text-sm">Administra clientes, cajeros, repartidores y administradores.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex items-center gap-2">
                <select name="rol"
                    class="border border-gray-300 rounded-lg p-2 text-sm focus:border-goool-green focus:outline-none w-full sm:w-auto">
                    <option value="">Todos los roles</option>
                    <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>Clientes</option>
                    <option value="cajero" {{ request('rol') == 'cajero' ? 'selected' : '' }}>Cajeros</option>
                    <option value="repartidor" {{ request('rol') == 'repartidor' ? 'selected' : '' }}>Repartidores</option>
                    <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administradores</option>
                </select>
                <button type="submit"
                    class="bg-goool-green text-white px-4 py-2 rounded-lg text-sm hover:bg-goool-green-dark transition whitespace-nowrap">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </form>

            <a href="{{ route('admin.usuarios.create') }}"
                class="bg-goool-green text-white font-bold px-5 py-2 rounded-lg hover:bg-goool-green-dark transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    {{-- Tabla (escritorio) --}}
    <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre Completo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Teléfono</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Rol</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Registrado</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $usuario->nombre_completo }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $usuario->telefono_contacto ?? '—' }}</td>
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
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                                        class="text-blue-600 hover:text-blue-800 p-2" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if ($usuario->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-2"
                                                title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-users text-4xl mb-2"></i>
                                <p>No hay usuarios registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">{{ $usuarios->links() }}</div>
    </div>

    {{-- Tarjetas (móvil) --}}
    <div class="md:hidden space-y-4">
        @forelse($usuarios as $usuario)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-goool-green text-white flex items-center justify-center font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($usuario->nombre_completo, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 truncate">{{ $usuario->nombre_completo }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $usuario->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $usuario->telefono_contacto ?? 'Sin teléfono' }}</p>
                    </div>
                    @php
                        $colores = [
                            'admin' => 'bg-red-100 text-red-700',
                            'cajero' => 'bg-blue-100 text-blue-700',
                            'repartidor' => 'bg-purple-100 text-purple-700',
                            'cliente' => 'bg-green-100 text-green-700',
                        ];
                        $color = $colores[$usuario->rol] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="{{ $color }} text-xs font-bold px-2 py-1 rounded-full whitespace-nowrap">
                        {{ ucfirst($usuario->rol) }}
                    </span>
                </div>
                <div class="border-t border-gray-100 px-4 py-3 flex gap-2">
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                        class="flex-1 bg-blue-50 text-blue-600 border border-blue-100 text-center font-bold py-2 rounded-lg text-sm hover:bg-blue-500 hover:text-white transition-colors duration-200">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>
                    @if ($usuario->id !== auth()->id())
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" class="flex-1"
                            onsubmit="return confirm('¿Eliminar este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-50 text-red-600 border border-red-100 font-bold py-2 rounded-lg text-sm hover:bg-red-500 hover:text-white transition-colors duration-200">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-users text-4xl mb-2"></i>
                <p>No hay usuarios registrados.</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $usuarios->links() }}</div>
    </div>
@endsection
