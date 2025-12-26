<?php

namespace App\Http\Controllers;

use App\Models\TratamientoProducto;
use Illuminate\Http\Request;

class TratamientoProductoController extends Controller
{
    public function store(Request $r)
    {
        $r->validate([
            'tratamiento_id' => 'required|integer',
            'producto_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.01',
            'precio' => 'required|numeric|min:0',
        ]);

        return TratamientoProducto::create($r->all())->load('producto');
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'producto_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.01',
            'precio' => 'required|numeric|min:0',
        ]);

        $tp = TratamientoProducto::findOrFail($id);
        $tp->update($r->all());
        return $tp->load('producto');
    }

    public function destroy($id)
    {
        TratamientoProducto::findOrFail($id)->delete();
        return response()->noContent();
    }
}
