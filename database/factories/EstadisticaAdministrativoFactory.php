<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EstadisticaAdministrativoFactory extends Factory
{
      

    public function definition()
    {
        $servicios = ['planta', 'contrato', 'linea'];
        $generos = ['masculino', 'femenino'];
        $estados = ['activo', 'inactivo'];

        return [
            'sede_id' => $this->faker->numberBetween(1, 13),
            'nombre_completo' => $this->faker->name,
            'n_documento' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'genero' => $this->faker->randomElement($generos),
            'cargo' => $this->faker->jobTitle,
            'profesion' => $this->faker->word,
            'servicio' => $this->faker->randomElement($servicios),
            'gestion' => '2025',
            'estado' => $this->faker->randomElement($estados),
        ];
    }
}
