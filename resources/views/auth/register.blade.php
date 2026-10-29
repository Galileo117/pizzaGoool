<x-guest-layout>

    {{-- LOGO --}}
    <div class="mb-5 flex justify-center">
        <a href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Pizza Goool" class="h-20 w-20 object-contain sm:h-22 sm:w-22">
        </a>
    </div>

    {{-- ENCABEZADO --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
            Crear tu cuenta
        </h1>
        <p class="mt-3 text-sm leading-6 text-gray-500 sm:text-base">
            Únete a la familia de <span class="font-semibold text-goool-green">Pizza Goool</span>
        </p>
    </div>

    {{-- FORMULARIO --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- NOMBRES --}}
        <div>
            <label for="nombres" class="mb-2 block text-sm font-bold text-gray-800">Nombres</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-user text-gray-400"></i>
                </div>
                <input id="nombres" type="text" name="nombres" value="{{ old('nombres') }}" required autofocus
                    autocomplete="given-name" maxlength="100" placeholder="Tus nombres"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
            </div>
            @error('nombres')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600"><i
                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- APELLIDOS --}}
        <div>
            <label for="apellidos" class="mb-2 block text-sm font-bold text-gray-800">Apellidos</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-user text-gray-400"></i>
                </div>
                <input id="apellidos" type="text" name="apellidos" value="{{ old('apellidos') }}" required
                    autocomplete="family-name" maxlength="100" placeholder="Tus apellidos"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
            </div>
            @error('apellidos')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600"><i
                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- EMAIL --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-gray-800">Correo electrónico</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="username" maxlength="150" placeholder="tu@email.com"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
            </div>
            @error('email')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600"><i
                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- TELEFONO --}}
        <div>
            <label for="telefono" class="mb-2 block text-sm font-bold text-gray-800">
                Teléfono <span class="font-normal text-gray-400">(opcional)</span>
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-phone text-gray-400"></i>
                </div>
                <input id="telefono" type="phone" name="telefono" value="{{ old('telefono') }}" autocomplete="tel"
                    maxlength="15" placeholder="71234567"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
            </div>
            @error('telefono')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600"><i
                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- PASSWORD --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-bold text-gray-800">Contraseña</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-12 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-red-600"><i
                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- CONFIRMAR PASSWORD --}}
        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-bold text-gray-800">Confirmar
                contraseña</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Repite tu contraseña"
                    class="block h-14 w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-12 text-base text-gray-900 placeholder:text-gray-400 outline-none transition focus:border-goool-green focus:bg-white focus:ring-4 focus:ring-goool-green/10">
                <button type="button" onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        {{-- BOTÓN --}}
        <div class="pt-2">
            <button type="submit"
                class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-goool-green px-5 text-base font-bold text-white shadow-lg shadow-goool-green/15 transition hover:bg-goool-green-dark hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-goool-green/20 active:scale-[0.99]">
                <i class="fa-solid fa-user-plus"></i> Crear cuenta
            </button>
        </div>

        {{-- LOGIN --}}
        <div class="pt-3 text-center">
            <p class="text-sm text-gray-500 sm:text-base">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}"
                    class="font-bold text-goool-green transition hover:text-goool-green-dark">Inicia sesión</a>
            </p>
        </div>
    </form>

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
