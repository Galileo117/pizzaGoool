{{-- Layout mínimo compartido por login, registro y flujos de contraseña/verificación. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pizza Goool') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 antialiased">

    <main class="relative min-h-screen overflow-hidden">

        {{-- Mancha verde superior izquierda --}}
        <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-goool-green/20 blur-3xl">
        </div>

        {{-- Mancha amarilla inferior derecha --}}
        <div
            class="pointer-events-none absolute -bottom-40 -right-40 h-[500px] w-[500px] rounded-full bg-goool-yellow/20 blur-3xl">
        </div>

        {{-- Patrón de puntos: Arriba derecha (SIEMPRE visible) --}}
        <div class="pointer-events-none absolute right-8 top-8 h-40 w-40 opacity-70">
            <div class="grid grid-cols-5 gap-4">
                @for ($i = 0; $i < 25; $i++)
                    <div class="h-3 w-3 rounded-full bg-goool-green"></div>
                @endfor
            </div>
        </div>

        {{-- Patrón de puntos: Abajo izquierda (SIEMPRE visible) --}}
        <div class="pointer-events-none absolute bottom-8 left-8 h-40 w-40 opacity-70">
            <div class="grid grid-cols-5 gap-4">
                @for ($i = 0; $i < 25; $i++)
                    <div class="h-3 w-3 rounded-full bg-goool-yellow"></div>
                @endfor
            </div>
        </div>

        {{-- Contenedor principal --}}
        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 sm:py-12">

            <div class="w-full max-w-md">

                {{-- Tarjeta --}}
                <div class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-xl shadow-gray-900/5">

                    <div class="px-6 py-8 sm:px-10 sm:py-10">
                        {{ $slot }}
                    </div>

                    {{-- Detalle decorativo inferior --}}
                    <div class="relative h-4 overflow-hidden">
                        <div class="absolute -bottom-8 left-0 h-16 w-full rounded-[50%] bg-goool-green/80"></div>
                    </div>

                </div>

                <div class="mt-5 text-center">
                    <p class="text-xs text-gray-400">
                        © {{ date('Y') }} Pizza Goool
                    </p>
                </div>

            </div>

        </div>

    </main>

</body>

</html>
