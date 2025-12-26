<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tratamiento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'historial_clinico_id',
        'user_id',
        'fecha',
        'observaciones',
        'comentario',
        'costo',
        'pagado'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function historial()
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinico_id');
    }

    public function productos()
    {
        return $this->hasMany(TratamientoProducto::class, 'tratamiento_id');
    }
}
