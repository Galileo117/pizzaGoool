{{-- Inicio de sesión web; LoginRequest valida credenciales y limita intentos. --}}
<x-guest-layout>

    {{-- LOGO --}}
    <div class="mb-6 flex justify-center">

        <a href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-20 w-20 object-contain sm:h-24 sm:w-24">
        </a>

    </div>


    {{-- ENCABEZADO --}}
    <div class="mb-8 text-center">

        <h1
            class="text-2xl font-extrabold
                   tracking-tight
                   text-gray-900
                   sm:text-3xl">
            ¡Bienvenido de nuevo!
        </h1>

        <p class="mx-auto mt-3 max-w-sm text-sm leading-6 text-gray-500 sm:text-base">

            Inicia sesión para disfrutar de las
            <span class="font-semibold text-goool-green">
                mejores pizzas.
            </span>

        </p>

    </div>


    {{-- FORMULARIO --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">

        @csrf


        {{-- EMAIL --}}
        <div>

            <label for="email" class="mb-2 block text-sm font-bold text-gray-800">
                Correo electrónico
            </label>

            <div class="relative">

                <div
                    class="pointer-events-none
                           absolute inset-y-0 left-0
                           flex items-center pl-4">
                    <i class="fa-solid fa-envelope text-gray-400"></i>
                </div>

                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" placeholder="tu@email.com"
                    class="block h-14 w-full
                           rounded-xl
                           border border-gray-200
                           bg-gray-50
                           pl-11 pr-4
                           text-base text-gray-900
                           placeholder:text-gray-400
                           outline-none
                           transition
                           focus:border-goool-green
                           focus:bg-white
                           focus:ring-4
                           focus:ring-goool-green/10">

            </div>

            @error('email')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    {{ $message }}

                </p>
            @enderror

        </div>


        {{-- PASSWORD --}}
        <div>

            <div class="mb-2 flex items-center justify-between gap-3">

                <label for="password" class="text-sm font-bold text-gray-800">
                    Contraseña
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-xs font-semibold
                               text-goool-green
                               transition
                               hover:text-goool-green-dark
                               sm:text-sm">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

            </div>


            <div class="relative">

                <div
                    class="pointer-events-none
                           absolute inset-y-0 left-0
                           flex items-center pl-4">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>

                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="Ingresa tu contraseña"
                    class="block h-14 w-full
                           rounded-xl
                           border border-gray-200
                           bg-gray-50
                           pl-11 pr-12
                           text-base text-gray-900
                           placeholder:text-gray-400
                           outline-none
                           transition
                           focus:border-goool-green
                           focus:bg-white
                           focus:ring-4
                           focus:ring-goool-green/10">

                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0
                           flex items-center
                           px-4
                           text-gray-400
                           transition
                           hover:text-gray-600"
                    aria-label="Mostrar contraseña">
                    <i class="fa-solid fa-eye"></i>
                </button>

            </div>

            @error('password')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    {{ $message }}

                </p>
            @enderror

        </div>


        {{-- RECORDAR --}}
        <div>

            <label for="remember_me" class="flex cursor-pointer select-none items-center gap-3">

                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4
                           rounded
                           border-gray-300
                           text-goool-green
                           focus:ring-goool-green">

                <span class="text-sm text-gray-600">
                    Recordarme en este dispositivo
                </span>

            </label>

        </div>


        {{-- BOTÓN --}}
        <button type="submit"
            class="flex h-14 w-full
                   items-center justify-center
                   gap-2
                   rounded-xl
                   bg-goool-green
                   px-5
                   text-base font-bold
                   text-white
                   shadow-lg shadow-goool-green/15
                   transition
                   hover:bg-goool-green-dark
                   hover:shadow-xl
                   focus:outline-none
                   focus:ring-4
                   focus:ring-goool-green/20
                   active:scale-[0.99]">

            <i class="fa-solid fa-right-to-bracket"></i>

            Iniciar sesión

        </button>


        {{-- SEPARADOR --}}
        <div class="relative py-2">

            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-100"></div>
            </div>

            <div class="relative flex justify-center">

                <span class="bg-white px-3 text-xs text-gray-400">
                    o
                </span>

            </div>

        </div>


        {{-- REGISTRO --}}
        <div class="text-center">

            <p class="text-sm text-gray-500 sm:text-base">

                ¿No tienes una cuenta?

                <a href="{{ route('register') }}"
                    class="font-bold text-goool-green transition hover:text-goool-green-dark">
                    Regístrate aquí
                </a>

            </p>

        </div>

    </form>


    {{-- PASSWORD TOGGLE --}}
    <script>
        function togglePassword(inputId, button) {

            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }
        }
    </script>

</x-guest-layout>
