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
        Schema::create('estadisticas_estudiantes', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('carrera_id')->constrained()->onDelete('cascade');
            $table->year('gestion'); // por ejemplo: 2025
            $table->integer('cantidad_hombres')->default(0);
            $table->integer('cantidad_mujeres')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();

            $table->unique(['carrera_id', 'gestion']); // evita duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadisticas_estudiantes');
    }
};
