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
        Schema::table('estadistica_titulados', function (Blueprint $table) {
            // Creamos el índice único compuesto
            $table->unique(
                ['carrera_id', 'sede_id', 'documentoIdentidad', 'fecha_colacion'],
                'estadistica_titulados_unique_combo'
            );
        });
    }

    public function down(): void
    {
        Schema::table('estadistica_titulados', function (Blueprint $table) {
            // Eliminamos el índice si se hace rollback
            $table->dropUnique('estadistica_titulados_unique_combo');
        });
    }

};
