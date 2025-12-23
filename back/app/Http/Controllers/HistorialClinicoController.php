<?php
namespace App\Http\Controllers;

use App\Models\HistorialClinico;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistorialClinicoController extends Controller
{
    public function index($mascotaId)
    {
        return HistorialClinico::with('tratamientos')
            ->where('mascota_id',$mascotaId)
            ->orderByDesc('fecha')
            ->get();
    }

    public function store(Request $r)
    {
        $historial = HistorialClinico::create(
            array_merge(
                $r->all(),
                [
                    'user_id'=>auth()->id(),
                    'veterinaria_id'=>auth()->user()->veterinaria_id
                ]
            )
        );

        foreach ($r->tratamientos ?? [] as $t) {
            $historial->tratamientos()->create($t);
        }

        return $historial->load('tratamientos');
    }

    public function update(Request $r,$id)
    {
        $historial = HistorialClinico::findOrFail($id);
        $historial->update($r->all());

        $historial->tratamientos()->delete();
        foreach ($r->tratamientos ?? [] as $t) {
            $historial->tratamientos()->create($t);
        }

        return $historial->load('tratamientos');
    }

    public function destroy($id)
    {
        HistorialClinico::findOrFail($id)->delete();
        return response()->noContent();
    }

    public function pdf($id)
    {
        $historial = HistorialClinico::with([
            'mascota','tratamientos','user','mascota.veterinaria'
        ])->findOrFail($id);

        return Pdf::loadView('pdf.historial',compact('historial'))
            ->setPaper('A4')
            ->stream('historial.pdf');
    }
}

