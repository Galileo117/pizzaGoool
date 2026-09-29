@props([
    'posicion' => 'arriba-derecha',
    'color' => 'text-goool-yellow',
    'opacidad' => 'opacity-20',
])

@php
    $posiciones = [
        'arriba-derecha' => 'top-0 right-0',
        'arriba-izquierda' => 'top-0 left-0',
        'abajo-derecha' => 'bottom-0 right-0',
        'abajo-izquierda' => 'bottom-0 left-0',
    ];
    $posicionClase = $posiciones[$posicion] ?? $posiciones['arriba-derecha'];
@endphp

<div class="absolute {{ $posicionClase }} {{ $opacidad }} pointer-events-none select-none {{ $color }}" aria-hidden="true">
    <svg width="200" height="200" viewBox="0 0 200 200" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <circle cx="10" cy="10" r="2" />
        <circle cx="30" cy="10" r="2" />
        <circle cx="50" cy="10" r="2" />
        <circle cx="70" cy="10" r="2" />
        <circle cx="90" cy="10" r="2" />
        <circle cx="110" cy="10" r="2" />
        <circle cx="130" cy="10" r="2" />
        <circle cx="150" cy="10" r="2" />
        <circle cx="170" cy="10" r="2" />
        <circle cx="190" cy="10" r="2" />

        <circle cx="10" cy="30" r="2" />
        <circle cx="30" cy="30" r="2" />
        <circle cx="50" cy="30" r="2" />
        <circle cx="70" cy="30" r="2" />
        <circle cx="90" cy="30" r="2" />
        <circle cx="110" cy="30" r="2" />
        <circle cx="130" cy="30" r="2" />
        <circle cx="150" cy="30" r="2" />
        <circle cx="170" cy="30" r="2" />
        <circle cx="190" cy="30" r="2" />

        <circle cx="10" cy="50" r="2" />
        <circle cx="30" cy="50" r="2" />
        <circle cx="50" cy="50" r="2" />
        <circle cx="70" cy="50" r="2" />
        <circle cx="90" cy="50" r="2" />
        <circle cx="110" cy="50" r="2" />
        <circle cx="130" cy="50" r="2" />
        <circle cx="150" cy="50" r="2" />
        <circle cx="170" cy="50" r="2" />
        <circle cx="190" cy="50" r="2" />

        <circle cx="10" cy="70" r="2" />
        <circle cx="30" cy="70" r="2" />
        <circle cx="50" cy="70" r="2" />
        <circle cx="70" cy="70" r="2" />
        <circle cx="90" cy="70" r="2" />
        <circle cx="110" cy="70" r="2" />
        <circle cx="130" cy="70" r="2" />
        <circle cx="150" cy="70" r="2" />
        <circle cx="170" cy="70" r="2" />
        <circle cx="190" cy="70" r="2" />

        <circle cx="10" cy="90" r="2" />
        <circle cx="30" cy="90" r="2" />
        <circle cx="50" cy="90" r="2" />
        <circle cx="70" cy="90" r="2" />
        <circle cx="90" cy="90" r="2" />
        <circle cx="110" cy="90" r="2" />
        <circle cx="130" cy="90" r="2" />
        <circle cx="150" cy="90" r="2" />
        <circle cx="170" cy="90" r="2" />
        <circle cx="190" cy="90" r="2" />

        <circle cx="10" cy="110" r="2" />
        <circle cx="30" cy="110" r="2" />
        <circle cx="50" cy="110" r="2" />
        <circle cx="70" cy="110" r="2" />
        <circle cx="90" cy="110" r="2" />
        <circle cx="110" cy="110" r="2" />
        <circle cx="130" cy="110" r="2" />
        <circle cx="150" cy="110" r="2" />
        <circle cx="170" cy="110" r="2" />
        <circle cx="190" cy="110" r="2" />
    </svg>
</div>