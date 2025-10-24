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
        Schema::create('img_convocatorias', function (Blueprint $table) {
           $table->id();
            
            $table->string('imagen')->nullable();
            
            $table->unsignedBigInteger('convocatoria_id');
            $table->foreign('convocatoria_id')
                ->references('id')
                ->on('convocatorias')
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
        Schema::dropIfExists('img_convocatorias_table');
    }
};
