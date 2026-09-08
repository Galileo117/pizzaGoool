<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pizza Goool')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Barra de navegación superior -->
    <header class="bg-green-800 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('cliente.home') }}" class="text-2xl font-bold">🍕 Pizza Goool</a>
            <nav class="space-x-4">
                @auth
                    <span class="text-sm">Bienvenido, {{ auth()->user()->nombre }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:underline">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm hover:underline">Iniciar Sesión</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Pie de página -->
    <footer class="bg-gray-900 text-white text-center py-4">
        <p class="text-sm">&copy; {{ date('Y') }} Pizza Goool. Todos los derechos reservados.</p>
    </footer>

</body>

</html>
