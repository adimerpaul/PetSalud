<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Veterinaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'veterinaria_nombre' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

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

            $user->load('veterinaria:id,nombre,color,estado');

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
            ->with('veterinaria:id,nombre,color,estado')
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
        $user = $request->user()->load('veterinaria:id,nombre,color,estado');
        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token eliminado']);
    }
}
