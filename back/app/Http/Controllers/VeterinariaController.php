<?php

namespace App\Http\Controllers;

use App\Models\Veterinaria;
use Illuminate\Http\Request;

class VeterinariaController extends Controller
{
    public function index(){
        return Veterinaria::orderBy('id','desc')->get();
    }

    public function store(Request $request){
        $data = $request->validate([
            'nombre'=>'required',
            'direccion'=>'nullable',
            'telefono'=>'nullable',
            'email'=>'nullable',
            'logo'=>'nullable',
            'imagen'=>'nullable',
            'descripcion'=>'nullable',
            'estado'=>'nullable',
            'color'=>'nullable',
        ]);
        return Veterinaria::create($data);
    }

    public function update(Request $request, Veterinaria $veterinaria){
        $veterinaria->update($request->all());
        return $veterinaria;
    }

    public function destroy(Veterinaria $veterinaria){
        $veterinaria->delete();
        return response()->json(['message'=>'Eliminado']);
    }
}
