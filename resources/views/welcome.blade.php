<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pizza Goool | Gestión de Pedidos y Delivery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-md">
            <h1 class="text-4xl font-bold text-green-700">🍕 Pizza Goool</h1>
            <p class="text-gray-600 mt-4">Sistema Web para la Gestión de Pedidos y Delivery</p>

            <div class="mt-8 space-y-4">
                <a href="{{ route('login') }}"
                    class="block w-full py-3 bg-green-700 text-white font-bold rounded-lg hover:bg-green-800">
                    Iniciar Sesión
                </a>

                <!-- Si quieres que los clientes se registren desde aquí, descomenta esto: -->
                <!-- <a href="{{ route('register') }}" class="block w-full py-3 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600">
                    Registrarse
                </a> -->
            </div>
        </div>
    </div>
</body>

</html>
