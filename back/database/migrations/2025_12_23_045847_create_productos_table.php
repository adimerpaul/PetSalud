<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('presentacion')->nullable();
            $table->string('contenido')->nullable();

            $table->decimal('precioCompra', 10, 2)->nullable();
            $table->decimal('precioVenta', 10, 2)->nullable();

            $table->integer('stock')->default(1);
            $table->boolean('activo')->default(true);

            $table->string('tipo')->default('Producto');
            $table->string('imagen')->default('imagenesdefault.png');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
