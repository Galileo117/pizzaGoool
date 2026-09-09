@extends('layouts.app')

@section('title', 'Checkout - Pizza Goool')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 sm:py-8 lg:py-10">

        {{-- ==============================================================
         ENCABEZADO
    ============================================================== --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-credit-card text-goool-yellow" aria-hidden="true"></i>
                Proceder al Pago
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-goool-green" aria-hidden="true"></i>
                Revisa tu pedido y confirma los datos de entrega
            </p>
        </div>

        <form action="{{ route('cliente.procesar_pedido') }}" method="POST" class="space-y-6">
            @csrf

            {{-- ==============================================================
             RESUMEN DEL PEDIDO
        ============================================================== --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                        Resumen de tu Pedido
                        <span class="bg-goool-yellow text-goool-green-dark text-xs px-2.5 py-0.5 rounded-full font-bold">
                            {{ count(session('carrito', [])) }} items
                        </span>
                    </h2>
                </div>

                <div class="p-5 sm:p-6">
                    @php
                        $carrito = session('carrito', []);
                        $total = 0;
                    @endphp

                    @if (count($carrito) > 0)
                        <div class="space-y-3 divide-y divide-gray-100">
                            @foreach ($carrito as $item)
                                <div class="flex justify-between items-center pt-3 first:pt-0">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-goool-green/10 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-pizza-slice text-goool-green text-sm"
                                                aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-800">{{ $item['nombre'] }}</span>
                                            <span class="text-gray-400 text-sm ml-2">x{{ $item['cantidad'] }}</span>
                                        </div>
                                    </div>
                                    <span class="font-bold text-goool-green">
                                        Bs. {{ number_format($item['precio'] * $item['cantidad'], 2) }}
                                    </span>
                                </div>
                                @php $total += $item['precio'] * $item['cantidad']; @endphp
                            @endforeach

                            {{-- Total --}}
                            <div class="flex justify-between items-center pt-4 border-t-2 border-goool-green/20">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                                    Bs. {{ number_format($total, 2) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-cart-shopping text-4xl text-gray-300 mb-3" aria-hidden="true"></i>
                            <p class="text-gray-400 font-medium">Tu carrito está vacío</p>
                            <a href="{{ route('cliente.catalogo') }}"
                                class="inline-block mt-3 text-goool-green hover:underline">
                                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                Volver al catálogo
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==============================================================
             TIPO DE ENTREGA
        ============================================================== --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                        Tipo de Entrega
                    </h2>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <label
                            class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="tipo_entrega" value="recoger_local"
                                class="mt-1 form-radio text-goool-green focus:ring-goool-green" checked>
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-store text-goool-green" aria-hidden="true"></i>
                                    Recoger en Local
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Av. Principal #123, Zona Central</p>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="tipo_entrega" value="contraentrega"
                                class="mt-1 form-radio text-goool-green focus:ring-goool-green">
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-truck text-goool-orange" aria-hidden="true"></i>
                                    Delivery a Domicilio
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Contraentrega con costo adicional</p>
                            </div>
                        </label>

                    </div>
                </div>
            </div>

            {{-- ==============================================================
             MÉTODO DE PAGO
        ============================================================== --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave" aria-hidden="true"></i>
                        Método de Pago
                    </h2>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <label
                            class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="metodo_pago" value="efectivo"
                                class="mt-1 form-radio text-goool-green focus:ring-goool-green" checked>
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-cash-register text-goool-green" aria-hidden="true"></i>
                                    Efectivo
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Paga al recibir tu pedido</p>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="metodo_pago" value="qr"
                                class="mt-1 form-radio text-goool-green focus:ring-goool-green">
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-qrcode text-goool-orange" aria-hidden="true"></i>
                                    Código QR
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Escanea y paga con tu app</p>
                            </div>
                        </label>

                    </div>
                </div>
            </div>

            {{-- ==============================================================
             DIRECCIÓN DE ENTREGA (solo para contraentrega)
        ============================================================== --}}
            <div id="direccion_contraentrega" class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden"
                style="display: none;">
                <div class="bg-goool-orange px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Dirección de Entrega
                    </h2>
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label for="direccion" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Dirección Completa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="direccion" name="direccion"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200 placeholder:text-gray-400"
                            placeholder="Ej: Av. Siempre Viva 123, Zona Norte">
                    </div>

                    <div>
                        <label for="referencia" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Referencia (opcional)
                        </label>
                        <input type="text" id="referencia" name="referencia"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200 placeholder:text-gray-400"
                            placeholder="Ej: Piso 2, portón verde, cerca del parque">
                    </div>
                </div>
            </div>

            {{-- ==============================================================
             BOTÓN DE CONFIRMACIÓN
        ============================================================== --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4">

                    <div class="flex items-center gap-4">
                        <span class="text-gray-600 text-sm">Total a pagar</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                            Bs. {{ number_format($total ?? 0, 2) }}
                        </span>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-3 bg-goool-green hover:bg-goool-green-dark text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full sm:w-auto justify-center focus:outline-none focus:ring-2 focus:ring-goool-green focus:ring-offset-2"
                        aria-label="Confirmar pedido">
                        <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                        Confirmar Pedido
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ==============================================================
     SCRIPT PARA MOSTRAR/OCULTAR DIRECCIÓN
============================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="tipo_entrega"]');
            const direccionDiv = document.getElementById('direccion_contraentrega');

            // Función para actualizar la visibilidad
            function toggleDireccion() {
                const selected = document.querySelector('input[name="tipo_entrega"]:checked');
                if (selected && selected.value === 'contraentrega') {
                    direccionDiv.style.display = 'block';
                    // Añadir animación de entrada
                    direccionDiv.style.opacity = '0';
                    direccionDiv.style.transform = 'translateY(-10px)';
                    requestAnimationFrame(() => {
                        direccionDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        direccionDiv.style.opacity = '1';
                        direccionDiv.style.transform = 'translateY(0)';
                    });
                } else {
                    direccionDiv.style.display = 'none';
                }
            }

            // Ejecutar al cambiar
            radios.forEach(radio => {
                radio.addEventListener('change', toggleDireccion);
            });

            // Ejecutar al cargar para estado inicial
            toggleDireccion();
        });
    </script>

@endsection
