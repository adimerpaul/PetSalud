<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialClinico extends Model
{
    use SoftDeletes;

    protected $table = 'historiales_clinicos';

    protected $fillable = [
        'mascota_id','user_id','veterinaria_id','fecha',
        'peso','tr','fc','fr','tllc','thc','pulso','apetito',
        'cf','mucosidad','esterilizado','desparasitacion',
        'parvo','hexa','octa','rabica','triple',
        'rayox','laboratorio','ecografia',
        'anamnesis','diagnostico','pronostico','observaciones'
    ];

    public function mascota() {
        return $this->belongsTo(Mascota::class);
    }

    public function tratamientos() {
        return $this->hasMany(Tratamiento::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
