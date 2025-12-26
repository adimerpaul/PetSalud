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
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('historial_clinico_id')->constrained()->cascadeOnDelete();
//            $table->foreignId('user_id')->nullable();
            $table->unsignedBigInteger('historial_clinico_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('historial_clinico_id')->references('id')->on('historiales_clinicos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');

            $table->date('fecha')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('comentario')->nullable();
            $table->decimal('costo',10,2)->default(0);
            $table->boolean('pagado')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
