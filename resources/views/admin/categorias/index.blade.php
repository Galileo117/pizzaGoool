{{-- Listado administrativo de categorías y accesos a crear, editar o eliminar. --}}
@extends('layouts.admin')

@section('title', 'Categorías - Panel de Administración')
@section('page_title', 'Gestión de Categorías')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Listado de Categorías</h2>
            <p class="text-gray-600 text-sm">Organiza los productos por categorías.</p>
        </div>
        <a href="{{ route('admin.categorias.create') }}" class="bg-goool-green text-white font-bold px-5 py-3 rounded-lg hover:bg-goool-green-dark transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <i class="fa-solid fa-plus"></i> Nueva Categoría
        </a>
    </div>

    {{-- Tabla (escritorio) --}}
    <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Descripción</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Productos</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categorias as $categoria)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $categoria->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-goool-green-light text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $categoria->productos_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.categorias.edit', $categoria->id) }}" class="text-blue-600 hover:text-blue-800 p-2"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-tags text-4xl mb-2"></i>
                            <p>No hay categorías registradas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">{{ $categorias->links() }}</div>
    </div>

    {{-- Tarjetas (móvil) --}}
    <div class="md:hidden space-y-4">
        @forelse($categorias as $categoria)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800">{{ $categoria->nombre }}</h3>
                        <span class="bg-goool-green-light text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ $categoria->productos_count }} prods
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
                </div>
                <div class="border-t border-gray-100 px-4 py-3 flex gap-2">
                    <a href="{{ route('admin.categorias.edit', $categoria->id) }}" class="flex-1 bg-blue-500 text-white text-center font-bold py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>
                    <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" class="flex-1" onsubmit="return confirm('¿Eliminar esta categoría?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white font-bold py-2 rounded-lg text-sm hover:bg-red-600 transition">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-tags text-4xl mb-2"></i>
                <p>No hay categorías registradas.</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $categorias->links() }}</div>
    </div>
@endsection