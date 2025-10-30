<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PreviewEstudiantesImport implements ToCollection, WithHeadingRow
{
    /**
     * Aquí se almacenarán los datos leídos desde el archivo.
     */
    public $data;

    /**
     * Procesa las filas del archivo Excel o CSV.
     */
    public function collection(Collection $collection)
    {
        // Ahora las claves de cada fila serán los nombres de las cabeceras
        $this->data = $collection;
    }
}
