<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estadistica_titulados', function (Blueprint $table) {
            $table->id();
            $table->string('nombreCompleto', 200);
            $table->string('documentoIdentidad', 100);
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('sede_id');
            $table->enum('genero', ['masculino','femenino']);
            $table->date('fecha_colacion');
            $table->enum('grado_academico', ['tecnico medio','tecnico superior','licenciatura']);
            $table->timestamp('deleted_at')->nullable();
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

            // Indices e indice unico compuesto
            $table->unique(['carrera_id', 'sede_id', 'documentoIdentidad', 'fecha_colacion'], 'unique_titulado');
            $table->index(['fecha_colacion'], 'idx_fecha');
            $table->index(['carrera_id', 'sede_id'], 'idx_carrera_sede');
            $table->index(['genero'], 'idx_genero');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadistica_titulados');
    }
};
