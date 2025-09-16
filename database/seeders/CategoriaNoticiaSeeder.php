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
                'nombre' => 'Noticias',
                'descripcion' => 'Noticias generales de la institución',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Eventos',
                'descripcion' => 'Eventos académicos y sociales',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Comunicados',
                'descripcion' => 'Comunicados oficiales de la institución',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Convocatorias',
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
