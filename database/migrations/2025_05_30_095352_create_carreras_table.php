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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion', 150);
            $table->enum('modalidad', ['semestral', 'anual']);
            $table->enum('estado', ['activo', 'inactivo']);
            $table->string('malla_curricular_pdf')->nullable();
            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
