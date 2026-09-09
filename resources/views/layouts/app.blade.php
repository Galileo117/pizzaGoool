<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pizza Goool')</title>

    <!-- Font Awesome por CDN (Garantizado) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen flex flex-col font-sans">

    <!-- Barra de navegación superior -->
    <header class="bg-goool-green text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('cliente.home') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-14 w-auto">
            </a>
            <nav class="space-x-4 text-sm font-semibold">
                @auth
                    <span class="text-gray-200"><i class="fa-solid fa-user"></i> Bienvenido,
                        {{ auth()->user()->nombre }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-200 hover:text-white"><i
                                class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-200 hover:text-white"><i
                            class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Pie de página -->
    <footer class="bg-goool-green-dark text-white text-center py-4">
        <p class="text-sm"><i class="fa-regular fa-copyright"></i> {{ date('Y') }} Pizza Goool. Todos los derechos
            reservados.</p>
    </footer>

</body>

</html>
