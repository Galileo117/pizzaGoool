<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1B5E20">
    <title>@yield('title', 'Pizza Goool')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased flex flex-col">

    {{-- ========================================
         HEADER
    ========================================= --}}
    <header class="sticky top-0 z-40 bg-goool-green text-white shadow-lg">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">

                {{-- Logo + menú hamburguesa --}}
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->rol === 'cliente')
                            <button onclick="toggleMenuCliente()" class="text-white text-2xl p-2 hover:bg-white/10 rounded-lg transition" aria-label="Abrir menú">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        @endif
                    @endauth

                    <a href="{{ route('cliente.home') }}" class="flex shrink-0 items-center gap-3">
                        <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-14 w-auto object-contain">
                        <span class="hidden text-2xl font-extrabold tracking-tight sm:inline">
                            Pizza<span class="text-goool-yellow">Goool</span>
                        </span>
                    </a>
                </div>

                {{-- Navegación derecha --}}
                <nav class="flex items-center gap-3 sm:gap-5" aria-label="Navegación principal">

                    @auth
                        @if(auth()->user()->rol === 'cliente')
                            {{-- Carrito con badge --}}
                            @php
                                $carritoHeader = session('carrito', []);
                                $cantidadHeader = 0;
                                foreach ($carritoHeader as $item) {
                                    $cantidadHeader += $item['cantidad'];
                                }
                            @endphp
                            <a href="{{ route('cliente.catalogo') }}#carrito"
                               class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 transition"
                               title="Ver carrito">
                                <i class="fa-solid fa-cart-shopping text-white text-lg"></i>
                                @if($cantidadHeader > 0)
                                    <span class="absolute -top-1 -right-1 bg-goool-yellow text-goool-green-dark text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                        {{ $cantidadHeader }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="flex items-center gap-3">
                            <div class="hidden md:flex items-center gap-2">
                                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                                </div>
                                <span class="font-medium text-sm">{{ auth()->user()->nombre }}</span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-3.5 py-2 text-sm hover:bg-white hover:text-goool-green transition">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span class="hidden sm:inline">Salir</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-goool-green hover:bg-goool-yellow transition">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>Iniciar Sesión</span>
                            </a>
                            <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-2 rounded-lg border-2 border-white/30 px-4 py-2 text-sm hover:bg-white/10 transition">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Registrarse</span>
                            </a>
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- ========================================
         MENÚ LATERAL DEL CLIENTE
    ========================================= --}}
    @auth
        @if(auth()->user()->rol === 'cliente')
            <div id="menu-cliente" class="fixed inset-0 z-50 hidden">
                <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleMenuCliente()"></div>

                <aside id="menu-cliente-sidebar" class="relative w-72 bg-goool-green text-white h-full flex flex-col transform transition-transform duration-300 -translate-x-full">
                    <div class="p-6 border-b border-goool-green-dark">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold truncate">{{ auth()->user()->nombre }}</p>
                                <p class="text-xs text-gray-300 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                        <a href="{{ route('cliente.home') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cliente.home') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                            <i class="fa-solid fa-house"></i> Inicio
                        </a>
                        <a href="{{ route('cliente.catalogo') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cliente.catalogo') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                            <i class="fa-solid fa-utensils"></i> Catálogo
                        </a>
                        <a href="{{ route('cliente.mis_pedidos') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cliente.mis_pedidos') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                            <i class="fa-solid fa-receipt"></i> Mis Pedidos
                        </a>
                        <a href="{{ route('cliente.perfil') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cliente.perfil') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                            <i class="fa-solid fa-user"></i> Mi Perfil
                        </a>
                    </nav>

                    <div class="p-4 border-t border-goool-green-dark">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-goool-green-dark transition text-left">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        @endif
    @endauth

    {{-- ========================================
         CONTENIDO PRINCIPAL
    ========================================= --}}
    <main id="main-content" class="flex-1">
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ========================================
         FOOTER MEJORADO
    ========================================= --}}
    <footer class="border-t border-white/10 bg-goool-green-dark text-gray-300 mt-auto">
        <div class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

                {{-- Logo + descripción --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-14 w-auto object-contain">
                        <span class="text-2xl font-extrabold text-white">
                            Pizza<span class="text-goool-yellow">Goool</span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                        La mejor pizza con pasión por el fútbol. Pide tu favorita y recíbela caliente en la puerta de tu casa.
                    </p>
                </div>

                {{-- Enlaces rápidos --}}
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Enlaces</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('cliente.home') }}" class="hover:text-goool-yellow transition">
                                <i class="fa-solid fa-chevron-right text-xs mr-2"></i>Inicio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cliente.catalogo') }}" class="hover:text-goool-yellow transition">
                                <i class="fa-solid fa-chevron-right text-xs mr-2"></i>Catálogo
                            </a>
                        </li>
                        @auth
                            @if(auth()->user()->rol === 'cliente')
                                <li>
                                    <a href="{{ route('cliente.mis_pedidos') }}" class="hover:text-goool-yellow transition">
                                        <i class="fa-solid fa-chevron-right text-xs mr-2"></i>Mis Pedidos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cliente.perfil') }}" class="hover:text-goool-yellow transition">
                                        <i class="fa-solid fa-chevron-right text-xs mr-2"></i>Mi Perfil
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                </div>

                {{-- Contacto --}}
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Contacto</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-goool-yellow mt-1"></i>
                            <span>Av. Principal #123, Zona Central<br>La Paz, Bolivia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-goool-yellow"></i>
                            <span>+591 71234567</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-goool-yellow"></i>
                            <span>contacto@pizzagoool.com</span>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Línea inferior --}}
            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm">
                    &copy; {{ now()->year }} <span class="font-bold text-white">Pizza Goool</span>. Todos los derechos reservados.
                </p>

                <div class="flex items-center gap-4 text-xl">
                    <a href="#" class="hover:text-goool-yellow transition" title="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="hover:text-goool-yellow transition" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="hover:text-goool-yellow transition" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="hover:text-goool-yellow transition" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

        </div>
    </footer>

    {{-- Contenedor de notificaciones tipo toast --}}
    <div id="toast-container" class="fixed top-24 right-4 z-[9999] space-y-3 pointer-events-none"></div>

    <script>
        function toggleMenuCliente() {
            const menu = document.getElementById('menu-cliente');
            const sidebar = document.getElementById('menu-cliente-sidebar');

            if (!menu || !sidebar) return;

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => sidebar.classList.remove('-translate-x-full'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                setTimeout(() => menu.classList.add('hidden'), 300);
            }
        }

        function mostrarToast(mensaje, tipo = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const colores = {
                success: { bg: 'bg-green-500', icon: 'fa-circle-check' },
                error:   { bg: 'bg-red-500',   icon: 'fa-circle-xmark' },
                warning: { bg: 'bg-yellow-500', icon: 'fa-triangle-exclamation' },
                info:    { bg: 'bg-blue-500',  icon: 'fa-circle-info' },
            };

            const color = colores[tipo] || colores.success;

            const toast = document.createElement('div');
            toast.className = `${color.bg} text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 min-w-[280px] max-w-[400px] pointer-events-auto transform translate-x-full transition-transform duration-300`;
            toast.innerHTML = `
                <i class="fa-solid ${color.icon} text-xl"></i>
                <span class="font-medium text-sm flex-1">${mensaje}</span>
                <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>