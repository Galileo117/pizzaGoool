<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Cajero - Pizza Goool')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen">

        {{-- Sidebar escritorio --}}
        <aside class="w-64 bg-goool-green text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4 border-b border-goool-green-dark flex flex-col items-center">
                <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-20 w-auto mb-2">
                <span class="text-goool-yellow font-bold text-sm uppercase tracking-widest">Panel Cajero</span>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('cajero.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cajero.dashboard') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('cajero.pedidos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cajero.pedidos.*') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                    <i class="fa-solid fa-receipt"></i> Gestionar Pedidos
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

        {{-- Menú móvil --}}
        <div id="mobile-menu" class="fixed inset-0 z-50 hidden md:hidden">
            <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleMenu()"></div>
            <aside id="mobile-sidebar" class="relative w-72 bg-goool-green text-white h-full flex flex-col transform transition-transform duration-300 -translate-x-full">
                <div class="p-4 border-b border-goool-green-dark flex flex-col items-center">
                    <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-20 w-auto mb-2">
                    <span class="text-goool-yellow font-bold text-sm uppercase tracking-widest">Panel Cajero</span>
                </div>

                <nav class="flex-1 p-4 space-y-2">
                    <a href="{{ route('cajero.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cajero.dashboard') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                    <a href="{{ route('cajero.pedidos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('cajero.pedidos.*') ? 'bg-goool-green-dark font-bold' : 'hover:bg-goool-green-dark' }} transition">
                        <i class="fa-solid fa-receipt"></i> Gestionar Pedidos
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

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleMenu()" class="md:hidden text-goool-green text-2xl p-1">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="text-lg sm:text-xl font-bold text-goool-green">@yield('page_title', 'Panel de Cajero')</h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-xs sm:text-sm text-gray-600">
                        <i class="fa-solid fa-user-circle text-goool-green"></i>
                        <span class="hidden sm:inline">{{ auth()->user()->nombre }}</span>
                    </span>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
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

</body>
</html>