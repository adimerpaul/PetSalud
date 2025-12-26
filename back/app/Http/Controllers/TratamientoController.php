<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TratamientoController extends Controller
{
    public function indexByHistorial($historialId)
    {
        return Tratamiento::with(['productos.producto'])
            ->where('historial_clinico_id', $historialId)
            ->orderByDesc('fecha')
            ->get();
    }

    public function store(Request $r)
    {
        $r->validate([
            'historial_id' => 'required|integer',
            'fecha' => 'required|date',
            'comentario' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'pagado' => 'nullable|boolean',
            'productos' => 'array',
            'productos.*.producto_id' => 'required|integer',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($r) {

            $tratamiento = Tratamiento::create([
                'historial_clinico_id' => $r->historial_id,
                'user_id' => auth()->id(),
                'fecha' => $r->fecha,
                'observaciones' => $r->observaciones,
                'comentario' => $r->comentario,
                'pagado' => (bool)($r->pagado ?? false),
                'costo' => 0,
            ]);

            $total = 0;
            foreach (($r->productos ?? []) as $p) {
                $cantidad = (float)$p['cantidad'];
                $precio = (float)$p['precio'];
                $total += $cantidad * $precio;

                $tratamiento->productos()->create([
                    'producto_id' => $p['producto_id'],
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                ]);
            }

            $tratamiento->update(['costo' => $total]);

            return $tratamiento->load(['productos.producto']);
        });
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'fecha' => 'required|date',
            'comentario' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'pagado' => 'nullable|boolean',
            'productos' => 'array',
            'productos.*.producto_id' => 'required|integer',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($r, $id) {

            $t = Tratamiento::with('productos')->findOrFail($id);

            $t->update([
                'fecha' => $r->fecha,
                'observaciones' => $r->observaciones,
                'comentario' => $r->comentario,
                'pagado' => (bool)($r->pagado ?? false),
            ]);

            // Reemplazar productos
            $t->productos()->delete();

            $total = 0;
            foreach (($r->productos ?? []) as $p) {
                $cantidad = (float)$p['cantidad'];
                $precio = (float)$p['precio'];
                $total += $cantidad * $precio;

                $t->productos()->create([
                    'producto_id' => $p['producto_id'],
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                ]);
            }

            $t->update(['costo' => $total]);

            return $t->load(['productos.producto']);
        });
    }

    public function destroy($id)
    {
        Tratamiento::findOrFail($id)->delete();
        return response()->noContent();
    }
}
