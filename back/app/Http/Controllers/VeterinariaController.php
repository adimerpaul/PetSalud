<?php

namespace App\Http\Controllers;

use App\Models\Veterinaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VeterinariaController extends Controller
{
    private function ensureAdmin(Request $request)
    {
        $role = strtolower($request->user()->role ?? '');
        if (!in_array($role, ['admin', 'administrador'])) {
            abort(403, 'Solo el administrador puede acceder.');
        }
    }

    private function getVeterinaria(Request $request): Veterinaria
    {
        $user = $request->user();

        if (!$user->veterinaria_id) {
            abort(404, 'Usuario sin veterinaria asignada.');
        }

        $vet = Veterinaria::find($user->veterinaria_id);
        if (!$vet) {
            abort(404, 'Veterinaria no encontrada.');
        }

        return $vet;
    }

    // GET /veterinaria
    public function showMine(Request $request)
    {
        $this->ensureAdmin($request);
        return $this->getVeterinaria($request);
    }

    // PUT /veterinaria
    public function updateMine(Request $request)
    {
        $this->ensureAdmin($request);
        $vet = $this->getVeterinaria($request);

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:150',
            'descripcion' => 'nullable|string|max:800',
            'color' => 'nullable|string|max:30',
        ]);

        $vet->update($data);
        return $vet;
    }

    // POST /veterinaria/logo
    public function uploadLogo(Request $request)
    {
        $this->ensureAdmin($request);
        $vet = $this->getVeterinaria($request);

        $request->validate([
            'logo' => 'required|image|max:2048'
        ]);

        if ($vet->logo) {
            Storage::disk('public')->delete('veterinarias/' . $vet->logo);
        }

        $filename = uniqid('logo_') . '.' . $request->logo->extension();
        $request->logo->storeAs('veterinarias', $filename, 'public');

        $vet->update(['logo' => $filename]);
        return $vet;
    }

    // POST /veterinaria/imagen
    public function uploadImagen(Request $request)
    {
        $this->ensureAdmin($request);
        $vet = $this->getVeterinaria($request);

        $request->validate([
            'imagen' => 'required|image|max:4096'
        ]);

        if ($vet->imagen) {
            Storage::disk('public')->delete('veterinarias/' . $vet->imagen);
        }

        $filename = uniqid('img_') . '.' . $request->imagen->extension();
        $request->imagen->storeAs('veterinarias', $filename, 'public');

        $vet->update(['imagen' => $filename]);
        return $vet;
    }
}
