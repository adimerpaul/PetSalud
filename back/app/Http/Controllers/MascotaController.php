<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
class MascotaController extends Controller
{
    public function pdf($id)
    {
        $mascota = Mascota::with('veterinaria')->findOrFail($id);

        // (opcional) seguridad multi-veterinaria:
        // if (auth()->check() && auth()->user()->veterinaria_id && $mascota->veterinaria_id != auth()->user()->veterinaria_id) {
        //     abort(403, 'No autorizado');
        // }

        return Pdf::loadView('pdf.mascota', compact('mascota'))
            ->setPaper('A4')
            ->stream('mascota.pdf');
    }
    public function index(Request $request)
    {
        $filter = $request->get('filter', '');
        $limit  = (int)($request->get('limit', 20));

        $q = Mascota::query();

        // si es multi-veterinaria:
        if ($request->user()?->veterinaria_id) {
            $q->where('veterinaria_id', $request->user()->veterinaria_id);
        }

        if ($filter) {
            $q->where(function ($qq) use ($filter) {
                $qq->where('nombre', 'like', "%$filter%")
                    ->orWhere('apellido', 'like', "%$filter%")
                    ->orWhere('especie', 'like', "%$filter%")
                    ->orWhere('raza', 'like', "%$filter%")
                    ->orWhere('propietario_nombre', 'like', "%$filter%")
                    ->orWhere('propietario_ci', 'like', "%$filter%");
            });
        }

        return $q->orderByDesc('id')->paginate($limit);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // si es multi-veterinaria:
        if ($request->user()?->veterinaria_id) {
            $data['veterinaria_id'] = $request->user()->veterinaria_id;
        }

        // foto (multipart)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = strtolower($file->getClientOriginalExtension());
            $name = Str::uuid()->toString() . '.' . ($ext ?: 'jpg');
            $file->move(public_path('uploads'), $name);
            $data['photo'] = $name;
        }

        $mascota = Mascota::create($data);
        return $mascota;
    }

    public function show(Request $request, Mascota $mascota)
    {
        // multi-veterinaria (opcional)
        if ($request->user()?->veterinaria_id && (int)$mascota->veterinaria_id !== (int)$request->user()->veterinaria_id) {
            abort(403);
        }
        return $mascota;
    }

    public function update(Request $request, Mascota $mascota)
    {
        if ($request->user()?->veterinaria_id && (int)$mascota->veterinaria_id !== (int)$request->user()->veterinaria_id) {
            abort(403);
        }

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = strtolower($file->getClientOriginalExtension());
            $name = Str::uuid()->toString() . '.' . ($ext ?: 'jpg');
            $file->move(public_path('uploads'), $name);
            $data['photo'] = $name;
        }

        // no permitir cambiar veterinaria
        unset($data['veterinaria_id']);

        $mascota->update($data);
        return $mascota->fresh();
    }

    public function destroy(Request $request, Mascota $mascota)
    {
        if ($request->user()?->veterinaria_id && (int)$mascota->veterinaria_id !== (int)$request->user()->veterinaria_id) {
            abort(403);
        }

        $mascota->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
