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
        Schema::create('carrera_img_sedes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('img_sede_id');
            $table->foreign('carrera_id')
                ->references('id')
                ->on('carreras')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('img_sede_id')
                ->references('id')
                ->on('img_sedes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera_img_sedes');
    }
};
