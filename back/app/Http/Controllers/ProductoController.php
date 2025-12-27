<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    function all(Request $request)
    {
        $q = Producto::query();

        if ($request->tipo) {
            $q->where('tipo', $request->tipo);
        }

        return $q->orderBy('nombre', 'asc')->get();
    }
    public function index(Request $request)
    {
        $q = Producto::query();

        if ($request->filter) {
            $q->where('nombre', 'like', "%{$request->filter}%")
                ->orWhere('codigo', 'like', "%{$request->filter}%")
                ->orWhere('tipo', 'like', "%{$request->filter}%");
        }

        if ($request->tipo) {
            $q->where('tipo', $request->tipo);
        }

        return $q->orderBy('id', 'desc')
            ->paginate($request->limit ?? 20);
    }

    public function store(Request $request)
    {
        return Producto::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());
        return $producto;
    }

    public function destroy($id)
    {
        Producto::findOrFail($id)->delete();
        return response()->noContent();
    }

    // SUBIR / CAMBIAR IMAGEN (simple)
    public function imagen(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = "producto_{$id}_" . time() . "." . $file->getClientOriginalExtension();

            $file->move(public_path('uploads'), $name);

            $producto->imagen = $name;
            $producto->save();
        }

        return $producto;
    }
}
