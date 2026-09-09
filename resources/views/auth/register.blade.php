<x-guest-layout>

    {{-- ==============================================================
         ENCABEZADO DEL FORMULARIO
    ============================================================== --}}
    <div class="mb-6 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <i class="fa-solid fa-user-plus text-goool-yellow text-2xl" aria-hidden="true"></i>
            <h2 class="text-2xl font-extrabold text-goool-green dark:text-white">
                Crear Cuenta en <span class="text-goool-yellow">Pizza Goool</span>
            </h2>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Regístrate para disfrutar de las mejores pizzas con pasión por el fútbol
        </p>
    </div>

    {{-- ==============================================================
         FORMULARIO DE REGISTRO
    ============================================================== --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Nombre completo --}}
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-user text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="name"
                    class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Tu nombre completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-sm" />
        </div>

        {{-- Correo electrónico --}}
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
                    autocomplete="username"
                    placeholder="tu@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-sm" />
        </div>

        {{-- Teléfono (opcional) --}}
        <div>
            <x-input-label for="telefono" :value="__('Teléfono (opcional)')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-phone text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="telefono"
                    class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="tel"
                    name="telefono"
                    :value="old('telefono')"
                    placeholder="Ej: 71234567" />
            </div>
            <x-input-error :messages="$errors->get('telefono')" class="mt-1.5 text-sm" />
        </div>

        {{-- Contraseña --}}
        <div>
            <x-input-label for="password" :value="__('Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-10 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres" />
                <button type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-goool-green rounded-lg"
                        aria-label="Mostrar contraseña">
                    <i class="fa-solid fa-eye" id="toggleIcon" aria-hidden="true"></i>
                </button>
            </div>
            <div class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-info-circle" aria-hidden="true"></i>
                La contraseña debe tener al menos 8 caracteres
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-sm" />
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-shield-check text-gray-400" aria-hidden="true"></i>
                </div>
                <x-text-input id="password_confirmation"
                    class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-goool-green focus:ring-2 focus:ring-goool-green/20 dark:focus:ring-goool-yellow/20 transition-all duration-200 dark:bg-gray-700 dark:text-white"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repite tu contraseña" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-sm" />
        </div>

        {{-- Términos y condiciones --}}
        <div class="flex items-start gap-2 pt-1">
            <input id="terms"
                   type="checkbox"
                   name="terms"
                   required
                   class="mt-0.5 w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 text-goool-green focus:ring-goool-green focus:ring-offset-2 dark:focus:ring-goool-yellow transition-all duration-200 cursor-pointer">
            <label for="terms" class="text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
                Acepto los
                <a href="#" class="text-goool-green hover:text-goool-green-dark dark:text-goool-yellow dark:hover:text-yellow-400 font-medium transition-colors duration-200">
                    Términos y Condiciones
                </a>
                y la
                <a href="#" class="text-goool-green hover:text-goool-green-dark dark:text-goool-yellow dark:hover:text-yellow-400 font-medium transition-colors duration-200">
                    Política de Privacidad
                </a>
            </label>
        </div>

        {{-- Botón de envío --}}
        <x-primary-button class="w-full justify-center py-3 text-base font-bold bg-goool-green hover:bg-goool-green-dark focus:ring-2 focus:ring-goool-green focus:ring-offset-2 dark:focus:ring-goool-yellow transition-all duration-200 rounded-xl shadow-md hover:shadow-lg active:scale-[0.98]">
            <i class="fa-solid fa-user-plus mr-2" aria-hidden="true"></i>
            {{ __('Crear Cuenta') }}
        </x-primary-button>

        {{-- Enlace de inicio de sesión --}}
        <div class="text-center pt-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="font-semibold text-goool-green hover:text-goool-green-dark dark:text-goool-yellow dark:hover:text-yellow-400 transition-colors duration-200">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </form>

    {{-- ==============================================================
         SCRIPT PARA MOSTRAR/OCULTAR CONTRASEÑA
    ============================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Cambiar el icono
                    if (type === 'text') {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>

</x-guest-layout>   