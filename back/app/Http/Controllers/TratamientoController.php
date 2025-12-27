<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\TratamientoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TratamientoController extends Controller
{
    public function indexByHistorial($historialId)
    {
        $items = Tratamiento::with(['productos.producto'])
            ->where('historial_clinico_id', $historialId)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return $items;
    }

    public function store(Request $r)
    {
        $r->validate([
            'historial_id' => 'required|integer',
            'fecha' => 'nullable|date',
            'comentario' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'pagado' => 'nullable|boolean',
            'productos' => 'array',
            'productos.*.producto_id' => 'required|integer',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($r) {
            $t = Tratamiento::create([
                'historial_clinico_id' => $r->historial_id,
                'user_id' => auth()->id(),
                'fecha' => $r->fecha,
                'comentario' => $r->comentario,
                'observaciones' => $r->observaciones,
                'pagado' => (bool) $r->pagado,
            ]);

            $total = 0;

            foreach (($r->productos ?? []) as $p) {
                $tp = TratamientoProducto::create([
                    'tratamiento_id' => $t->id,
                    'producto_id' => $p['producto_id'],
                    'cantidad' => $p['cantidad'],
                    'precio' => $p['precio'],
                ]);
                $total += ((float)$tp->cantidad * (float)$tp->precio);
            }

            // si quieres guardar el total en tratamientos.costo
            $t->costo = $total;
            $t->save();

            return $t->load(['productos.producto']);
        });
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'historial_id' => 'required|integer',
            'fecha' => 'nullable|date',
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
                'historial_clinico_id' => $r->historial_id,
                'fecha' => $r->fecha,
                'comentario' => $r->comentario,
                'observaciones' => $r->observaciones,
                'pagado' => (bool) $r->pagado,
            ]);

            // reemplazar productos (simple y seguro)
            $t->productos()->delete();

            $total = 0;

            foreach (($r->productos ?? []) as $p) {
                $tp = TratamientoProducto::create([
                    'tratamiento_id' => $t->id,
                    'producto_id' => $p['producto_id'],
                    'cantidad' => $p['cantidad'],
                    'precio' => $p['precio'],
                ]);
                $total += ((float)$tp->cantidad * (float)$tp->precio);
            }

            $t->costo = $total;
            $t->save();

            return $t->load(['productos.producto']);
        });
    }

    public function destroy($id)
    {
        $t = Tratamiento::findOrFail($id);
        $t->delete();
        return response()->noContent();
    }

    // ==========================
    // PDF
    // ==========================
    public function pdf($id)
    {
//        return $id;
        $tratamiento = Tratamiento::with([
            'historial.mascota.veterinaria',
            'historial.user',
            'productos.producto',
            'user'
        ])->findOrFail($id);
//        return $tratamiento;

        $pdf = Pdf::loadView('pdf.tratamiento', compact('tratamiento'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("tratamiento-{$tratamiento->id}.pdf");
    }
}
