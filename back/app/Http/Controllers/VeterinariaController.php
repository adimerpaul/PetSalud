<?php

namespace App\Http\Controllers;

use App\Models\Veterinaria;
use Illuminate\Http\Request;

class VeterinariaController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        $user = $request->user();
        if (!$user || ($user->role !== 'Administrador' && $user->role !== 'Admin')) {
            abort(403, 'Solo el Administrador puede acceder a esta opción.');
        }
    }

    // GET /veterinaria -> devuelve SOLO la veterinaria del usuario logueado
    public function showMine(Request $request)
    {
        $this->ensureAdmin($request);

        $user = $request->user();
        if (!$user->veterinaria_id) {
            return response()->json([
                'message' => 'El usuario no tiene veterinaria asignada.'
            ], 404);
        }

        $vet = Veterinaria::find($user->veterinaria_id);
        if (!$vet) {
            return response()->json([
                'message' => 'Veterinaria no encontrada.'
            ], 404);
        }

        return $vet;
    }

    // PUT /veterinaria -> actualiza SOLO la veterinaria del usuario logueado
    public function updateMine(Request $request)
    {
        $this->ensureAdmin($request);

        $user = $request->user();
        if (!$user->veterinaria_id) {
            return response()->json([
                'message' => 'El usuario no tiene veterinaria asignada.'
            ], 404);
        }

        $vet = Veterinaria::find($user->veterinaria_id);
        if (!$vet) {
            return response()->json([
                'message' => 'Veterinaria no encontrada.'
            ], 404);
        }

        // Ajusta estos campos a TU modelo real (nombres de columnas)
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:150',
            'descripcion' => 'nullable|string|max:800',
            'color' => 'nullable|string|max:30',
            'nit' => 'nullable|string|max:60',
            'ciudad' => 'nullable|string|max:80',
            'propietario' => 'nullable|string|max:120',
            'logo' => 'nullable|string|max:255', // si manejas logo como filename
        ]);

        $vet->update($data);

        return $vet;
    }
}
