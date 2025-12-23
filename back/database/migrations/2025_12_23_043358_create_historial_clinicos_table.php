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
        Schema::create('historiales_clinicos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mascota_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('veterinaria_id')->nullable();

            // SIGNOS VITALES
            $table->float('peso')->nullable();
            $table->string('tr')->nullable();   // temperatura rectal
            $table->string('fc')->nullable();   // frecuencia cardíaca
            $table->string('fr')->nullable();   // frecuencia respiratoria
            $table->string('tllc')->nullable(); // tiempo de llenado capilar
            $table->string('thc')->nullable();  // tiempo de hidratación
            $table->string('pulso')->nullable();
            $table->string('apetito')->nullable();
            $table->string('cf')->nullable();   // condición física
            $table->string('mucosidad')->nullable();
            $table->string('esterilizado')->nullable();
            $table->string('desparasitacion')->nullable();

            // VACUNAS
            $table->string('parvo')->nullable();
            $table->string('hexa')->nullable();
            $table->string('octa')->nullable();
            $table->string('rabica')->nullable();
            $table->string('triple')->nullable();

            // EXÁMENES
            $table->string('rayox')->nullable();
            $table->string('laboratorio')->nullable();
            $table->string('ecografia')->nullable();

            // CLÍNICA
            $table->text('anamnesis')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('pronostico')->nullable();
            $table->text('observaciones')->nullable();

            $table->date('fecha');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiales_clinicos');
    }
};
