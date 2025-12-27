<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('tipo')->nullable()->default('Venta'); // Venta | Gasto
            $table->string('pago')->nullable()->default('Efectivo'); // Efectivo | Qr | Transferencia
            $table->text('comentarioDoctor')->nullable();

            $table->dateTime('fecha')->nullable();
            $table->dateTime('fechaCreacion')->nullable();

            $table->boolean('facturado')->nullable()->default(false);

            // cliente
            $table->string('nombre')->nullable(); // en tu front es "Nombre Cliente"
            $table->string('ci')->nullable();

            $table->decimal('total', 10, 2)->nullable()->default(0);

            $table->boolean('anulado')->default(false);

            $table->foreignId('mascota_id')->nullable()->constrained('mascotas');
            $table->foreignId('user_id')->nullable()->constrained('users');
//            veterinaria_id
            $table->foreignId('veterinaria_id')->nullable()->constrained('veterinarias');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
