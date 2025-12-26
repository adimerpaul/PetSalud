<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TratamientoProducto extends Model
{
    protected $table = 'tratamiento_productos';
    use SoftDeletes;

    protected $fillable = [
        'tratamiento_id',
        'producto_id',
        'cantidad',
        'precio'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
    }
}
