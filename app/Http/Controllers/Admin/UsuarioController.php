<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::orderBy('created_at', 'desc');

        // Filtro por rol (opcional)
        if ($request->has('rol') && $request->rol !== '') {
            $query->where('rol', $request->rol);
        }

        $usuarios = $query->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }
}