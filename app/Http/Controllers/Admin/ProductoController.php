<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Administra el catálogo y protege referencias de productos en pedidos históricos.
 */
class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::all();

        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:1000',
            'precio'       => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'stock'        => 'required|integer|min:0|max:9999',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'precio',
            'stock',
        ]);

        $data['descripcion'] = $request->descripcion ?? '';
        $data['disponible']  = $request->has('disponible');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
        }

        Producto::create($data);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();

        return view(
            'admin.productos.edit',
            compact('producto', 'categorias')
        );
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:1000',
            'precio'       => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'stock'        => 'required|integer|min:0|max:9999',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $producto->categoria_id = $validated['categoria_id'];
        $producto->nombre       = $validated['nombre'];
        $producto->descripcion  = $validated['descripcion'] ?? '';
        $producto->precio       = $validated['precio'];
        $producto->stock        = $validated['stock'];
        $producto->disponible   = $request->boolean('disponible');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($producto->imagen);
            }

            $producto->imagen = $request->file('imagen')
                ->store('productos', 'public');
        }

        $producto->save();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->detallePedidos()->exists()) {
            return redirect()
                ->route('admin.productos.index')
                ->with('error', 'No se puede eliminar un producto incluido en pedidos. Puedes marcarlo como no disponible.');
        }

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
