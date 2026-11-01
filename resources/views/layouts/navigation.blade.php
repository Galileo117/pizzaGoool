{{-- Navegación Breeze para escritorio y móvil; los enlaces dependen de sesión y rol. --}}
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm"
    role="navigation" aria-label="Navegación principal">

    {{-- ==============================================================
        BARRA DE NAVEGACIÓN PRINCIPAL
    ============================================================== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20">

            {{-- Columna izquierda: Logo y enlaces --}}
            <div class="flex items-center">

                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="block transition-transform duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2 rounded-lg"
                        aria-label="Ir al panel de control">
                        <x-application-logo
                            class="block h-9 w-auto fill-current text-goool-green dark:text-goool-yellow" />
                    </a>
                </div>

                {{-- Enlaces de navegación (solo visible en desktop) --}}
                <div class="hidden sm:flex sm:items-center sm:ms-8 lg:ms-12">
                    <div class="flex space-x-1 lg:space-x-2">

                        {{-- Enlace al Dashboard --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-gauge-high mr-2" aria-hidden="true"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        {{-- Enlace al Catálogo (para clientes) --}}
                        @if (auth()->user()->rol === 'cliente')
                            <x-nav-link :href="route('cliente.catalogo')" :active="request()->routeIs('cliente.catalogo')"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-book-open mr-2" aria-hidden="true"></i>
                                {{ __('Catálogo') }}
                            </x-nav-link>
                        @endif

                        {{-- Enlace a Pedidos (para cajeros y repartidores) --}}
                        @if (in_array(auth()->user()->rol, ['cajero', 'repartidor']))
                            <x-nav-link :href="route('pedidos.index')" :active="request()->routeIs('pedidos.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-clipboard-list mr-2" aria-hidden="true"></i>
                                {{ __('Pedidos') }}
                            </x-nav-link>
                        @endif

                        {{-- Enlace a Usuarios (solo admin) --}}
                        @if (auth()->user()->rol === 'admin')
                            <x-nav-link :href="route('admin.usuarios')" :active="request()->routeIs('admin.usuarios')"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-users mr-2" aria-hidden="true"></i>
                                {{ __('Usuarios') }}
                            </x-nav-link>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Dropdown de usuario --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                {{-- Badge de rol --}}
                <span
                    class="hidden lg:inline-flex items-center gap-1.5 mr-3 px-2.5 py-1 rounded-full bg-goool-green/10 dark:bg-goool-yellow/10 text-xs font-semibold text-goool-green dark:text-goool-yellow border border-goool-green/20 dark:border-goool-yellow/20">
                    <i class="fa-solid fa-circle text-[6px]" aria-hidden="true"></i>
                    {{ ucfirst(auth()->user()->rol) }}
                </span>

                {{-- Dropdown de usuario --}}
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 border border-transparent hover:border-gray-300 dark:hover:border-gray-600">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-goool-green dark:bg-goool-yellow flex items-center justify-center text-white dark:text-goool-green-dark font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 1)) }}
                                </div>
                                <span
                                    class="hidden lg:inline-block font-semibold">{{ Auth::user()->nombre_completo }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Información del usuario --}}
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ Auth::user()->nombre_completo }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ Auth::user()->email }}
                            </p>
                            <p class="mt-1 text-xs">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-goool-green/10 dark:bg-goool-yellow/10 text-goool-green dark:text-goool-yellow text-[10px] font-medium">
                                    <i class="fa-solid fa-shield-alt" aria-hidden="true"></i>
                                    {{ ucfirst(Auth::user()->rol) }}
                                </span>
                            </p>
                        </div>

                        {{-- Enlaces del menú --}}
                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fa-solid fa-user w-4 text-gray-400" aria-hidden="true"></i>
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (auth()->user()->rol === 'cliente')
                                <x-dropdown-link :href="route('cliente.mis-pedidos')"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <i class="fa-solid fa-receipt w-4 text-gray-400" aria-hidden="true"></i>
                                    {{ __('Mis Pedidos') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('cliente.home')"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fa-solid fa-home w-4 text-gray-400" aria-hidden="true"></i>
                                {{ __('Ir al Inicio') }}
                            </x-dropdown-link>
                        </div>

                        {{-- Cerrar sesión --}}
                        <div class="border-t border-gray-200 dark:border-gray-600 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                                    <i class="fa-solid fa-right-from-bracket w-4" aria-hidden="true"></i>
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Botón Hamburguesa (Móvil) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-goool-yellow focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                    aria-expanded="false" :aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-menu"
                    aria-label="Abrir menú de navegación">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==============================================================
        MENÚ MÓVIL RESPONSIVE
    ============================================================== --}}
    <div id="mobile-menu" :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden border-t border-gray-200 dark:border-gray-700"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2">

        {{-- Enlaces principales --}}
        <div class="pt-2 pb-3 space-y-0.5">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                <i class="fa-solid fa-gauge-high w-5 text-gray-400" aria-hidden="true"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (auth()->user()->rol === 'cliente')
                <x-responsive-nav-link :href="route('cliente.catalogo')" :active="request()->routeIs('cliente.catalogo')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                    <i class="fa-solid fa-book-open w-5 text-gray-400" aria-hidden="true"></i>
                    {{ __('Catálogo') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(auth()->user()->rol, ['cajero', 'repartidor']))
                <x-responsive-nav-link :href="route('pedidos.index')" :active="request()->routeIs('pedidos.*')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                    <i class="fa-solid fa-clipboard-list w-5 text-gray-400" aria-hidden="true"></i>
                    {{ __('Pedidos') }}
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()->rol === 'admin')
                <x-responsive-nav-link :href="route('admin.usuarios')" :active="request()->routeIs('admin.usuarios')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                    <i class="fa-solid fa-users w-5 text-gray-400" aria-hidden="true"></i>
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif
        </div>

        {{-- Opciones de perfil --}}
        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">

            {{-- Información del usuario --}}
            <div class="flex items-center px-4 mb-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 rounded-full bg-goool-green dark:bg-goool-yellow flex items-center justify-center text-white dark:text-goool-green-dark font-bold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 1)) }}
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-semibold text-gray-800 dark:text-white">
                        {{ Auth::user()->nombre_completo }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ Auth::user()->email }}
                    </div>
                    <div class="mt-0.5">
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-goool-green/10 dark:bg-goool-yellow/10 text-goool-green dark:text-goool-yellow text-[10px] font-medium">
                            <i class="fa-solid fa-shield-alt" aria-hidden="true"></i>
                            {{ ucfirst(Auth::user()->rol) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Enlaces de perfil --}}
            <div class="mt-3 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                    <i class="fa-solid fa-user w-5 text-gray-400" aria-hidden="true"></i>
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                @if (auth()->user()->rol === 'cliente')
                    <x-responsive-nav-link :href="route('cliente.mis-pedidos')" :active="request()->routeIs('cliente.mis-pedidos')"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                        <i class="fa-solid fa-receipt w-5 text-gray-400" aria-hidden="true"></i>
                        {{ __('Mis Pedidos') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('cliente.home')"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
                    <i class="fa-solid fa-home w-5 text-gray-400" aria-hidden="true"></i>
                    {{ __('Ir al Inicio') }}
                </x-responsive-nav-link>

                {{-- Cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fa-solid fa-right-from-bracket w-5" aria-hidden="true"></i>
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
