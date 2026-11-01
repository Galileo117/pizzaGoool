{{-- Layout autenticado de Breeze, usado por dashboard y formularios de cuenta. --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Pizza Goool - La mejor experiencia en pizzas')">
    <meta name="theme-color" content="#1B5E20">

    <title>@yield('title', 'Pizza Goool')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased flex flex-col">

    <header class="sticky top-0 z-50 bg-goool-green text-white shadow-lg" role="banner">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex h-20 items-center justify-between">

                <a href="{{ route('cliente.home') }}" class="flex shrink-0 items-center gap-3"
                    aria-label="Ir al inicio de Pizza Goool">

                    <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-16 w-auto object-contain"
                        style="height: 64px; width: auto;" loading="eager">

                    <span class="hidden text-3xl font-extrabold tracking-tight sm:inline">
                        Pizza<span class="text-goool-yellow">Goool</span>
                    </span>
                </a>

                <nav aria-label="Navegación de usuario" class="flex items-center gap-3 sm:gap-5">

                    @auth
                        <div class="flex items-center gap-3 sm:gap-5">
                            <div class="hidden items-center gap-2 text-sm text-gray-200 md:flex">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->nombre_completo, 0, 1)) }}
                                </div>
                                <span class="font-medium">{{ auth()->user()->nombre_completo }}</span>
                                <span
                                    class="hidden rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white lg:inline-block">
                                    {{ auth()->user()->rol }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-3.5 py-2 text-sm font-medium text-white transition-all duration-200 hover:bg-white hover:text-goool-green">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span class="hidden sm:inline">Salir</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-3 sm:gap-4">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-goool-green shadow-sm transition-all duration-200 hover:bg-goool-yellow">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>Iniciar Sesión</span>
                            </a>

                            <a href="{{ route('register') }}"
                                class="hidden items-center gap-2 rounded-lg border-2 border-white/30 px-4 py-2 text-sm font-medium text-white transition-all duration-200 hover:bg-white/10 sm:inline-flex">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Registrarse</span>
                            </a>
                        </div>
                    @endauth

                </nav>
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-white/10 bg-goool-green-dark text-gray-300">
        <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-5 md:flex-row">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" alt="" aria-hidden="true"
                        class="h-10 w-auto object-contain opacity-80" style="height: 40px; width: auto;" loading="lazy">
                    <span class="text-sm font-medium">
                        &copy; {{ now()->year }}
                        <span class="font-bold text-white">Pizza Goool</span>
                    </span>
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm">
                    <a href="#"
                        class="inline-flex items-center gap-1.5 rounded transition-colors duration-200 hover:text-white">
                        <i class="fa-regular fa-circle-question"></i>
                        <span>Ayuda</span>
                    </a>
                    <span class="text-gray-600">|</span>
                    <a href="#"
                        class="inline-flex items-center gap-1.5 rounded transition-colors duration-200 hover:text-white">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Contacto</span>
                    </a>
                </nav>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
