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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('paterno');
            $table->string('materno');
            $table->string('ci');
            $table->string('ru', 20);

            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')
                ->references('id')
                ->on('carreras')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->timestamps();
        });

        DB::statement("ALTER TABLE estudiantes ADD COLUMN punto geometry(Point, 4326)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
