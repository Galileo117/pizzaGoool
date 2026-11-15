{{-- Catálogo público: el componente Livewire maneja filtros, grid, carrito y scripts. --}}
@extends('layouts.cliente')

@section('title', 'Catálogo - Pizza Goool')

@section('content')
    <livewire:carrito-compras contexto="catalogo" :carrito="$carrito" :total="$total" :cantidad-items="$cantidadItems" />
@endsection
