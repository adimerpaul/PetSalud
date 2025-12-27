<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();

            $table->dateTime('fecha')->nullable();

            // en Nest guardabas productoName, subtotal, precio, cantidad
            $table->string('productoName')->nullable();

            $table->decimal('subtotal', 10, 2)->nullable()->default(0);
            $table->decimal('precio', 10, 2)->nullable()->default(0);
            $table->integer('cantidad')->nullable()->default(1);

            $table->boolean('anulado')->default(false);

            $table->foreignId('sale_id')->nullable()->constrained('sales');
            $table->foreignId('producto_id')->nullable()->constrained('productos');

            $table->foreignId('mascota_id')->nullable()->constrained('mascotas');
            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
