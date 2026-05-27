<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('requisitos_defensa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id');
            $table->enum('tipo_titulo', ['tec_medio', 'tec_superior', 'licenciatura']);
            $table->string('nombre');
            $table->string('archivo');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estudiante_id')
                ->references('id')->on('estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitos_defensa');
    }
};
