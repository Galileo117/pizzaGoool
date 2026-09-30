@extends('layouts.admin')

@section('title', 'Nuevo Producto - Panel de Administración')
@section('page_title', 'Nuevo Producto')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.productos.index') }}" class="text-goool-green hover:underline">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        <h2 class="text-xl font-bold text-goool-green-dark mt-2">Crear Nuevo Producto</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
            id="form-producto">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría <span
                            class="text-red-500">*</span></label>
                    <select name="categoria_id" required
                        class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                        <option value="">-- Seleccionar --</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Precio (Bs.) <span
                            class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="7" max="999.99" name="precio" value="{{ old('precio') }}" required
                        onwheel="this.blur()"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none">
                    @error('precio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Imagen</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg p-2">
                    @error('imagen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fa-solid fa-image text-goool-green"></i> Vista previa:
                </p>

                <div class="flex justify-center">
                    <div class="text-center">
                        <div id="preview-container"
                            class="w-40 h-40 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300 overflow-hidden">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300" id="preview-icon"></i>
                            <img id="preview-image" src="#" alt="Vista previa"
                                class="w-full h-full object-cover hidden">
                        </div>
                        <p class="text-xs text-gray-400 mt-2" id="preview-text">Selecciona una imagen</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="disponible" id="disponible" value="1"
                    {{ old('disponible', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-goool-green focus:ring-goool-green">
                <label for="disponible" class="text-sm font-semibold text-gray-700">Disponible para la venta</label>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
                    <i class="fa-solid fa-save"></i> Guardar Producto
                </button>
            </div>
        </form>
    </div>

    <script>
        // Prevenir envío con Enter
        document.getElementById('form-producto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });

        // Vista previa de imagen
        document.getElementById('imagen').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('preview-image');
            const previewIcon = document.getElementById('preview-icon');
            const previewText = document.getElementById('preview-text');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    previewIcon.classList.add('hidden');
                    previewText.textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewImage.classList.add('hidden');
                previewIcon.classList.remove('hidden');
                previewText.textContent = 'Selecciona una imagen';
            }
        });
    </script>
@endsection
