<?php

namespace App\Http\Controllers;

use App\Models\TratamientoProducto;
use Illuminate\Http\Request;

class TratamientoProductoController extends Controller
{
    public function store(Request $r)
    {
        return TratamientoProducto::create($r->all());
    }

    public function update(Request $r,$id)
    {
        $tp = TratamientoProducto::findOrFail($id);
        $tp->update($r->all());
        return $tp;
    }

    public function destroy($id)
    {
        TratamientoProducto::findOrFail($id)->delete();
        return response()->noContent();
    }
}
