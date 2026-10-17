<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Información del Perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Actualiza la información de tu cuenta y tu dirección de correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- NOMBRES --}}
        <div>
            <x-input-label for="nombres" value="Nombres" />
            <x-text-input id="nombres" name="nombres" type="text" class="mt-1 block w-full" :value="old('nombres', $user->nombres)"
                required autofocus autocomplete="given-name" maxlength="100" />
            @if ($errors->has('nombres'))
                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('nombres') }}</p>
            @endif
        </div>

        {{-- APELLIDOS --}}
        <div>
            <x-input-label for="apellidos" value="Apellidos" />
            <x-text-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" :value="old('apellidos', $user->apellidos)"
                required autocomplete="family-name" maxlength="100" />
            @if ($errors->has('apellidos'))
                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('apellidos') }}</p>
            @endif
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" maxlength="150" />
            @if ($errors->has('email'))
                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('email') }}</p>
            @endif
        </div>

        {{-- TELÉFONO --}}
        <div>
            <x-input-label for="telefono" value="Teléfono (opcional)" />
            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $user->telefono)"
                autocomplete="tel" maxlength="15" />
            @if ($errors->has('telefono'))
                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('telefono') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar Cambios</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">Guardado.</p>
            @endif
        </div>
    </form>
</section>
