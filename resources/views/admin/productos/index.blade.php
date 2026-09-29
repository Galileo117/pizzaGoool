@extends('layouts.admin')

@section('title', 'Productos - Panel de Administración')
@section('page_title', 'Gestión de Productos')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-goool-green-dark">Listado de Productos</h2>
            <p class="text-gray-600 text-sm">Administra el catálogo de pizzas, bebidas y promociones.</p>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="bg-goool-green text-white font-bold px-5 py-3 rounded-lg hover:bg-goool-green-dark transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Nuevo Producto
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Imagen</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Categoría</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Precio</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Disponible</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($productos as $producto)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-goool-green-light/30 flex items-center justify-center">
                                    <i class="fa-solid fa-pizza-slice text-goool-green text-xl"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $producto->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td class="px-4 py-3 font-bold text-goool-green">Bs. {{ number_format($producto->precio, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($producto->disponible)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fa-solid fa-check"></i> Sí
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fa-solid fa-xmark"></i> No
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.productos.edit', $producto->id) }}" class="text-blue-600 hover:text-blue-800 p-2" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-pizza-slice text-4xl mb-2"></i>
                            <p>No hay productos registrados.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $productos->links() }}
        </div>
    </div>
@endsection