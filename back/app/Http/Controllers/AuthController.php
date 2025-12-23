<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Veterinaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'veterinaria_nombre' => 'required|string|max:255',
            'name' => 'required|string|max:255',
//            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $existingVet = Veterinaria::where('nombre', $request->veterinaria_nombre)->first();
        if ($existingVet) {
            return response()->json(['message' => 'El nombre de la veterinaria ya está en uso'], 422);
        }
        $existingUser = User::where('username', $request->username)->first();
        if ($existingUser) {
            return response()->json(['message' => 'El nombre de usuario ya está en uso'], 422);
        }

        DB::beginTransaction();
        try {
            $vet = Veterinaria::create([
                'nombre' => $request->veterinaria_nombre,
                'direccion' => $request->veterinaria_direccion,
                'telefono' => $request->veterinaria_telefono,
                'email' => $request->veterinaria_email,
                'descripcion' => $request->veterinaria_descripcion,
                'estado' => 'Activo',
                'color' => $request->veterinaria_color,
                'logo' => 'defaultImage.png',
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'role' => 'Admin', // el que registra queda admin
                'password' => Hash::make($request->password),
                'veterinaria_id' => $vet->id,
                'avatar' => 'defaultAvatar.png',
            ]);

            $user->load('veterinaria');

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)
            ->with('veterinaria')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Usuario o contraseña incorrectos'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function me(Request $request)
    {
        return $request->user()->load('veterinaria');
    }

    public function updateMe(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'username' => ['required','string','max:80', Rule::unique('users','username')->ignore($user->id)],
            'email' => ['nullable','email','max:150', Rule::unique('users','email')->ignore($user->id)],
        ]);

        $user->update($data);

        return $user->fresh()->load('veterinaria');
    }

    public function updateMyPassword(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
//        current_password=''
        if (!isset($request->current_password) || !isset($request->password)) {
            return response()->json(['message' => 'Se requieren la contraseña actual y la nueva contraseña.'], 422);
        }
        if ($request->current_password === $request->password) {
            return response()->json(['message' => 'La nueva contraseña debe ser diferente a la actual.'], 422);
        }

        $data = $request->validate([
            'current_password' => ['required','string'],
            'password' => ['required','string','min:6','confirmed'], // necesita password_confirmation
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' => 'Contraseña actualizada']);
    }

    public function updateMyAvatar(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

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
            ->cover(320, 320)
            ->toJpeg(75)
            ->save($path);

        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'message' => 'Avatar actualizado',
            'avatar' => $filename,
            'user' => $user->fresh()->load('veterinaria')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token eliminado']);
    }
}
