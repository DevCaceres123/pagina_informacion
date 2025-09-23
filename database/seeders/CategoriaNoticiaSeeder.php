<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriasNoticia;

class CategoriaNoticiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'noticias',
                'descripcion' => 'Noticias generales de la institución',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'eventos',
                'descripcion' => 'Eventos académicos y sociales',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'comunicados',
                'descripcion' => 'Comunicados oficiales de la institución',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'convocatorias',
                'descripcion' => 'Convocatorias y llamados públicos',
                'estado' => 'activo',
            ],
        ];

        foreach ($categorias as $data) {
            $categoria = new CategoriasNoticia();
            $categoria->nombre = $data['nombre'];
            $categoria->descripcion = $data['descripcion'];
            $categoria->estado = $data['estado'];
            $categoria->save();
        }
    }

}
