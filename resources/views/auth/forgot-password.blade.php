{{-- Solicita el enlace de recuperación; los mensajes provienen del flujo de password broker. --}}
<x-guest-layout>

    <div class="w-full">

        {{-- Encabezado --}}
        <div class="text-center mb-8 sm:mb-10">

            <div class="flex justify-center mb-5">
                <div
                    class="flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-goool-green text-white shadow-md">
                    <i class="fa-solid fa-key text-2xl sm:text-3xl"></i>
                </div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                Recuperar contraseña
            </h1>

            <p class="mt-3 text-sm sm:text-base leading-relaxed text-gray-500 max-w-md mx-auto">
                ¿Olvidaste tu contraseña? Ingresa tu correo electrónico
                y te enviaremos un enlace para restablecerla.
            </p>

        </div>

        {{-- Mensaje de éxito --}}
        @if (session('status'))
            <div
                class="mb-6 flex items-start gap-3 p-4 rounded-xl
                        bg-green-50 border border-green-200 text-green-700">

                <i class="fa-solid fa-circle-check mt-0.5"></i>

                <p class="text-sm leading-relaxed">
                    {{ session('status') }}
                </p>

            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>

                <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                    Correo electrónico
                </label>

                <div class="relative">

                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>

                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="tu@email.com"
                        class="w-full pl-11 pr-4 py-3.5
                               text-base text-gray-900
                               bg-gray-50
                               border border-gray-200
                               rounded-xl
                               placeholder:text-gray-400
                               focus:bg-white
                               focus:border-goool-green
                               focus:ring-4 focus:ring-goool-green/10
                               outline-none
                               transition duration-200">

                </div>

                @error('email')
                    <p class="flex items-center gap-1.5 text-sm text-red-600 mt-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Botón --}}
            <button type="submit"
                class="w-full flex items-center justify-center gap-2
                       py-3.5 px-4
                       text-base font-bold text-white
                       bg-goool-green
                       rounded-xl
                       shadow-sm
                       hover:bg-goool-green-dark
                       hover:shadow-md
                       focus:outline-none
                       focus:ring-4 focus:ring-goool-green/20
                       active:scale-[0.99]
                       transition duration-200">
                <i class="fa-solid fa-paper-plane"></i>
                Enviar enlace de recuperación
            </button>

            {{-- Volver --}}
            <div class="pt-2 text-center">

                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2
                           text-sm sm:text-base
                           font-semibold text-gray-500
                           hover:text-goool-green
                           transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>

            </div>

        </form>

    </div>

</x-guest-layout>
