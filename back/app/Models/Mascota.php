<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mascota extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'mascotas';

    protected $fillable = [
        'nombre',
        'apellido',
        'especie',
        'raza',
        'sexo',
        'fecha_nac',
        'edad',
        'senas_particulares',
        'color',
        'photo',

        'propietario_nombre',
        'propietario_ci',
        'propietario_direccion',
        'propietario_telefono',
        'propietario_ciudad',
        'propietario_celular',

        'veterinaria_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
//    veterinaria
    public function veterinaria() {
        return $this->belongsTo(Veterinaria::class);
    }
}
