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
        $sedes = [
            [
                'nombre' => 'Villa esperanza',
                'descripcion' => 'Noticias generales de la institución',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Achacachi',
                'descripcion' => 'Noticias generales de la institución',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Ancoraimes',
                'descripcion' => 'Eventos académicos y sociales',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Caranavi',
                'descripcion' => 'Comunicados oficiales de la institución',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Coroico - Cruz Loma',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Guaqui',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Batallas',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Mapiri',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Palos Blancos',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Chaguaya',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'Viacha',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
            [
                'nombre' => 'San Pablo',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
                'usuario_id' => 1,
            ],
        ];

        foreach ($sedes as $data) {
            $sede = new Sede();
            $sede->nombre = $data['nombre'];
            $sede->descripcion = $data['descripcion'];
            $sede->estado = $data['estado'];
            $sede->usuario_id = $data['usuario_id'];
            $sede->save();
        }
    }
}
