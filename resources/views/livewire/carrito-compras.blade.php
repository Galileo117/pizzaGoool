<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Listado de Productos (AHORA DENTRO DEL COMPONENTE) -->
        <div class="md:col-span-2">
            <h1 class="text-3xl font-bold text-goool-green mb-6">Nuestro Menú</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($productos as $producto)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="w-full h-40 bg-goool-green-light rounded-md mb-2 flex items-center justify-center">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover rounded-md">
                            @else
                                <i class="fa-solid fa-pizza-slice text-6xl text-white"></i>
                            @endif
                        </div>
                        <h3 class="font-bold text-lg">{{ $producto->nombre }}</h3>
                        <p class="text-gray-600 text-sm">Bs. {{ $producto->precio }}</p>
                        
                        <!-- ESTE BOTÓN AHORA ESTÁ DENTRO DEL COMPONENTE -->
                        <button 
                            wire:click="agregarAlCarrito({{ $producto->id }})"
                            class="mt-3 w-full bg-goool-green text-white py-2 rounded-lg font-bold hover:bg-goool-green-dark">
                            <i class="fa-solid fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Carrito de Compras -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-goool-green mb-4"><i class="fa-solid fa-cart-shopping"></i> Tu Carrito</h2>
                
                <div class="space-y-4">
                    @if(count($carrito) > 0)
                        @foreach($carrito as $productoId => $item)
                            <div class="flex items-center justify-between border-b pb-4">
                                <div>
                                    <h4 class="font-bold">{{ $item['nombre'] }}</h4>
                                    <p class="text-sm text-gray-600">Bs. {{ $item['precio'] }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] - 1 }})" class="bg-gray-200 px-2 py-1 rounded">-</button>
                                    <span class="font-bold">{{ $item['cantidad'] }}</span>
                                    <button wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] + 1 }})" class="bg-gray-200 px-2 py-1 rounded">+</button>
                                    <button wire:click="quitarDelCarrito({{ $productoId }})" class="text-goool-red ml-2 hover:text-red-700">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Tu carrito está vacío.</p>
                    @endif
                </div>
                
                <div class="mt-6 text-right">
                    @if(count($carrito) > 0)
                        <h3 class="text-xl font-bold text-goool-green">Total: Bs. {{ $total }}</h3>
                        <a href="{{ route('cliente.checkout') }}" class="inline-block mt-4 bg-goool-green text-white px-6 py-3 rounded-lg font-bold hover:bg-goool-green-dark">
                            <i class="fa-solid fa-credit-card"></i> Proceder al Pago
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>