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
        Schema::create('planos_infraestructura', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->unsignedBigInteger('infraestructura_id');
            $table->foreign('infraestructura_id')
                ->references('id')
                ->on('infraestructuras')
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
        Schema::dropIfExists('planos_infraestructura');
    }
};
