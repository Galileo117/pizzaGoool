{{-- Administración del QR usado como medio de pago; envía cambios a ConfiguracionController. --}}
@extends('layouts.admin')

@section('title', 'Configuración - Panel de Administración')
@section('page_title', 'Configuración del Sistema')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-goool-green-dark">Configuración del Sistema</h2>
        <p class="text-gray-600 text-sm">Administra el código QR de pago y el costo de envío a domicilio.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Formularios -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Formulario 1: QR -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-goool-green px-6 py-4">
                    <h3 class="text-white font-bold"><i class="fa-solid fa-qrcode"></i> Imagen del QR Estático</h3>
                </div>

                <form action="{{ route('admin.configuracion.guardar_qr') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-5">
                    @csrf

                    <!-- Subir nueva imagen -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-image text-goool-green"></i> Imagen del QR
                        </label>
                        <input type="file" name="imagen_qr" accept="image/*"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none"
                            id="input-imagen-qr">
                        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.</p>
                        @error('imagen_qr')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mensaje personalizado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-comment-dots text-goool-green"></i> Mensaje para el cliente
                        </label>
                        <textarea name="mensaje_qr" rows="3"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-green focus:outline-none"
                            placeholder="Escanea este código QR desde tu aplicación bancaria...">{{ $mensajeQR }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Este mensaje se mostrará junto al QR en la pantalla de pago.
                        </p>
                        @error('mensaje_qr')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botón QR -->
                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="bg-goool-green text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-green-dark transition">
                            <i class="fa-solid fa-save"></i> Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>

            <!-- Formulario 2: Costo de Envío (Ahora es un formulario independiente) -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-goool-orange px-6 py-4">
                    <h3 class="text-white font-bold"><i class="fa-solid fa-truck"></i> Costo de Envío (Delivery)</h3>
                </div>

                <form action="{{ route('admin.configuracion.guardar_costo_envio') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-money-bill-wave text-goool-orange"></i> Monto del envío (Bs.)
                        </label>
                        <input type="number" step="0.01" min="0" max="9999.99" name="costo_envio"
                            value="{{ old('costo_envio', $costoEnvio) }}"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:border-goool-orange focus:outline-none"
                            placeholder="10.00" required>
                        <p class="text-xs text-gray-500 mt-1">Este monto se suma al total cuando el cliente elige "Delivery
                            a Domicilio".</p>
                        @error('costo_envio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="bg-goool-orange text-white font-bold px-6 py-3 rounded-lg hover:bg-goool-orange-dark transition">
                            <i class="fa-solid fa-save"></i> Guardar Costo de Envío
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Columna Derecha: Vista previa -->
        <div>
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700 text-sm">
                        <i class="fa-solid fa-eye text-goool-green"></i> Vista Previa Actual
                    </h3>
                </div>
                <div class="p-6">
                    @if ($imagenQR)
                        <div class="text-center">
                            <img id="preview-imagen" src="{{ asset('storage/' . $imagenQR) }}" alt="QR Configurado"
                                class="w-full max-w-[250px] mx-auto rounded-lg border-2 border-gray-200 bg-white p-2">
                            <p class="text-xs text-green-600 mt-3 font-semibold">
                                <i class="fa-solid fa-circle-check"></i> QR configurado correctamente
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div id="placeholder-sin-qr"
                                class="bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 p-6">
                                <i class="fa-solid fa-qrcode text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-500 font-semibold">Sin QR configurado</p>
                                <p class="text-xs text-gray-400 mt-1">Sube una imagen para activar el pago por QR.</p>
                            </div>
                            <img id="preview-imagen" src="" alt=""
                                class="hidden w-full max-w-[250px] mx-auto rounded-lg border-2 border-gray-200 bg-white p-2">
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mt-4 text-sm">
                <p class="font-semibold mb-2"><i class="fa-solid fa-lightbulb"></i> ¿Cómo obtener el QR?</p>
                <p>Pide a tu banco o billetera digital el QR estático de tu cuenta y guárdalo como imagen JPG o PNG.</p>
            </div>
        </div>
    </div>

    <!-- Preview dinámico al seleccionar imagen -->
    @push('scripts')
        <script>
            document.getElementById('input-imagen-qr').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('preview-imagen');
                    const placeholder = document.getElementById('placeholder-sin-qr');

                    preview.src = event.target.result;
                    preview.classList.remove('hidden');

                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        </script>
    @endpush
@endsection
