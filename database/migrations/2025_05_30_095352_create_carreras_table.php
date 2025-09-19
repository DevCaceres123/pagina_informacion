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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('modalidad', ['semestral', 'anual','mixto']);
            $table->enum('estado', ['activo', 'inactivo']);
            $table->string('malla_curricular_pdf')->nullable();
            $table->string('vinculo_web')->nullable();            
            $table->unsignedBigInteger('usuario_id');
            $table->timestamp('deleted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
