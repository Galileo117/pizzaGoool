@extends('layouts.app')

@section('title', 'Catálogo - Pizza Goool')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Encabezado con carrito flotante -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-goool-green">Nuestro Menú</h1>
            <p class="text-gray-500 mt-1">Pide tu pizza favorita</p>
        </div>
        
        <!-- Carrito en la parte superior -->
        <button onclick="abrirCarrito()" class="relative bg-goool-green text-white p-3 rounded-full shadow-lg hover:bg-goool-green-dark">
            <i class="fa-solid fa-cart-shopping text-xl"></i>
            <span id="contador-carrito" class="absolute -top-1 -right-1 bg-goool-yellow text-goool-green-dark text-xs font-bold px-2 py-0.5 rounded-full">0</span>
        </button>
    </div>
    
    <!-- Grid de productos -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($productos as $producto)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="h-32 bg-goool-green-light rounded-xl mb-3 flex items-center justify-center">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover rounded-xl">
                    @else
                        <i class="fa-solid fa-pizza-slice text-5xl text-white"></i>
                    @endif
                </div>
                
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-900">{{ $producto->nombre }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $producto->descripcion }}</p>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-xl font-bold text-goool-green">Bs. {{ $producto->precio }}</span>
                        
                        <button 
                            onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio }})"
                            class="bg-goool-yellow text-goool-green-dark px-3 py-2 rounded-full hover:bg-goool-yellow-dark font-bold text-xs">
                            <i class="fa-solid fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Carrito tipo Drawer -->
    <div id="carrito-overlay" class="fixed inset-0 bg-black/50 hidden z-40" onclick="cerrarCarrito()"></div>
    
    <div id="carrito-drawer" class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300">
        <div class="p-4 h-full flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-goool-green"><i class="fa-solid fa-cart-shopping"></i> Carrito</h2>
                <button onclick="cerrarCarrito()" class="text-gray-500"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <div class="flex-1 overflow-y-auto" id="contenido-carrito">
                <!-- Dinámico -->
            </div>
            
            <div class="border-t border-gray-200 pt-4 mt-4 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-semibold">Total:</span>
                    <span id="total-carrito" class="text-2xl font-bold text-goool-green">Bs. 0</span>
                </div>
                
                <a href="#" id="boton-pagar" onclick="event.preventDefault(); irAlCheckout()" class="w-full bg-goool-green text-white py-3 rounded-xl font-bold text-center hover:bg-goool-green-dark">
                    <i class="fa-solid fa-credit-card"></i> Proceder al Pago
                </a>
            </div>
        </div>
    </div>
    
</div>

<!-- JavaScript que maneja el carrito sin recarga -->
<script>
    let carrito = [];
    let csrfToken = '{{ csrf_token() }}';
    
    // Agregar al carrito
    function agregarAlCarrito(id, nombre, precio) {
        const productoExistente = carrito.find(item => item.id === id);
        if (productoExistente) {
            productoExistente.cantidad++;
        } else {
            carrito.push({ id, nombre, precio, cantidad: 1 });
        }
        
        actualizarContador();
        actualizarCarrito();
        abrirCarrito();
    }
    
    // Actualizar contador
    function actualizarContador() {
        const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
        document.getElementById('contador-carrito').textContent = totalItems;
    }
    
    // Actualizar contenido del carrito
    function actualizarCarrito() {
        const contenido = document.getElementById('contenido-carrito');
        const totalElement = document.getElementById('total-carrito');
        const botonPagar = document.getElementById('boton-pagar');
        
        let total = 0;
        let html = '';
        
        if (carrito.length === 0) {
            html = '<p class="text-gray-500 text-center mt-10">Tu carrito está vacío.</p>';
            botonPagar.style.display = 'none';
        } else {
            botonPagar.style.display = 'block';
            for (const item of carrito) {
                total += item.precio * item.cantidad;
                html += `
                    <div class="flex items-center justify-between border-b border-gray-100 py-4">
                        <div>
                            <h4 class="font-bold text-sm">${item.nombre}</h4>
                            <p class="text-xs text-gray-500">Bs. ${item.precio}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})" class="bg-gray-100 p-2 rounded-full hover:bg-gray-200"><i class="fa-solid fa-minus text-xs"></i></button>
                            <span class="font-bold">${item.cantidad}</span>
                            <button onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})" class="bg-goool-yellow p-2 rounded-full hover:bg-goool-yellow-dark"><i class="fa-solid fa-plus text-xs"></i></button>
                        </div>
                    </div>
                `;
            }
        }
        
        contenido.innerHTML = html;
        totalElement.textContent = `Bs. ${total}`;
    }
    
    // Actualizar cantidad
    function actualizarCantidad(id, cantidad) {
        if (cantidad <= 0) {
            carrito = carrito.filter(item => item.id !== id);
        } else {
            const item = carrito.find(item => item.id === id);
            if (item) item.cantidad = cantidad;
        }
        actualizarContador();
        actualizarCarrito();
    }
    
    // Guardar el carrito en la sesión y ir al Checkout
    function irAlCheckout() {
        // Enviar datos al servidor
        fetch('{{ route('cliente.guardar_carrito') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ carrito: carrito })
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route('cliente.checkout') }}';
            }
        });
    }
    
    // Apertura y cierre
    function abrirCarrito() {
        document.getElementById('carrito-overlay').classList.remove('hidden');
        document.getElementById('carrito-drawer').classList.remove('translate-x-full');
    }
    
    function cerrarCarrito() {
        document.getElementById('carrito-overlay').classList.add('hidden');
        document.getElementById('carrito-drawer').classList.add('translate-x-full');
    }
</script>
@endsection