<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta tags para SEO y redes sociales --}}
    <meta name="description" content="Pizza Goool - Autenticación para acceder a la mejor experiencia en pizzas con pasión por el fútbol">
    <meta name="theme-color" content="#1B5E20">

    <title>{{ config('app.name', 'Pizza Goool') }} - @yield('title', 'Autenticación')</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Font Awesome para iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/..." crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Scripts de Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos específicos por página --}}
    @stack('styles')
</head>

<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo y marca --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-block transition-transform duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2 focus:ring-offset-white rounded-full" aria-label="Volver al inicio">
                <div class="relative">
                    <x-application-logo class="w-20 h-20 mx-auto text-goool-green dark:text-goool-yellow" />

                    {{-- Badge decorativo --}}
                    <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-goool-yellow text-[8px] font-bold text-goool-green-dark shadow-lg">
                        <i class="fa-solid fa-pizza-slice" aria-hidden="true"></i>
                    </span>
                </div>
            </a>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-goool-green dark:text-white">
                Pizza <span class="text-goool-yellow">Goool</span>
            </h1>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                @yield('subtitle', 'Accede a tu cuenta para disfrutar de las mejores pizzas')
            </p>
        </div>

        {{-- Tarjeta del formulario --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-300">

            {{-- Cabecera decorativa --}}
            <div class="h-1.5 bg-gradient-to-r from-goool-green via-goool-yellow to-goool-orange"></div>

            {{-- Contenido --}}
            <div class="p-6 sm:p-8">
                {{ $slot }}
            </div>

            {{-- Pie de la tarjeta --}}
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4 bg-gray-50/50 dark:bg-gray-900/50">
                <div class="flex flex-col items-center gap-2 text-xs text-gray-500 dark:text-gray-400 sm:flex-row sm:justify-between">

                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-lock" aria-hidden="true"></i>
                        <span>Conexión segura</span>
                    </span>

                    <span class="hidden sm:inline" aria-hidden="true">•</span>

                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                        <a href="#" class="hover:text-goool-green dark:hover:text-goool-yellow transition-colors duration-200">
                            ¿Necesitas ayuda?
                        </a>
                    </span>
                </div>
            </div>
        </div>

        {{-- Enlaces adicionales --}}
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            <span>¿Eres nuevo?</span>
            <a href="{{ route('register') }}" class="font-medium text-goool-green dark:text-goool-yellow hover:underline transition-colors duration-200">
                Crear cuenta
            </a>
            <span class="mx-2" aria-hidden="true">|</span>
            <a href="{{ route('cliente.home') }}" class="hover:text-goool-green dark:hover:text-goool-yellow transition-colors duration-200">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Volver al inicio
            </a>
        </div>
    </div>

    {{-- Scripts específicos por página --}}
    @stack('scripts')

</body>
</html>