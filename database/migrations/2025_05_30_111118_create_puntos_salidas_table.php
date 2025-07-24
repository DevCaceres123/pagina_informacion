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
        Schema::create('puntos_salidas', function (Blueprint $table) {
            $table->id();
            $table->string('ubicacion', 150);
            $table->unsignedBigInteger('sede_id');
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('sede_id')        
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE puntos_salidas ADD COLUMN punto geometry(Point, 4326)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_salidas');
    }
};
