<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrera;
use App\Models\Sede;

class CarreraSedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Relación carrera => sedes (nombres en minúsculas y sin tildes)
        $relaciones = [
            'administracion de empresas' => ['caranavi', 'viacha', 'villa esperanza'],
            'arquitectura' => ['villa esperanza'],
            'artes plasticas' => ['villa esperanza'],
            'ciencias de la comunicacion social' => ['caranavi', 'villa esperanza'],
            'ciencias de la educacion' => ['achacachi','batallas','caranavi','chaguaya','guaqui','viacha','villa esperanza'],
            'ciencias del desarrollo' => ['villa esperanza'],
            'ciencias fisicas y energias alternativas' => ['villa esperanza'],
            'ciencias politicas' => ['villa esperanza'],
            'comercio internacional' => ['villa esperanza'],
            'contaduria publica' => ['achacachi','palos blancos','viacha','villa esperanza'],
            'derecho' => ['achacachi','ancoraimes','caranavi','viacha','villa esperanza'],
            'economia' => ['villa esperanza'],
            'educacion parvularia' => ['coroico - cruz loma','villa esperanza'],
            'enfermeria' => ['achacachi','caranavi','viacha','villa esperanza'],
            'gestion turistica y hotelera' => ['villa esperanza'],
            'historia' => ['villa esperanza'],
            'ingenieria agronomica' => ['caranavi', 'san pablo','villa esperanza'],
            'ingenieria ambiental' => ['villa esperanza'],
            'ingenieria autotronica' => ['viacha','villa esperanza'],
            'ingenieria civil' => ['villa esperanza'],
            'ingenieria de gas y petroquimica' => ['palos blancos','villa esperanza'],
            'ingenieria de sistemas' => ['viacha','villa esperanza'],
            'ingenieria electrica' => ['villa esperanza'],
            'ingenieria electronica' => ['villa esperanza'],
            'ingenieria en produccion empresarial' => ['caranavi','san antonio','villa esperanza'],
            'ingenieria en zootecnia e industria pecuaria' => ['villa esperanza'],
            'ingenieria textil' => ['villa esperanza'],
            'linguistica e idiomas' => ['achacachi','caranavi','villa esperanza'],
            'medicina' => ['villa esperanza'],
            'medicina veterinaria y zootecnia' => ['mapiri-charopampa','villa esperanza'],
            'nutricion y dietetica' => ['villa esperanza'],
            'odontologia' => ['villa esperanza'],
            'psicologia' => ['villa esperanza'],
            'psicomotricidad y deportes' => ['villa esperanza'],
            'sociologia' => ['villa esperanza'],
            'tecnologia en laboratorio dental' => ['villa esperanza'],
            'trabajo social' => ['villa esperanza'],
        ];

        foreach ($relaciones as $nombreCarrera => $sedes) {
            $carrera = Carrera::where('nombre', $nombreCarrera)->first();

            if ($carrera) {
                $sedeIds = Sede::whereIn('nombre', $sedes)->pluck('id')->toArray();
                echo "Carrera: $nombreCarrera\n";
                echo "Sedes encontradas: " . implode(', ', $sedeIds) . "\n";
                $carrera->sedes()->sync($sedeIds);
            } else {
                echo "Carrera NO encontrada: $nombreCarrera\n";
            }
        }

    }
}
