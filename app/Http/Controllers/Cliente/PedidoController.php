<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function procesar(Request $request)
    {
        $carrito = session()->get('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('cliente.catalogo')->with('error', 'Tu carrito está vacío.');
        }

        // Validar datos
        $request->validate([
            'tipo_entrega' => 'required|in:recoger_local,contraentrega',
            'metodo_pago' => 'required|in:efectivo,qr',
            'direccion' => 'required_if:tipo_entrega,contraentrega',
        ]);

        // Calcular total
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // Crear pedido
        $pedido = Pedido::create([
            'usuario_id' => Auth::id(),
            'total' => $total,
            'estado' => 'pendiente',
            'metodo_pago' => $request->metodo_pago,
            'tipo_entrega' => $request->tipo_entrega,
            'direccion' => $request->direccion ?? null,
            'referencia' => $request->referencia ?? null,
        ]);

        // Registrar detalles del pedido
        foreach ($carrito as $item) {
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        // Limpiar carrito
        session()->forget('carrito');

        return redirect()->route('cliente.seguimiento', $pedido->id)->with('success', '¡Pedido creado correctamente!');
    }
}