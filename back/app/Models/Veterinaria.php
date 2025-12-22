<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinaria extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'logo',
        'imagen',
        'descripcion',
        'estado',
        'color',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    function users(){
        return $this->hasMany(User::class);
    }
}
