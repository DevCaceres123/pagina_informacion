<?php

namespace Database\Factories;

use App\Models\Noticia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Noticia>
 */
class NoticiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    protected $model = Noticia::class;


    public function definition(): array
    {
          return [
            'titulo' => $this->faker->sentence(6), // título corto
            'contenido' => $this->faker->paragraph(5), // texto más largo
            'estado_destacado' => 'inactivo',
            // 'estado_noticia' => $this->faker->randomElement(['activo', 'inactivo']), 
            'estado_noticia' => 'activo', 
            'user_id' => 1, //
            'sede_id' => $this->faker->randomElement(\App\Models\Sede::pluck('id')->toArray()), //seleccionar un id Generado
            'created_at' => $this->faker->dateTimeBetween('2025-01-01', 'now'),
            'categoria_id' => $this->faker->numberBetween(1, 4), // ej. categorías de 1 a 5
        ];
    }
}
