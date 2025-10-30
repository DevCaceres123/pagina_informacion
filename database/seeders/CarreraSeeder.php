<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\Carrera;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'administracion de empresas', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'arquitectura', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'artes plasticas', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ciencias de la comunicacion social', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ciencias de la educacion', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ciencias del desarrollo', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ciencias fisicas y energias alternativas', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ciencias politicas', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'comercio internacional', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'contaduria publica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'derecho', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'economia', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'educacion parvularia', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'enfermeria', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'gestion turistica y hotelera', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'historia', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria agronomica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria ambiental', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria autotronica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria civil', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria de gas y petroquimica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria de sistemas', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria electrica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria electronica', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria en produccion empresarial', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria en zootecnia e industria pecuaria', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'ingenieria textil', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'linguistica e idiomas', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'medicina', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'medicina veterinaria y zootecnia', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'nutricion y dietetica', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'odontologia', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'psicologia', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'psicomotricidad y deportes', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'sociologia', 'modalidad' => 'semestral', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'tecnologia en laboratorio dental', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
            ['nombre' => 'trabajo social', 'modalidad' => 'anual', 'estado' => 'activo', 'usuario_id' => 1],
        ];

        foreach ($carreras as $data) {
            $carrera = new Carrera();
            $carrera->nombre = $data['nombre'];
            $carrera->modalidad = $data['modalidad']; // corregido
            $carrera->estado = $data['estado'];
            $carrera->usuario_id = $data['usuario_id'];
            $carrera->save();
        }
    }
}
