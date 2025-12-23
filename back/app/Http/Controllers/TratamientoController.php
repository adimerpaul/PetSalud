<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    public function store(Request $r)
    {
        $tratamiento = Tratamiento::create([
            'historial_clinico_id' => $r->historial_id,
            'user_id' => auth()->id(),
            'fecha' => $r->fecha,
            'observaciones' => $r->observaciones,
            'comentario' => $r->comentario,
            'costo' => $r->costo ?? 0,
        ]);

        return $tratamiento;
    }

    public function update(Request $r,$id)
    {
        $t = Tratamiento::findOrFail($id);
        $t->update($r->all());
        return $t;
    }

    public function destroy($id)
    {
        Tratamiento::findOrFail($id)->delete();
        return response()->noContent();
    }
}
