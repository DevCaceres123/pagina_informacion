<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sede_id');
            $table->unsignedBigInteger('carrera_id');
            $table->string('nombre_completo');
            $table->string('matricula')->unique();
            $table->enum('tipo_documento', ['CI', 'Pasaporte', 'Otro']);
            $table->string('numero_documento');
            $table->year('gestion');
            $table->enum('genero', ['masculino', 'femenino']);

            // Documentos de un solo archivo
            $table->string('certificado_habilitacion')->nullable();
            $table->string('copia_titulo_tec_medio')->nullable();
            $table->string('copia_titulo_tec_superior')->nullable();
            $table->string('copia_titulo_licenciatura')->nullable();

            $table->boolean('aprobado_tec_medio')->default(false);
            $table->boolean('aprobado_tec_superior')->default(false);
            $table->boolean('aprobado_licenciatura')->default(false);
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sede_id')
                ->references('id')->on('sedes')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('carrera_id')
                ->references('id')->on('carreras')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
