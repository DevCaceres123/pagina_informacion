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
        Schema::create('estadistica_administrativos', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('sede_id');
            $table->string('nombre_completo', 200);
            $table->string('n_documento', 200);
            $table->enum('genero', ['masculino', 'femenino']);            
            $table->string('cargo', 100);
            $table->string('profesion', 150);
            $table->enum('servicio', ['planta', 'contrato','linea']);     
            $table->year('gestion');   
            $table->enum('estado', ['activo', 'inactivo']);     
            // $table->foreign('carrera_id')
            //     ->references('id')
            //     ->on('carreras')
            //     ->onDelete('restrict')
            //     ->onUpdate('cascade');

            $table->foreign('sede_id')
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->unique(['sede_id', 'n_documento', 'cargo'], 'unique_administrativo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadistica_administrativos');
    }
};
