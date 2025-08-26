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
        Schema::create('datos_infraestructura', function (Blueprint $table) {
            $table->id();

            // 🔗 Relación con infraestructura
            $table->unsignedBigInteger('infraestructura_id');
            $table->foreign('infraestructura_id')
                  ->references('id')
                  ->on('infraestructuras')
                  ->onDelete('cascade');

            // 📍 Sección 1: Datos de la Ubicación
            $table->string('distrito',10)->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('urb')->nullable();
            $table->string('manzano')->nullable();
            $table->string('lote')->nullable();

            // 📐 Sección 2: Medidas del Inmueble
            $table->decimal('sup_test', 12, 2)->nullable();   // ejemplo: 6245.10 m2
            $table->decimal('sup_lev', 12, 2)->nullable();
            $table->decimal('sup_adju', 12, 2)->nullable();
            $table->decimal('sup_util', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_infraestructura');
    }
};
