<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pizza Goool')</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    
    <!-- Barra de navegación super limpia -->
    <header class="bg-goool-green text-white py-2 px-4 flex justify-between items-center shadow-md">
        <div class="flex items-center gap-2">
            <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-10 w-auto">
        </div>
        <nav class="flex items-center gap-3 text-sm font-semibold">
            @auth
                <span class="hidden sm:inline"><i class="fa-solid fa-user"></i> {{ auth()->user()->nombre }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-goool-yellow"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-goool-yellow">Iniciar Sesión</a>
            @endauth
        </nav>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-goool-green-dark text-white text-center py-3">
        <p class="text-xs"><i class="fa-regular fa-copyright"></i> 2026 Pizza Goool. Todos los derechos reservados.</p>
    </footer>

</body>
</html>