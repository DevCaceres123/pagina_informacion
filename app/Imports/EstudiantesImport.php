<?php

namespace App\Imports;

use App\Models\EstadisticaEstudiante;
use App\Models\Carrera;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;// indica que la primera fila es cabezera
use Maatwebsite\Excel\Concerns\WithValidation;// permite definir reglas de validadion para cada fila
use Maatwebsite\Excel\Concerns\SkipsOnFailure;// si una filla falla nose detendra
use Maatwebsite\Excel\Concerns\SkipsFailures;// nos permite acceder a los errores
use Maatwebsite\Excel\Concerns\Importable; // para ejecutar la importacion desde el controlador

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;
    use SkipsFailures;

    /**
     * 🔹 Aquí guardaremos las filas que tengan errores personalizados,
     * por ejemplo cuando no se encuentra la carrera o la sede.
     */
    public $erroresPersonalizados = [];
    public $filasInsertadas = 0;

    /**
     * 🔹 Convierte cada fila del archivo en un modelo de base de datos.
     * Se ejecuta automáticamente por cada fila del CSV/Excel.
     */
    public function model(array $row)
    {
        // 🔤 Normalizamos los textos de carrera y sede
        $nombreCarrera = $this->normalizeText($row['carrera'] ?? '');
        $nombreSede    = $this->normalizeText($row['sede'] ?? '');

        // 🔍 Buscamos carrera y sede en la base de datos ya normalizadas
        $carrera = Carrera::whereRaw('LOWER(nombre) = ?', [$nombreCarrera])->first();
        $sede    = Sede::whereRaw('LOWER(nombre) = ?', [$nombreSede])->first();

        // Si no se encuentra la carrera o la sede, registramos un error personalizado
        if (!$carrera) {
            $this->erroresPersonalizados[] = [
                'fila'   => $row['fila'] ?? 'desconocida',
                'campo'  => 'carrera',
                'valor'  => $row['carrera'] ?? '',
                'mensaje' => "Carrera '{$row['carrera']}' no encontrada."
            ];
            return null; // no insertamos la fila
        }

        if (!$sede) {
            $this->erroresPersonalizados[] = [
                'fila'   => $row['fila'] ?? 'desconocida',
                'campo'  => 'sede',
                'valor'  => $row['sede'] ?? '',
                'mensaje' => "Sede '{$row['sede']}' no encontrada."
            ];
            return null;
        }

        // 🧩 Validamos que exista la relación carrera-sede
        $relacionExiste = \DB::table('carrera_sede')
            ->where('carrera_id', $carrera->id)
            ->where('sede_id', $sede->id)
            ->exists();

        if (!$relacionExiste) {
            $this->erroresPersonalizados[] = [
                'fila'   => $row['fila'] ?? 'desconocida',
                'campo'  => 'relacion_carrera_sede',
                'valor'  => "{$row['carrera']} - {$row['sede']}",
                'mensaje' => "La relación entre carrera '{$row['carrera']}' y sede '{$row['sede']}' no existe en la tabla."
            ];
            return null;
        }

        $this->filasInsertadas++;


        // Si todo está bien, actualizamos o insertamos
        return EstadisticaEstudiante::updateOrCreate(
            [
                'carrera_id' => $carrera->id,
                'sede_id'    => $sede->id,
                'gestion'    => $row['gestion'],
            ],
            [
                'mujeres' => $row['femenino'] ?? 0,
                'hombres' => $row['masculino'] ?? 0,
                'total'   => $row['total'] ?? 0,
            ]
        );
    }

    /**
     * 🔹 Reglas de validación para cada fila.
     * Si alguna no cumple, se salta y se registra automáticamente el error.
     */
    public function rules(): array
    {
        return [
            'carrera'   => 'required|string',
            'sede'      => 'required|string',
            'gestion'   => 'required|numeric',
            'femenino'  => 'nullable|numeric|min:0',
            'masculino' => 'nullable|numeric|min:0',
            'total'     => 'nullable|numeric|min:0',
        ];
    }

    /**
     * 🔹 Procesa los archivos por partes (chunks) para no sobrecargar memoria.
     * Ideal cuando el archivo tiene miles de filas.
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * 🔧 Función para normalizar textos:
     * - Convierte a minúsculas
     * - Elimina tildes y diéresis
     * - Quita espacios extra
     */
    private function normalizeText(string $text): string
    {
        // Quita espacios no separables, saltos de línea y tabs
        $text = preg_replace('/[\xA0\xC2\xAD\s]+/u', ' ', $text);
        $text = trim($text);

        // Convierte a minúsculas
        $text = mb_strtolower($text, 'UTF-8');

        // Reemplaza acentos y eñes
        $text = strtr($text, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'ñ' => 'n', 'Ñ' => 'n',
        ]);

        return $text;
    }
}
