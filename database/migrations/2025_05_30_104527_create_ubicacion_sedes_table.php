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
        Schema::create('ubicacion_sedes', function (Blueprint $table) {
            $table->id();
            $table->string('ubicacion', 100);
            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
        });
        // Campos geoespaciales
        // Asegura que PostGIS esté disponible
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
        DB::statement("ALTER TABLE ubicacion_sedes ADD COLUMN punto geometry(Point, 4326)");
        DB::statement("ALTER TABLE ubicacion_sedes ADD COLUMN poligono geometry(Polygon, 4326)");
        DB::statement("ALTER TABLE ubicacion_sedes ADD COLUMN linea geometry(LineString, 4326)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicacion_sedes');
    }
};
