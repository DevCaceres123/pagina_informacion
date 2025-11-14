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
        Schema::create('estadistica_docentes', function (Blueprint $table) {
            $table->id();       
            $table->string('nombreCompleto', 200);
            $table->string('documento_identidad', 100);                 
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('sede_id');
            $table->enum('genero', ['masculino','femenino']);
            $table->year('gestion');
            $table->string('profesion', 200);
            $table->string('grado_academico', 200);
             $table->enum('estado', ['activo','inactivo']);                    

            $table->foreign('carrera_id')
                ->references('id')
                ->on('carreras')
                ->onDelete('restrict')
                ->onUpdate('cascade');


            $table->foreign('sede_id')
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->timestamps();

            // Indices e indice unico compuesto
            $table->unique(['carrera_id', 'sede_id', 'documento_identidad', 'gestion'], 'unique_docente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadistica_docentes');
    }
};
