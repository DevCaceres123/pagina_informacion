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
        Schema::create('img_sedes', function (Blueprint $table) {
            $table->id();
            //$table->string('descripcion', 150)->nullable();
            $table->string('imagen')->nullable();

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')
               ->references('id')
               ->on('sedes')
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
        Schema::dropIfExists('img_sedes');
    }
};
