<?php

namespace App\Imports;

use App\Models\Carrera;
use App\Models\Sede;
use App\Models\EstadisticaTitulado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{
    Importable,
    SkipsFailures,
    SkipsOnFailure,
    ToModel,
    WithHeadingRow,
    WithValidation
};

class TituladosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public $erroresPersonalizados = [];
    public $filasInsertadas = 0;

    /**
     * 🔹 Caches para evitar repetir consultas a la BD
     */
    private $carreras;
    private $sedes;
    private $relaciones;
    private $dataToInsert = [];

    public function __construct()
    {
        // 🔹 Precargamos carreras, sedes y relaciones carrera_sede
        $this->carreras = Carrera::select('id', 'nombre')->get()
            ->mapWithKeys(fn($c) => [$this->normalizeText($c->nombre) => $c->id]);

        $this->sedes = Sede::select('id', 'nombre')->get()
            ->mapWithKeys(fn($s) => [$this->normalizeText($s->nombre) => $s->id]);

        $this->relaciones = DB::table('carrera_sede')
            ->select('carrera_id', 'sede_id')
            ->get()
            ->map(fn($r) => "{$r->carrera_id}-{$r->sede_id}")
            ->toArray();
    }

    public function model(array $row)
    {
        // Normalizamos texto
        $nombreCarrera = $this->normalizeText($row['carrera'] ?? '');
        $nombreSede = $this->normalizeText($row['sede'] ?? '');

        // Validamos existencia
        $carreraId = $this->carreras[$nombreCarrera] ?? null;
        $sedeId = $this->sedes[$nombreSede] ?? null;

        if (!$carreraId) {
            $this->erroresPersonalizados[] = $this->errorFila($row, 'carrera', "Carrera '{$row['carrera']}' no encontrada.");
            return null;
        }

        if (!$sedeId) {
            $this->erroresPersonalizados[] = $this->errorFila($row, 'sede', "Sede '{$row['sede']}' no encontrada.");
            return null;
        }

        // Validar relación carrera-sede
        $relacionKey = "{$carreraId}-{$sedeId}";
        if (!in_array($relacionKey, $this->relaciones)) {
            $this->erroresPersonalizados[] = $this->errorFila(
                $row,
                'relacion_carrera_sede',
                "La carrera '{$row['carrera']}' no existe en la sede '{$row['sede']}'."
            );
            return null;
        }

        // Guardamos datos para inserción masiva
        $this->dataToInsert[] = [
            'carrera_id' => $carreraId,
            'sede_id' => $sedeId,
            'documentoIdentidad' => $row['documento'],
            'fecha_colacion' => $row['fecha'],
            'nombreCompleto' => $this->normalizeText($row['nombre_completo']) ?? '',
            'genero' => $row['genero'] ?? '',
            'grado_academico' => $row['grado_academico'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->filasInsertadas++;

        // 🔹 Cada 1000 filas, insertamos en bloque para mayor velocidad
        if (count($this->dataToInsert) >= 1000) {
            $this->flushData();
        }

        return null;
    }

    /**
     * 🔹 Inserta los registros pendientes con upsert masivo
     */
    private function flushData()
    {
        EstadisticaTitulado::upsert(
            $this->dataToInsert,
            ['carrera_id', 'sede_id', 'documentoIdentidad', 'fecha_colacion'],
            ['nombreCompleto', 'genero', 'grado_academico', 'updated_at']
        );

        $this->dataToInsert = [];
    }

    /**
     * 🔹 Al finalizar toda la importación, aseguramos guardar lo que falte
     */
    public function __destruct()
    {
        if (!empty($this->dataToInsert)) {
            $this->flushData();
        }
    }

    public function rules(): array
    {
        return [
            'carrera'   => 'required|string',
            'sede'      => 'required|string',
            'fecha'     => 'required|date',
            'documento' => 'required|min:3|max:50',
            'nombre_completo' => 'required|min:3|max:100',
            'genero' => 'required|in:masculino,femenino',
            'grado_academico' => 'required|in:licenciatura,tecnico medio,tecnico superior',
        ];
    }

    private function normalizeText(string $text): string
    {
        $text = preg_replace('/[\xA0\xC2\xAD\s]+/u', ' ', trim($text));
        $text = mb_strtolower($text, 'UTF-8');
        $text = strtr($text, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'ñ' => 'n', 'Ñ' => 'n',
        ]);
        return $text;
    }

    private function errorFila(array $row, string $campo, string $mensaje): array
    {
        return [
            'fila' => $row['fila'] ?? 'desconocida',
            'campo' => $campo,
            'valor' => $row[$campo] ?? '',
            'mensaje' => $mensaje
        ];
    }
}
