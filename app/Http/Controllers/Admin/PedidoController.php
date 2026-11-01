<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

/**
 * Provee la consulta de pedidos del panel administrativo.
 */
class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with('usuario')->orderBy('created_at', 'desc');

        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }
}
