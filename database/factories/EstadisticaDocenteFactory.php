<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EstadisticaDocente>
 */
class EstadisticaDocenteFactory extends Factory
{
    // Mapa de carreras por sede
    private $carrerasPorSede = [
        1 => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37], // Villa Esperanza
        2 => [5,10,11,14,28], // Achacachi
        3 => [11], // Ancoraimes
        4 => [1,4,11,14,17,25,28], // Caranavi
        5 => [13], // Coroico - Cruz Loma
        6 => [5], // Guaqui
        7 => [5], // Batallas
        8 => [30], // Mapiri-Charopampa
        9 => [10,21], // Palos Blancos
        10 => [5], // Chaguaya
        11 => [1,5,10,11,14,19,22], // Viacha
        12 => [17], // Caranavi - San Pablo
        13 => [25], // San Antonio
    ];

    public function definition()
    {
        $faker = $this->faker;

        // Elegimos una sede válida
        $sedeId = $faker->randomElement(array_keys($this->carrerasPorSede));

        // Elegimos una carrera válida para esa sede
        $carreraId = $faker->randomElement($this->carrerasPorSede[$sedeId]);

        return [
            'nombreCompleto' => $faker->name(),
            'documentoIdentidad' => $faker->unique()->numerify('########'),
            'carrera_id' => $carreraId,
            'sede_id' => $sedeId,
            'genero' => $faker->randomElement(['masculino', 'femenino']),
            'gestion' => '2025', // solo año
            'profesion' => $faker->jobTitle(),
            'grado_academico' => $faker->randomElement([
                'licenciatura',
                'maestría',
                'doctorado',
                'técnico superior'
            ]),
            'estado' => 'activo', // SIEMPRE activo
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
