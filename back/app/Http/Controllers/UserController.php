<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    private function authVetId(Request $request): ?int
    {
        return $request->user()?->veterinaria_id;
    }

    private function ensureSameVet(Request $request, User $user): void
    {
        $vetId = $this->authVetId($request);
        if (!$vetId || (int)$user->veterinaria_id !== (int)$vetId) {
            abort(403, 'No autorizado (otra veterinaria).');
        }
    }

    public function index(Request $request)
    {
        $vetId = $this->authVetId($request);
        if (!$vetId) {
            abort(422, 'Tu usuario no tiene veterinaria asignada.');
        }

        return User::where('veterinaria_id', $vetId)
            ->orderByDesc('id')
            ->get(['id','name','username','email','role','avatar','veterinaria_id','created_at']);
    }

    public function store(Request $request)
    {
        $auth = $request->user();
        if (!$auth?->veterinaria_id) {
            abort(422, 'Tu usuario no tiene veterinaria asignada.');
        }

        $data = $request->validate([
            'name'     => ['required','string','max:150'],
            'username' => ['required','string','max:80', Rule::unique('users','username')],
            'email'    => ['nullable','email','max:150', Rule::unique('users','email')],
            'password' => ['required','string','min:6'],
            'role'     => ['required', Rule::in(['Administrador','Vendedor','Veterinario'])],
        ]);

        $user = User::create([
            'name'          => $data['name'],
            'username'      => $data['username'],
            'email'         => $data['email'] ?? null,
            'password'      => Hash::make($data['password']),
            'role'          => $data['role'],
            'avatar'        => 'defaultAvatar.png',
            'veterinaria_id'=> $auth->veterinaria_id, // ✅ SIEMPRE su misma veterinaria
        ]);

        return $user;
    }

    public function update(Request $request, User $user)
    {
        $this->ensureSameVet($request, $user);

        $data = $request->validate([
            'name'     => ['nullable','string','max:150'],
            'username' => ['nullable','string','max:80', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['nullable','email','max:150', Rule::unique('users','email')->ignore($user->id)],
            'role'     => ['nullable', Rule::in(['Administrador','Vendedor','Veterinario'])],
        ]);

        // ✅ Evitar que cambien veterinaria_id por request
        unset($data['veterinaria_id']);

        $user->update($data);

        return $user->fresh(['id','name','username','email','role','avatar','veterinaria_id']);
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->ensureSameVet($request, $user);

        $data = $request->validate([
            'password' => ['required','string','min:6'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' => 'Contraseña actualizada']);
    }

    public function updateAvatar(Request $request, User $user)
    {
        $this->ensureSameVet($request, $user);

        if (!$request->hasFile('avatar')) {
            return response()->json(['message' => 'No se ha enviado un archivo'], 400);
        }

        $file = $request->file('avatar');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            return response()->json(['message' => 'Formato no permitido'], 422);
        }

        $filename = time() . '_' . $user->id . '.jpg';
        $path = public_path('images/' . $filename);

        $manager = new ImageManager(new Driver());
        $manager->read($file->getPathname())
            ->cover(320, 320)   // recorta bonito al centro
            ->toJpeg(75)
            ->save($path);

        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'message' => 'Avatar actualizado',
            'avatar'  => $filename
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureSameVet($request, $user);

        // opcional: evitar que se elimine a sí mismo
        if ((int)$request->user()->id === (int)$user->id) {
            abort(422, 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
