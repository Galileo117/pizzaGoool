{{-- Campo de texto compartido que combina atributos recibidos y estado deshabilitado. --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-goool-green focus:ring-goool-green rounded-md shadow-sm']) }}>