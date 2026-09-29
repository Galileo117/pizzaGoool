<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pizza Goool') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 antialiased">

    <main class="relative min-h-screen overflow-hidden">

        {{-- =========================
            DECORACIÓN DE FONDO
        ========================== --}}

        {{-- Mancha verde superior izquierda --}}
        <div
            class="pointer-events-none absolute -left-32 -top-32
                   h-80 w-80 rounded-full
                   bg-goool-green/10
                   blur-3xl">
        </div>

        {{-- Mancha amarilla inferior derecha --}}
        <div
            class="pointer-events-none absolute -bottom-40 -right-40
                   h-96 w-96 rounded-full
                   bg-goool-yellow/10
                   blur-3xl">
        </div>

        {{-- Patrón de puntos --}}
        <div
            class="pointer-events-none absolute right-6 top-8
                   hidden h-32 w-32
                   opacity-40
                   sm:block">
            <div class="grid grid-cols-6 gap-3">
                @for ($i = 0; $i < 36; $i++)
                    <div class="h-1.5 w-1.5 rounded-full bg-goool-green/30"></div>
                @endfor
            </div>
        </div>


        {{-- =========================
            CONTENEDOR PRINCIPAL
        ========================== --}}

        <div
            class="relative z-10
                   flex min-h-screen
                   items-center justify-center
                   px-4 py-8
                   sm:px-6
                   sm:py-12">

            <div class="w-full max-w-md">

                {{-- =========================
                    TARJETA
                ========================== --}}

                <div
                    class="overflow-hidden
                           rounded-3xl
                           border border-gray-200/80
                           bg-white
                           shadow-xl shadow-gray-900/5">

                    {{-- Contenido --}}
                    <div class="px-6 py-8
                               sm:px-10 sm:py-10">

                        {{ $slot }}

                    </div>


                    {{-- =========================
                        DETALLE INFERIOR
                    ========================== --}}

                    <div class="relative h-4 overflow-hidden">

                        <div
                            class="absolute -bottom-8 left-0
                                   h-16 w-full
                                   rounded-[50%]
                                   bg-goool-green/80">
                        </div>

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
