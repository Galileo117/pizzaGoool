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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased flex flex-col">

    {{-- HEADER --}}
    <header class="sticky top-0 z-40 bg-goool-green text-white shadow-lg">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-4">

                {{-- Logo + menú hamburguesa --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    @auth
                        @if (auth()->user()->rol === 'cliente')
                            <button onclick="toggleMenuCliente()"
                                class="text-white text-2xl p-2 hover:bg-white/10 rounded-lg transition-colors duration-200"
                                aria-label="Abrir menú">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        @endif
                    @endauth

                    <a href="{{ route('cliente.home') }}" class="flex shrink-0 items-center gap-3 group">
                        <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool"
                            class="h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                        <span class="hidden text-2xl font-extrabold tracking-tight sm:inline">
                            Pizza<span class="text-goool-yellow">Goool</span>
                        </span>
                    </a>
                </div>

                {{-- Barra de búsqueda (solo en pantallas medianas+) --}}
                @if (auth()->check() && auth()->user()->rol === 'cliente')
                    <form action="{{ route('cliente.catalogo') }}" method="GET"
                        class="hidden lg:flex flex-1 max-w-md">
                        <div class="relative w-full group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i
                                    class="fa-solid fa-search text-white/60 group-focus-within:text-goool-green transition-colors duration-200"></i>
                            </div>
                            <input type="text" name="buscar" placeholder="Buscar pizzas, bebidas..."
                                value="{{ request('buscar') }}"
                                class="w-full pl-11 pr-4 py-2.5 rounded-full bg-white/10 border border-white/20 text-white placeholder-white/60 focus:bg-white focus:text-gray-900 focus:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-goool-yellow transition-all duration-200">
                        </div>
                    </form>
                @endif

                {{-- Navegación derecha --}}
                <nav class="flex items-center gap-3 sm:gap-5 flex-shrink-0" aria-label="Navegación principal">

                    @auth
                        @if (auth()->user()->rol === 'cliente')
                            {{-- Carrito con badge --}}
                            @php
                                $carritoHeader = session('carrito', []);
                                $cantidadHeader = 0;
                                foreach ($carritoHeader as $item) {
                                    $cantidadHeader += $item['cantidad'];
                                }
                            @endphp
                            <a href="{{ route('cliente.catalogo') }}#carrito"
                                class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 transition-colors duration-200 group"
                                title="Ver carrito">
                                <i
                                    class="fa-solid fa-cart-shopping text-white text-lg group-hover:scale-110 transition-transform duration-200"></i>
                                @if ($cantidadHeader > 0)
                                    <span
                                        class="absolute -top-1 -right-1 bg-goool-yellow text-goool-green-dark text-[11px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-sm">
                                        {{ $cantidadHeader }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="flex items-center gap-3">
                            <div class="hidden md:flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold shadow-sm ring-1 ring-white/30">
                                    {{-- CORREGIDO: nombres --}}
                                    {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                                </div>
                                {{-- CORREGIDO: nombres --}}
                                <span class="font-semibold text-sm hidden lg:inline">{{ auth()->user()->nombres }}</span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold hover:bg-white hover:text-goool-green transition-all duration-200">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span class="hidden sm:inline">Salir</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-goool-green hover:bg-goool-yellow transition-all duration-200 shadow-sm">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>Iniciar Sesión</span>
                            </a>
                            <a href="{{ route('register') }}"
                                class="hidden sm:inline-flex items-center gap-2 rounded-xl border-2 border-white/30 px-4 py-2.5 text-sm font-semibold hover:bg-white/10 transition-all duration-200">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Registrarse</span>
                            </a>
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- MENÚ LATERAL DEL CLIENTE --}}
    @auth
        @if (auth()->user()->rol === 'cliente')
            <div id="menu-cliente" class="fixed inset-0 z-50 hidden">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="toggleMenuCliente()">
                </div>

                <aside id="menu-cliente-sidebar"
                    class="relative w-72 bg-goool-green text-white h-full flex flex-col transform transition-transform duration-300 -translate-x-full shadow-2xl">

                    <div class="p-6 border-b border-goool-green-dark/50 bg-goool-green-dark/20">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-xl font-bold shadow-inner ring-2 ring-white/20">
                                {{-- CORREGIDO: nombres --}}
                                {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                {{-- CORREGIDO: nombres y apellidos --}}
                                <p class="font-bold text-lg truncate">{{ auth()->user()->nombres }}
                                    {{ auth()->user()->apellidos }}</p>
                                <p class="text-xs text-white/70 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar">
                        <p class="px-4 text-xs font-bold text-goool-green-light uppercase tracking-wider mb-2 mt-2">Mi
                            Cuenta</p>

                        <a href="{{ route('cliente.home') }}"
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('cliente.home') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                            <i
                                class="fa-solid fa-house w-5 text-center {{ request()->routeIs('cliente.home') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                            Inicio
                        </a>
                        <a href="{{ route('cliente.catalogo') }}"
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('cliente.catalogo') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                            <i
                                class="fa-solid fa-utensils w-5 text-center {{ request()->routeIs('cliente.catalogo') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                            Catálogo
                        </a>
                        <a href="{{ route('cliente.mis_pedidos') }}"
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('cliente.mis_pedidos') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                            <i
                                class="fa-solid fa-receipt w-5 text-center {{ request()->routeIs('cliente.mis_pedidos') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                            Mis Pedidos
                        </a>
                        <a href="{{ route('cliente.perfil') }}"
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('cliente.perfil') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                            <i
                                class="fa-solid fa-user w-5 text-center {{ request()->routeIs('cliente.perfil') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                            Mi Perfil
                        </a>
                    </nav>

                    <div class="p-4 border-t border-goool-green-dark/50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-goool-green-dark hover:bg-red-500 text-white font-semibold transition-colors duration-200 shadow-sm">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        @endif
    @endauth

    {{-- CONTENIDO PRINCIPAL --}}
    <main id="main-content" class="flex-1">
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div
                    class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-0.5 text-lg"></i>
                    <div>
                        <p class="font-bold">¡Éxito!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div
                    class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-xmark mt-0.5 text-lg"></i>
                    <div>
                        <p class="font-bold">Ocurrió un error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-white/10 bg-goool-green-dark text-gray-300 mt-auto">
        <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

            {{-- Contenido principal del Footer --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">

                {{-- Logo + descripción --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-14 w-auto object-contain">
                        <span class="text-2xl font-extrabold text-white tracking-tight">
                            Pizza<span class="text-goool-yellow">Goool</span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                        La mejor pizza con pasión por el fútbol. Pide tu favorita y recíbela caliente en la puerta de tu
                        casa.
                    </p>
                </div>

                {{-- Enlaces rápidos --}}
                <div>
                    <h4 class="text-white font-bold mb-5 uppercase text-xs tracking-widest">Enlaces</h4>
                    <ul class="space-y-3 text-sm font-medium">
                        <li>
                            <a href="{{ route('cliente.home') }}"
                                class="hover:text-goool-yellow transition-colors duration-200 flex items-center gap-2">
                                <i class="fa-solid fa-chevron-right text-[10px] text-goool-yellow/50"></i>Inicio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cliente.catalogo') }}"
                                class="hover:text-goool-yellow transition-colors duration-200 flex items-center gap-2">
                                <i class="fa-solid fa-chevron-right text-[10px] text-goool-yellow/50"></i>Catálogo
                            </a>
                        </li>
                        @auth
                            @if (auth()->user()->rol === 'cliente')
                                <li>
                                    <a href="{{ route('cliente.mis_pedidos') }}"
                                        class="hover:text-goool-yellow transition-colors duration-200 flex items-center gap-2">
                                        <i class="fa-solid fa-chevron-right text-[10px] text-goool-yellow/50"></i>Mis
                                        Pedidos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cliente.perfil') }}"
                                        class="hover:text-goool-yellow transition-colors duration-200 flex items-center gap-2">
                                        <i class="fa-solid fa-chevron-right text-[10px] text-goool-yellow/50"></i>Mi Perfil
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                </div>

                {{-- Contacto --}}
                <div>
                    <h4 class="text-white font-bold mb-5 uppercase text-xs tracking-widest">Contacto</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot text-goool-yellow text-xs"></i>
                            </div>
                            <span class="leading-relaxed">Av. Mario Mercado<br>(a media cuadra del mercado Bajo
                                Llojeta)<br>La Paz, Bolivia</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-regular fa-clock text-goool-yellow text-xs"></i>
                            </div>
                            <span class="leading-relaxed">Lunes a domingo<br>17:00 a 22:00 <span
                                    class="text-white/60 text-xs">(5:00 PM – 10:00 PM)</span></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-goool-yellow text-xs"></i>
                            </div>
                            <span>+591 71234567</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-goool-yellow text-xs"></i>
                            </div>
                            <span>contacto@pizzagoool.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Línea inferior (Copyright y Redes) --}}
            <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <p class="text-sm text-gray-400 text-center sm:text-left">
                    &copy; {{ now()->year }} <span class="font-bold text-white tracking-wide">Pizza Goool</span>.
                    Todos los derechos reservados.
                </p>

                <div class="flex items-center gap-3">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 hover:bg-goool-yellow hover:text-goool-green-dark flex items-center justify-center transition-all duration-300"
                        title="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 hover:bg-goool-yellow hover:text-goool-green-dark flex items-center justify-center transition-all duration-300"
                        title="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 hover:bg-goool-yellow hover:text-goool-green-dark flex items-center justify-center transition-all duration-300"
                        title="TikTok">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-white/5 hover:bg-goool-yellow hover:text-goool-green-dark flex items-center justify-center transition-all duration-300"
                        title="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
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
                success: {
                    bg: 'bg-green-500',
                    icon: 'fa-circle-check'
                },
                error: {
                    bg: 'bg-red-500',
                    icon: 'fa-circle-xmark'
                },
                warning: {
                    bg: 'bg-yellow-500',
                    icon: 'fa-triangle-exclamation'
                },
                info: {
                    bg: 'bg-blue-500',
                    icon: 'fa-circle-info'
                },
            };

            const color = colores[tipo] || colores.success;
            const toast = document.createElement('div');

            toast.className =
                `${color.bg} text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 min-w-[280px] max-w-[400px] pointer-events-auto transform translate-x-full transition-all duration-300`;
            toast.innerHTML = `
                <i class="fa-solid ${color.icon} text-xl"></i>
                <span class="font-medium text-sm flex-1">${mensaje}</span>
                <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('translate-x-full'));

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>

    @stack('scripts')
</body>

</html>
