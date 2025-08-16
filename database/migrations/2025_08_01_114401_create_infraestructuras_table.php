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
        Schema::create('infraestructuras', function (Blueprint $table) {
            $table->id();            
            $table->string('propiedad',150);
            $table->string('uso_asignado',150);
            $table->string('contrato',150);        
            $table->enum('estado_inmueble', ['bueno', 'mediano','malo']);
            $table->enum('estado', ['inicial', 'proceso','finalizado']);
            $table->string('observacion_estado');
            $table->date('fecha_inicio');
            $table->date('fecha_final');    
            $table->string('numero_nota',50)->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('sede_id');
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
        Schema::dropIfExists('infraestructuras');
    }
};
