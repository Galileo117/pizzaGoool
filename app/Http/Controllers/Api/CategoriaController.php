<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaResource;
use App\Http\Traits\ApiResponse;
use App\Models\Categoria;

/**
 * Publica categorías en el formato JSON consumido por el catálogo móvil.
 */
class CategoriaController extends Controller
{
    use ApiResponse;

    /**
     * Lista todas las categorías.
     */
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return $this->successResponse(
            CategoriaResource::collection($categorias),
            'Lista de categorías.'
        );
    }
}
