@extends('layouts.admin')

@section('title', 'Nueva Categoría - Panel de Administración')
@section('page_title', 'Nueva Categoría')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.categorias.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        <h2 class="text-xl font-bold text-goool-green-dark mt-2">Crear Nueva Categoría</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <form action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-5" id="form-categoria">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span
                        class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
                    <i class="fa-solid fa-save"></i> Guardar Categoría
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('form-categoria').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
