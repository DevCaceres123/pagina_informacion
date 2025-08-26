<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sede;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $sede       = new Sede();
        $sede->nombre = 'prueba1';
        $sede->descripcion = 'es solo una prueba de sede para ver si funciona';
        $sede->estado='activo';
        $sede->usuario_id=1;
        $sede->save();

        
    }
}
