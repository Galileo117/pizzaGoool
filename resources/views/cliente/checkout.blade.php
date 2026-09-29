@extends('layouts.cliente')

@section('title', 'Checkout - Pizza Goool')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #mapa {
            height: 320px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }

        #mapa-container {
            display: none;
        }

        #mapa-container.visible {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 sm:py-8 lg:py-10">

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-goool-green flex items-center gap-3">
                <i class="fa-solid fa-credit-card text-goool-yellow"></i>
                Proceder al Pago
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mt-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-goool-green"></i>
                Revisa tu pedido y confirma los datos de entrega
            </p>
        </div>

        <form action="{{ route('cliente.procesar_pedido') }}" method="POST" class="space-y-6" id="form-checkout">
            @csrf

            {{-- Resumen del pedido --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-receipt"></i>
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
                                        <div class="w-8 h-8 bg-goool-green/10 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-pizza-slice text-goool-green text-sm"></i>
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

                            <div class="flex justify-between items-center pt-4 border-t-2 border-goool-green/20">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                                    Bs. {{ number_format($total, 2) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-cart-shopping text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-400 font-medium">Tu carrito está vacío</p>
                            <a href="{{ route('cliente.catalogo') }}" class="inline-block mt-3 text-goool-green hover:underline">
                                <i class="fa-solid fa-arrow-left"></i> Volver al catálogo
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tipo de entrega --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast"></i> Tipo de Entrega
                    </h2>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="tipo_entrega" value="recoger_local" class="mt-1 form-radio text-goool-green focus:ring-goool-green" checked>
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-store text-goool-green"></i> Recoger en Local
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Av. Principal #123, Zona Central</p>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="tipo_entrega" value="contraentrega" class="mt-1 form-radio text-goool-green focus:ring-goool-green">
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-truck text-goool-orange"></i> Delivery a Domicilio
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Contraentrega con costo adicional</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Método de pago --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-goool-green px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave"></i> Método de Pago
                    </h2>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="metodo_pago" value="efectivo" class="mt-1 form-radio text-goool-green focus:ring-goool-green" checked onchange="toggleQR(false)">
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-cash-register text-goool-green"></i> Efectivo
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Paga al recibir tu pedido</p>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-goool-green has-[:checked]:border-goool-green has-[:checked]:bg-goool-green/5">
                            <input type="radio" name="metodo_pago" value="qr" class="mt-1 form-radio text-goool-green focus:ring-goool-green" onchange="toggleQR(true)">
                            <div>
                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-qrcode text-goool-orange"></i> Código QR
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Escanea y paga con tu app</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Sección del QR --}}
                <div id="seccion-qr" class="hidden px-5 sm:px-6 py-6 bg-gray-50 border-t border-gray-200">
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-goool-green mb-2">
                            <i class="fa-solid fa-qrcode"></i> Escanea el Código QR
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ \App\Models\Configuracion::obtener('mensaje_qr', 'Escanea este código QR desde tu aplicación bancaria para realizar el pago.') }}
                        </p>

                        @php
                            $imagenQR = \App\Models\Configuracion::obtener('imagen_qr_estatico');
                        @endphp

                        @if ($imagenQR)
                            <div class="inline-block bg-white p-4 rounded-xl shadow-md border border-gray-200">
                                <img id="imagen-qr-pago" src="{{ asset('storage/' . $imagenQR) }}" alt="Código QR de Pago" class="w-56 h-56 sm:w-64 sm:h-64 object-contain mx-auto">
                            </div>

                            <div id="instrucciones-escritorio" class="mt-4 hidden sm:block">
                                <p class="text-sm text-gray-600">
                                    <i class="fa-solid fa-mobile-screen text-goool-green"></i>
                                    Escanea este código con tu aplicación bancaria desde tu celular.
                                </p>
                            </div>

                            <div id="instrucciones-movil" class="mt-4 hidden">
                                <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg text-sm text-left max-w-md mx-auto">
                                    <p class="font-bold mb-2">
                                        <i class="fa-solid fa-mobile-screen"></i> Estás desde tu celular
                                    </p>
                                    <ul class="space-y-1.5 text-xs">
                                        <li>
                                            <i class="fa-solid fa-check text-goool-green"></i>
                                            Guarda la imagen del QR en tu galería.
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check text-goool-green"></i>
                                            Abre tu app bancaria y usa la opción <strong>"Escanear desde galería"</strong>.
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-check text-goool-green"></i>
                                            O pide a alguien más que te escanee el QR.
                                        </li>
                                    </ul>

                                    <div class="mt-4">
                                        <button type="button" onclick="descargarQR()" class="w-full inline-flex items-center justify-center gap-2 bg-goool-green text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-goool-green-dark transition">
                                            <i class="fa-solid fa-download"></i> Guardar QR
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 mt-4">
                                <i class="fa-solid fa-circle-info"></i>
                                Una vez realizado el pago, confirma tu pedido. El cajero verificará la transacción.
                            </p>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                El código QR aún no ha sido configurado.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dirección de entrega (solo delivery) --}}
            <div id="direccion_contraentrega" class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden" style="display: none;">
                <div class="bg-goool-orange px-5 sm:px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fa-solid fa-location-dot"></i> Dirección de Entrega
                    </h2>
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label for="direccion" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Dirección Completa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="direccion" name="direccion"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200"
                            placeholder="Ej: Av. Siempre Viva 123, Zona Norte">
                    </div>

                    <div>
                        <label for="referencia" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Referencia (opcional)
                        </label>
                        <input type="text" id="referencia" name="referencia"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 transition-all duration-200"
                            placeholder="Ej: Piso 2, portón verde">
                    </div>

                    <div id="mapa-container">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            <i class="fa-solid fa-map-location-dot text-goool-green"></i>
                            Marca tu ubicación en el mapa (opcional pero recomendado)
                        </label>
                        <div id="mapa" class="border-2 border-gray-200"></div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Toca el mapa para marcar la ubicación exacta. Ayudará al repartidor a llegar más rápido.
                        </p>

                        <input type="hidden" name="latitud" id="latitud">
                        <input type="hidden" name="longitud" id="longitud">
                    </div>
                </div>
            </div>

            {{-- Total y botones --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4">

                    <div class="flex items-center gap-4">
                        <span class="text-gray-600 text-sm">Total a pagar</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-goool-green">
                            Bs. {{ number_format($total ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="{{ route('cliente.catalogo') }}"
                            class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-xl font-semibold transition-all duration-200 w-full sm:w-auto">
                            <i class="fa-solid fa-arrow-left"></i>
                            Cancelar
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-3 bg-goool-green hover:bg-goool-green-dark text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full sm:w-auto justify-center">
                            <i class="fa-solid fa-check-circle"></i>
                            Confirmar Pedido
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Variables globales del mapa
        let map, marker, mapaInicializado = false;

        /**
         * Muestra u oculta la sección del QR.
         */
        function toggleQR(mostrar) {
            const seccionQR = document.getElementById('seccion-qr');
            if (!seccionQR) return;

            if (mostrar) {
                seccionQR.classList.remove('hidden');
            } else {
                seccionQR.classList.add('hidden');
            }
        }

        /**
         * Descarga la imagen del QR.
         */
        function descargarQR() {
            const imagen = document.getElementById('imagen-qr-pago');
            if (!imagen) {
                if (typeof mostrarToast === 'function') {
                    mostrarToast('No se encontró la imagen del QR.', 'error');
                }
                return;
            }

            const link = document.createElement('a');
            link.href = imagen.src;
            link.download = 'PizzaGoool-QR.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            if (typeof mostrarToast === 'function') {
                mostrarToast('QR descargado. Ábrelo desde tu app bancaria.', 'success');
            }
        }

        /**
         * Detecta si el usuario está en móvil y muestra las instrucciones correctas.
         */
        function detectarDispositivo() {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            const instruccionesEscritorio = document.getElementById('instrucciones-escritorio');
            const instruccionesMovil = document.getElementById('instrucciones-movil');

            if (!instruccionesEscritorio || !instruccionesMovil) return;

            if (esMovil) {
                instruccionesEscritorio.classList.add('hidden');
                instruccionesMovil.classList.remove('hidden');
            } else {
                instruccionesEscritorio.classList.remove('hidden');
                instruccionesMovil.classList.add('hidden');
            }
        }

        /**
         * Inicializa el mapa de Leaflet.
         */
        function inicializarMapa() {
            if (mapaInicializado) return;
            mapaInicializado = true;

            const mapaElement = document.getElementById('mapa');
            if (!mapaElement) return;

            // Coordenadas por defecto: La Paz, Bolivia
            const latInicial = -16.5;
            const lngInicial = -68.15;

            map = L.map('mapa').setView([latInicial, lngInicial], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            map.on('click', function(e) {
                const { lat, lng } = e.latlng;

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                }

                document.getElementById('latitud').value = lat;
                document.getElementById('longitud').value = lng;

                marker.on('dragend', function() {
                    const pos = marker.getLatLng();
                    document.getElementById('latitud').value = pos.lat;
                    document.getElementById('longitud').value = pos.lng;
                });
            });
        }

        /**
         * Inicializa todos los eventos cuando el DOM está listo.
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Detectar móvil/escritorio
            detectarDispositivo();

            // Toggle dirección de entrega
            const radiosEntrega = document.querySelectorAll('input[name="tipo_entrega"]');
            const divDireccion = document.getElementById('direccion_contraentrega');
            const mapaContainer = document.getElementById('mapa-container');

            function toggleDireccion() {
                const selected = document.querySelector('input[name="tipo_entrega"]:checked');
                if (selected && selected.value === 'contraentrega') {
                    divDireccion.style.display = 'block';
                    mapaContainer.classList.add('visible');
                    setTimeout(inicializarMapa, 100);
                } else {
                    divDireccion.style.display = 'none';
                    mapaContainer.classList.remove('visible');
                }
            }

            radiosEntrega.forEach(r => r.addEventListener('change', toggleDireccion));
            toggleDireccion();
        });
    </script>
@endpush