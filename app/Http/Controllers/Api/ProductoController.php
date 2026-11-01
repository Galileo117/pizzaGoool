<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Provee el catálogo disponible y el detalle de productos para la app móvil.
 */
class ProductoController extends Controller
{
    use ApiResponse;

    /**
     * Lista los productos disponibles.
     */
    public function index(Request $request)
    {
        $query = Producto::where('disponible', true)->with('categoria');

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'LIKE', '%'.$request->buscar.'%');
        }

        $productos = $query->orderBy('created_at', 'desc')->get();

        return $this->successResponse(
            ProductoResource::collection($productos),
            'Lista de productos.'
        );
    }

    /**
     * Detalle de un producto.
     */
    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);

        if (! $producto) {
            return $this->notFoundResponse('Producto no encontrado.');
        }

        return $this->successResponse(
            new ProductoResource($producto),
            'Detalle del producto.'
        );
    }
}
