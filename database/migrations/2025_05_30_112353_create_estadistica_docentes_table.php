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
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('sede_id');
            $table->year('gestion');
            $table->integer('hombres')->default(0);
            $table->integer('mujeres')->default(0);
            $table->integer('total')->default(0);

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
