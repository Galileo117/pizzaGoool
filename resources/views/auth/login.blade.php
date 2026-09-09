<x-guest-layout>

    {{-- ==============================================================
         ENCABEZADO DEL FORMULARIO
    ============================================================== --}}
    <div class="mb-6 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <i class="fa-solid fa-pizza-slice text-goool-yellow text-2xl" aria-hidden="true"></i>
            <h2 class="text-2xl font-extrabold text-goool-green dark:text-white">
                Bienvenido a <span class="text-goool-yellow">Pizza Goool</span>
            </h2>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Inicia sesión para disfrutar de las mejores pizzas
        </p>
    </div>

    {{-- ==============================================================
         FORMULARIO DE LOGIN
    ============================================================== --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-envelope text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-sm" />
        </div>

        {{-- Contraseña --}}
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs font-medium text-goool-green hover:text-goool-green-dark dark:text-goool-yellow dark:hover:text-yellow-400 transition-colors duration-200">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-sm" />
        </div>

        {{-- Recordarme --}}
        <div class="flex items-center">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer group">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 text-goool-green focus:ring-goool-green focus:ring-offset-2 dark:focus:ring-goool-yellow transition-all duration-200 cursor-pointer">
                <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-200">
                    {{ __('Recordarme en este dispositivo') }}
                </span>
            </label>
        </div>

        {{-- Botón de envío --}}
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 py-3 text-base font-bold text-white bg-goool-green hover:bg-goool-green-dark focus:ring-2 focus:ring-goool-green focus:ring-offset-2 dark:focus:ring-goool-yellow transition-all duration-200 rounded-xl shadow-md hover:shadow-lg active:scale-[0.98]">
            <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
            {{ __('Iniciar Sesión') }}
        </button>

        {{-- Enlace de registro --}}
        <div class="text-center pt-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="font-semibold text-goool-green hover:text-goool-green-dark dark:text-goool-yellow dark:hover:text-yellow-400 transition-colors duration-200">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </form>

</x-guest-layout>