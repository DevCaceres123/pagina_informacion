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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->text('contenido');
            $table->enum('estado_destacado', ['activo', 'inactivo']);
            $table->enum('estado_noticia', ['activo', 'inactivo']);
            $table->string('url_video')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sede_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            $table->foreign('sede_id')      
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
