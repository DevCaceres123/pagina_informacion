<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class PreviewAdministrativoImport implements ToCollection, WithHeadingRow , WithLimit
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        // Solo guardamos las primeras 10 filas
        $this->data = $collection->take(10);
    }
    public function limit(): int
    {
        return 10; // 🔹 Solo lee las primeras 10 filas del archivo
    }
}
