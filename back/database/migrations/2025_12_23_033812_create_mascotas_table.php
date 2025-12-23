<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('especie')->nullable();
            $table->string('raza')->nullable();
            $table->string('sexo')->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('edad')->nullable();
            $table->string('senas_particulares')->nullable();
            $table->string('color')->nullable();
            $table->string('photo')->nullable(); // nombre de archivo

            // propietario
            $table->string('propietario_nombre')->nullable();
            $table->string('propietario_ci')->nullable();
            $table->string('propietario_direccion')->nullable();
            $table->string('propietario_telefono')->nullable();
            $table->string('propietario_ciudad')->nullable();
            $table->string('propietario_celular')->nullable();

            // si manejas multi veterinaria
            $table->unsignedBigInteger('veterinaria_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
