<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_botones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();      // Identificador interno            
            $table->text('url');                         // URL del sistema
            $table->boolean('activo')->default(true);    // Mostrar u ocultar
            $table->timestamps();                        // Opcional: creado/actualizado
        });

        // Insertar los 4 botones iniciales
        DB::table('config_botones')->insert([
            [
                'clave' => 'btn_sistema_estudiante',                
                'url' => 'https://sistema1.com',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'btn_sistema_titulado',                
                'url' => 'https://sistema2.com',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'btn_sistema_docentes',                
                'url' => 'https://sistema3.com',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'btn_sistema_administrativo',                
                'url' => 'https://sistema4.com',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('config_botones');
    }
};
