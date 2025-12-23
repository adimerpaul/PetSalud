<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tratamiento extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'historial_clinico_id',
        'medicamento','dosis','frecuencia','duracion',
        'indicaciones','costo','pagado'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function historial()
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinico_id');
    }
}
