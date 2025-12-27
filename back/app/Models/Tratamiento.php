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
//    user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

//
//    use Illuminate\Database\Migrations\Migration;
//    use Illuminate\Database\Schema\Blueprint;
//    use Illuminate\Support\Facades\Schema;
//
//return new class extends Migration
//{
//    /**
//     * Run the migrations.
//     */
//    public function up(): void
//    {
//        Schema::create('tratamiento_productos', function (Blueprint $table) {
//            $table->id();
//
//            $table->foreignId('tratamiento_id')->constrained()->cascadeOnDelete();
//            $table->foreignId('producto_id')->constrained('productos');
//
//            $table->integer('cantidad')->default(1);
//            $table->decimal('precio', 10, 2)->default(0);
//
//            $table->softDeletes();
//            $table->timestamps();
//        });
//
//    }
//
//    /**
//     * Reverse the migrations.
//     */
//    public function down(): void
//    {
//        Schema::dropIfExists('tratamiento_productos');
//    }
//}
//
//;
//    tratramiuntos productos
    function tratamientoProductos()
    {
        return $this->hasMany(TratamientoProducto::class, 'tratamiento_id');
    }
}
