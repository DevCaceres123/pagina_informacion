<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estudiante;
use App\Models\Sede;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;

class SeguimientoEstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // Cargamos solo los pares carrera-sede que realmente existen
        $pares = DB::table('carrera_sede')->select('carrera_id', 'sede_id')->get()->toArray();

        if (empty($pares)) {
            $this->command->error('No hay relaciones carrera-sede. Ejecute CarreraSedeSeeder primero.');
            return;
        }

        $nombres = [
            'juan carlos', 'pedro pablo', 'maria elena', 'rosa ana', 'luis alberto',
            'carlos antonio', 'ana maria', 'jose miguel', 'andrea paola', 'diego armando',
            'patricia roxana', 'roberto carlos', 'claudia beatriz', 'miguel angel', 'jessica lorena',
            'edgar ivan', 'silvia carolina', 'ronald david', 'veronica susana', 'marco antonio',
            'sandra patricia', 'nelson omar', 'gabriela alejandra', 'richard henry', 'natalia vanessa',
            'hugo cesar', 'miriam alejandra', 'franz daniel', 'elizabeth ruth', 'alex rodrigo',
            'paola andrea', 'ivan rodrigo', 'karen daniela', 'omar alejandro', 'ruth miriam',
            'erick fabian', 'diana carolina', 'freddy jhonny', 'lidia rosa', 'cesar augusto',
            'wendy elizabeth', 'bryan alejandro', 'gloria esther', 'wilber rodrigo', 'jenny luz',
            'augusto pablo', 'noelia fernanda', 'raul ernesto', 'cinthia lorena', 'mauricio andres',
        ];

        $apellidosPaternos = [
            'mamani', 'quispe', 'condori', 'huanca', 'flores', 'lima', 'choque', 'garcia',
            'rodriguez', 'lopez', 'martinez', 'gutierrez', 'apaza', 'ticona', 'calle',
            'vargas', 'mendoza', 'rojas', 'herrera', 'morales', 'ramos', 'molina',
            'chuquimia', 'laura', 'poma', 'calcina', 'soliz', 'alvarado', 'nina',
            'catari', 'atahuichi', 'tarqui', 'callisaya', 'paco', 'patzi', 'mita',
            'caceres', 'colque', 'turpo', 'zenteno', 'soria', 'alanoca', 'mamani',
            'hilari', 'cusi', 'pari', 'churata', 'quisbert', 'villca', 'laime',
        ];

        $apellidosMaternos = [
            'quispe', 'mamani', 'flores', 'condori', 'garcia', 'huanca', 'lima',
            'rodriguez', 'choque', 'lopez', 'apaza', 'ticona', 'vargas', 'mendoza',
            'gutierrez', 'calle', 'rojas', 'morales', 'herrera', 'ramos', 'molina',
            'laura', 'poma', 'calcina', 'nina', 'catari', 'tarqui', 'colque',
            'soria', 'zenteno', 'paco', 'alvarado', 'callisaya', 'alanoca', 'hilari',
            'cusi', 'pari', 'churata', 'quisbert', 'villca', 'laime', 'chuquimia',
            'turpo', 'mita', 'soliz', 'caceres', 'patzi', 'atahuichi', 'huallpa', 'yujra',
        ];

        $tiposDocumento  = ['CI', 'CI', 'CI', 'CI', 'CI', 'CI', 'Pasaporte', 'Otro'];
        $generos         = ['masculino', 'femenino'];
        $gestiones       = [2019, 2020, 2021, 2022, 2023, 2024, 2025];
        $observaciones   = [
            null, null, null,
            'pendiente entrega de documentos',
            'en proceso de titulacion',
            'documentos completos',
            'requiere revision de nota',
            'egresado con pendiente',
            null,
        ];

        $registros = [];
        $matriculasUsadas = [];
        $contador = 0;

        DB::beginTransaction();

        while ($contador < 500) {
            $apPaterno = $apellidosPaternos[array_rand($apellidosPaternos)];
            $apMaterno = $apellidosMaternos[array_rand($apellidosMaternos)];
            $nombre    = $nombres[array_rand($nombres)];

            $nombreCompleto = "{$apPaterno} {$apMaterno} {$nombre}";

            // Matricula única: prefijo año + 6 dígitos
            do {
                $gestion   = $gestiones[array_rand($gestiones)];
                $matricula = $gestion . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($matricula, $matriculasUsadas));

            $matriculasUsadas[] = $matricula;

            $tipoDoc = $tiposDocumento[array_rand($tiposDocumento)];
            $numDoc  = match ($tipoDoc) {
                'CI'        => (string) rand(1000000, 9999999),
                'Pasaporte' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2)) . rand(100000, 999999),
                default     => 'OTRO-' . rand(10000, 99999),
            };

            $par = $pares[array_rand($pares)];

            $registros[] = [
                'sede_id'          => $par->sede_id,
                'carrera_id'       => $par->carrera_id,
                'nombre_completo'  => $nombreCompleto,
                'matricula'        => $matricula,
                'tipo_documento'   => $tipoDoc,
                'numero_documento' => strtolower($numDoc),
                'gestion'          => $gestion,
                'genero'           => $generos[array_rand($generos)],
                'observacion'      => $observaciones[array_rand($observaciones)],
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $contador++;
        }

        // Insertar en lotes de 100 para no saturar la memoria
        foreach (array_chunk($registros, 100) as $lote) {
            DB::table('estudiantes')->insert($lote);
        }

        DB::commit();

        $this->command->info('✅ 500 estudiantes de seguimiento creados correctamente.');
    }
}
