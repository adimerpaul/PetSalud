<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sale_details';

    protected $fillable = [
        'fecha',
        'productoName',
        'subtotal','precio','cantidad',
        'anulado',
        'sale_id','producto_id',
        'mascota_id','user_id',
    ];

    protected $casts = [
        'anulado' => 'boolean',
        'fecha' => 'datetime',
        'subtotal' => 'decimal:2',
        'precio' => 'decimal:2',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
