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
        Schema::create('tratamiento_productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tratamiento_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');

            $table->integer('cantidad')->default(1);
            $table->decimal('precio',10,2)->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamiento_productos');
    }
};
