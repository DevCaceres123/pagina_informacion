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
            $table->string('solicitud',150);
            $table->string('nota',150)->nullable();
            $table->string('numero_nota',50)->nullable();
            $table->string('contrato',150)->nullable();        
            $table->enum('estado_inmueble', ['bueno', 'regular','malo']);
            $table->enum('estado_tramite', ['inicial', 'proceso','finalizado']);
            $table->string('observacion_estado');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_final')->nullable();                
            $table->unsignedBigInteger('usuario_id');            
            $table->unsignedBigInteger('sede_id');
            $table->timestamp('deleted_at')->nullable();
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
