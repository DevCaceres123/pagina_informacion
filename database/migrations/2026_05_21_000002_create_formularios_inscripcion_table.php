<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('formularios_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id');
            $table->string('archivo');
            $table->date('fecha_recepcion');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estudiante_id')
                ->references('id')->on('estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formularios_inscripcion');
    }
};
