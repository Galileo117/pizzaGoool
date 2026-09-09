@extends('layouts.app')

@section('title', 'Checkout - Pizza Goool')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-goool-green mb-6"><i class="fa-solid fa-credit-card"></i> Proceder al Pago</h1>
    
    <form action="{{ route('cliente.procesar_pedido') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Todo el carrito (se usa la sesión guardada) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-goool-green-dark mb-4"><i class="fa-solid fa-receipt"></i> Resumen de tu Pedido</h2>
            
            <div class="space-y-4">
                @foreach(session('carrito') as $item)
                    <div class="flex justify-between border-b border-gray-100 pb-4">
                        <span class="font-semibold">{{ $item['nombre'] }} x {{ $item['cantidad'] }}</span>
                        <span class="font-bold">Bs. {{ $item['precio'] * $item['cantidad'] }}</span>
                    </div>
                @endforeach
                
                @php
                    $total = 0;
                    foreach(session('carrito') as $item) {
                        $total += $item['precio'] * $item['cantidad'];
                    }
                @endphp
                
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xl font-bold">Total:</span>
                    <span class="text-2xl font-bold text-goool-green">Bs. {{ $total }}</span>
                </div>
            </div>
        </div>
        
        <!-- Elección de entrega -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-goool-green-dark mb-4"><i class="fa-solid fa-truck-fast"></i> Tipo de Entrega</h2>
            <div class="space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="recoger_local" class="form-radio text-goool-green" checked>
                    <span class="font-semibold">Recoger en Local</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="tipo_entrega" value="contraentrega" class="form-radio text-goool-green">
                    <span class="font-semibold">Contraentrega (Delivery)</span>
                </label>
            </div>
        </div>
        
        <!-- Elección de pago -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-goool-green-dark mb-4"><i class="fa-solid fa-money-bill-wave"></i> Método de Pago</h2>
            <div class="space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="metodo_pago" value="efectivo" class="form-radio text-goool-green" checked>
                    <span class="font-semibold">Pagar en Efectivo</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="metodo_pago" value="qr" class="form-radio text-goool-green">
                    <span class="font-semibold">Pagar con Código QR</span>
                </label>
            </div>
        </div>
        
        <!-- Dirección (solo si es contraentrega) -->
        <div class="bg-white rounded-lg shadow p-6" id="direccion_contraentrega" style="display: none;">
            <h2 class="text-xl font-bold text-goool-green-dark mb-4"><i class="fa-solid fa-location-dot"></i> Dirección de Entrega</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Dirección Completa</label>
                    <input type="text" name="direccion" class="w-full border rounded-lg p-2" placeholder="Av. Siempre Viva 123">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Referencia</label>
                    <input type="text" name="referencia" class="w-full border rounded-lg p-2" placeholder="Piso 2, portón verde">
                </div>
            </div>
        </div>
        
        <!-- Confirmar -->
        <div class="bg-white rounded-lg shadow p-6 text-right">
            <button type="submit" class="mt-4 bg-goool-green text-white px-8 py-3 rounded-lg font-bold hover:bg-goool-green-dark">
                <i class="fa-solid fa-check-circle"></i> Confirmar Pedido
            </button>
        </div>
    </form>
</div>

<script>
    const radios = document.querySelectorAll('input[name="tipo_entrega"]');
    const direccionDiv = document.getElementById('direccion_contraentrega');
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'contraentrega') {
                direccionDiv.style.display = 'block';
            } else {
                direccionDiv.style.display = 'none';
            }
        });
    });
</script>
@endsection