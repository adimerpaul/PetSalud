<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'tipo','pago','comentarioDoctor',
        'fecha','fechaCreacion','facturado',
        'nombre','ci','total','anulado',
        'mascota_id','user_id','veterinaria_id'
    ];

    protected $casts = [
        'facturado' => 'boolean',
        'anulado' => 'boolean',
        'fecha' => 'datetime',
        'fechaCreacion' => 'datetime',
        'total' => 'decimal:2',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }
    function veterinaria()
    {
        return $this->belongsTo(Veterinaria::class);
    }
}
