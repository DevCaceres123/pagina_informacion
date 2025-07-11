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
        Schema::create('administrativos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('paterno', 30);
            $table->string('materno', 30);
            $table->string('ci', 20)->unique();
            $table->string('cargo', 50);
            $table->string('telefono', 15)->nullable();

            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')
                ->references('id')
                ->on('carreras')
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
        Schema::dropIfExists('administrativos');
    }
};
