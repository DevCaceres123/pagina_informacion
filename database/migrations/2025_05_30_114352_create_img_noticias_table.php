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
        Schema::create('img_noticias', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('imagen')->nullable();
            
            $table->unsignedBigInteger('noticia_id');
            $table->foreign('noticia_id')
                ->references('id')
                ->on('noticias')
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
        Schema::dropIfExists('img_noticias');
    }
};
