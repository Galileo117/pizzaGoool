{{-- Marco compartido del panel administrativo; las páginas hijas inyectan título y contenido. --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Administración - Pizza Goool')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <div class="flex min-h-screen">

        {{-- Sidebar (escritorio) --}}
        <aside class="w-64 bg-goool-green text-white flex-shrink-0 hidden md:flex flex-col shadow-xl z-20">
            {{-- Logo --}}
            <div class="p-5 border-b border-goool-green-dark/50 flex flex-col items-center">
                <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-32 w-auto mb-3 drop-shadow-md">
                <span class="text-goool-yellow font-bold text-sm uppercase tracking-widest">Panel Admin</span>
            </div>

            {{-- Navegación --}}
            <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar">
                <p class="px-4 text-xs font-bold text-goool-green-light uppercase tracking-wider mb-2 mt-2">Menú
                    Principal</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.productos.index') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.productos.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-pizza-slice w-5 text-center {{ request()->routeIs('admin.productos.*') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Productos
                </a>

                <a href="{{ route('admin.categorias.index') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.categorias.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-tags w-5 text-center {{ request()->routeIs('admin.categorias.*') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Categorías
                </a>

                <a href="{{ route('admin.pedidos.index') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.pedidos.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-receipt w-5 text-center {{ request()->routeIs('admin.pedidos.*') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Pedidos
                </a>

                <a href="{{ route('admin.usuarios.index') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.usuarios.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-users w-5 text-center {{ request()->routeIs('admin.usuarios.*') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Usuarios
                </a>

                <p class="px-4 text-xs font-bold text-goool-green-light uppercase tracking-wider mb-2 mt-6">Análisis y
                    Configuración</p>

                <a href="{{ route('admin.reportes.index') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reportes.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-chart-line w-5 text-center {{ request()->routeIs('admin.reportes.*') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Reportes
                </a>

                <a href="{{ route('admin.configuracion') }}"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.configuracion') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                    <i
                        class="fa-solid fa-qrcode w-5 text-center {{ request()->routeIs('admin.configuracion') ? 'text-goool-green' : 'text-white/70 group-hover:text-white' }}"></i>
                    Configurar QR
                </a>
            </nav>

            {{-- Cerrar sesión --}}
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

        {{-- Menú móvil (overlay) --}}
        <div id="mobile-menu" class="fixed inset-0 z-50 hidden md:hidden">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="toggleMenu()"></div>
            <aside id="mobile-sidebar"
                class="relative w-72 bg-goool-green text-white h-full flex flex-col transform transition-transform duration-300 -translate-x-full shadow-2xl">
                <div class="p-5 border-b border-goool-green-dark/50 flex flex-col items-center relative">
                    <button onclick="toggleMenu()"
                        class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl p-2"><i
                            class="fa-solid fa-xmark"></i></button>
                    <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-32 w-auto mb-3 drop-shadow-md">
                    <span class="text-goool-yellow font-bold text-sm uppercase tracking-widest">Panel Admin</span>
                </div>

                <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-gauge-high w-5 text-center"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.productos.index') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.productos.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-pizza-slice w-5 text-center"></i> Productos
                    </a>
                    <a href="{{ route('admin.categorias.index') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.categorias.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-tags w-5 text-center"></i> Categorías
                    </a>
                    <a href="{{ route('admin.pedidos.index') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.pedidos.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-receipt w-5 text-center"></i> Pedidos
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.usuarios.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i> Usuarios
                    </a>
                    <a href="{{ route('admin.reportes.index') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reportes.*') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i> Reportes
                    </a>
                    <a href="{{ route('admin.configuracion') }}"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.configuracion') ? 'bg-white text-goool-green shadow-sm font-bold' : 'text-white/90 hover:bg-goool-green-dark hover:text-white font-medium' }}">
                        <i class="fa-solid fa-qrcode w-5 text-center"></i> Configurar QR
                    </a>
                </nav>

                <div class="p-4 border-t border-goool-green-dark/50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-goool-green-dark hover:bg-red-500 text-white font-semibold transition-colors duration-200">
                            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        {{-- Contenido Principal --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Header --}}
            <header
                class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-3 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <button onclick="toggleMenu()"
                        class="md:hidden text-goool-green text-2xl p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-800 tracking-tight">@yield('page_title', 'Panel de Administración')</h1>
                </div>

                {{-- Perfil de usuario --}}
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        {{-- CORREGIDO: nombres y apellidos --}}
                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ auth()->user()->nombre_completo }}
                            {{ auth()->user()->apellidos }}</p>
                        <p class="text-[11px] font-semibold text-goool-green uppercase tracking-wider">
                            {{ auth()->user()->rol }}</p>
                    </div>

                    <div
                        class="w-10 h-10 rounded-full bg-green-100 text-goool-green flex items-center justify-center text-xl font-extrabold shadow-sm ring-2 ring-slate-100">
                        {{ strtoupper(substr(auth()->user()->nombre_completo, 0, 1)) }}
                    </div>


                </div>
            </header>

            {{-- Área de Contenido --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
                {{-- Alertas --}}
                @if (session('success'))
                    <div
                        class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-start gap-3">
                        <i class="fa-solid fa-circle-check mt-0.5 text-lg"></i>
                        <div>
                            <p class="font-bold">¡Éxito!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 shadow-sm flex items-start gap-3">
                        <i class="fa-solid fa-circle-xmark mt-0.5 text-lg"></i>
                        <div>
                            <p class="font-bold">Ocurrió un error</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const sidebar = document.getElementById('mobile-sidebar');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => sidebar.classList.remove('-translate-x-full'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                setTimeout(() => menu.classList.add('hidden'), 300);
            }
        }
    </script>

    <style>
        /* Scrollbar personalizado para el sidebar */
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
</body>

</html>
