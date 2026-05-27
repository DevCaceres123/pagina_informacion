<?php

namespace App\Imports;

use App\Models\Estudiante;
use App\Models\Carrera;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class SeguimientoEstudianteImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    use Importable, SkipsFailures;

    public $erroresPersonalizados = [];
    public $filasInsertadas = 0;

    private $carreras;
    private $sedes;
    private $relaciones;
    private $dataToInsert = [];

    public function __construct()
    {
        // Pre-cargamos todo en memoria para evitar queries por fila
        $this->carreras = Carrera::where('estado', 'activo')->select('id', 'nombre')->get()
            ->mapWithKeys(fn($c) => [$this->normalizeText($c->nombre) => $c->id]);

        $this->sedes = Sede::where('estado', 'activo')->select('id', 'nombre')->get()
            ->mapWithKeys(fn($s) => [$this->normalizeText($s->nombre) => $s->id]);

        $this->relaciones = DB::table('carrera_sede')
            ->select('carrera_id', 'sede_id')
            ->get()
            ->map(fn($r) => "{$r->carrera_id}-{$r->sede_id}")
            ->toArray();
    }

    public function model(array $row)
    {
        $nombreCarrera = $this->normalizeText($row['carrera'] ?? '');
        $nombreSede    = $this->normalizeText($row['sede'] ?? '');

        $carreraId = $this->carreras[$nombreCarrera] ?? null;
        $sedeId    = $this->sedes[$nombreSede] ?? null;

        if (!$carreraId) {
            $this->erroresPersonalizados[] = [
                'campo'   => 'carrera',
                'mensaje' => "Carrera '{$row['carrera']}' no encontrada o no está activa.",
            ];
            return null;
        }

        if (!$sedeId) {
            $this->erroresPersonalizados[] = [
                'campo'   => 'sede',
                'mensaje' => "Sede '{$row['sede']}' no encontrada o no está activa.",
            ];
            return null;
        }

        if (!in_array("{$carreraId}-{$sedeId}", $this->relaciones)) {
            $this->erroresPersonalizados[] = [
                'campo'   => 'relacion_carrera_sede',
                'mensaje' => "La carrera '{$row['carrera']}' no existe en la sede '{$row['sede']}'.",
            ];
            return null;
        }

        $this->dataToInsert[] = [
            'sede_id'          => $sedeId,
            'carrera_id'       => $carreraId,
            'nombre_completo'  => $this->normalizeText($row['nombre_completo']),
            'matricula'        => trim($row['matricula']),
            'tipo_documento'   => $row['tipo_documento'],
            'numero_documento' => $this->normalizeText($row['numero_documento']),
            'gestion'          => $row['gestion'],
            'genero'           => $this->normalizeText($row['genero']),
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        $this->filasInsertadas++;

        // Insertar en bloque cada 1000 filas
        if (count($this->dataToInsert) >= 1000) {
            $this->flushData();
        }

        return null;
    }

    public function finalize(): void
    {
        if (!empty($this->dataToInsert)) {
            $this->flushData();
        }
    }

    private function flushData(): void
    {
        Estudiante::upsert(
            $this->dataToInsert,
            ['matricula'],
            ['nombre_completo', 'tipo_documento', 'numero_documento', 'sede_id', 'carrera_id', 'gestion', 'genero', 'updated_at']
        );

        $this->dataToInsert = [];
    }

    public function rules(): array
    {
        return [
            'nombre_completo'  => 'required|string|min:3|max:150',
            'matricula'        => 'required',
            'tipo_documento'   => 'required|in:CI,Pasaporte,Otro',
            'numero_documento' => 'required',
            'sede'             => 'required|string',
            'carrera'          => 'required|string',
            'gestion'          => 'required|numeric|min:2000|max:2100',
            'genero'           => 'required|in:masculino,femenino',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
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
}
