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
        Schema::table('convocatorias', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('categorias_noticias')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convocatorias', function (Blueprint $table) {
            // Primero eliminar la relación
            $table->dropForeign(['categoria_id']);

            // Luego eliminar el campo
            $table->dropColumn('categoria_id');
        });
    }
};
