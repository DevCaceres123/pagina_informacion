<?php

namespace App\Imports;

use App\Models\EstadisticaEstudiante;
use Maatwebsite\Excel\Concerns\ToModel;

class EstudiantesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new EstadisticaEstudiante([
            'carrera_id'       => $row['nombre_carrera'],       // si tienes un id, mejor
            'sede_id'          => $row['nombre_sede'],          // si tienes un id, mejor
            'hombres'          => $row['cantidad_hombres'] ?? 0,
            'mujeres'          => $row['cantidad_mujeres'] ?? 0,
            'total'            => $row['total_estudiantes'] ?? 0,
            'gestion'          => $row['gestion'],
        ]);

    }


    public function chunkSize(): int
    {
        return 1000;
    }
}
