<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request){
        // si quieres limitar a su veterinaria:
        $user = $request->user();
        return User::where('veterinaria_id', $user->veterinaria_id)
            ->orderBy('id','desc')
            ->get();
    }

    public function store(Request $request){
        $data = $request->validate([
            'name'=>'required',
            'username'=>'required|unique:users,username',
            'email'=>'nullable|email|unique:users,email',
            'password'=>'required|min:6',
            'role'=>'nullable',
            'veterinaria_id'=>'nullable|exists:veterinarias,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['avatar'] = $data['avatar'] ?? 'defaultAvatar.png';

        return User::create($data);
    }

    public function update(Request $request, User $user){
        $user->update($request->except('password'));
        return $user;
    }

    public function updatePassword(Request $request, User $user){
        $request->validate(['password'=>'required|min:6']);
        $user->update(['password' => Hash::make($request->password)]);
        return $user;
    }

    public function destroy(User $user){
        $user->delete();
        return response()->json(['message'=>'Eliminado']);
    }
}
