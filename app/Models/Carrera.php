<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrera extends Model
{
    use SoftDeletes;
    public function sedes()
    {
        return $this->belongsToMany('App\Models\Sede', 'carrera_sede', 'carrera_id', 'sede_id');
    }
    public function estudiantes()
    {
        return $this->hasMany('App\Models\Estudiante');
    }
    public function estadistica_estudiantes(){
        return $this->hasMany('App\Models\EstadisticaEstudiante');
    }

    public function estadistica_titulados(){
        return $this->hasMany('App\Models\EstadisticaTitualado');
    }

    public function docentes()
    {
        return $this->hasMany('App\Models\Docente');
    }
    public function administrativos()
    {
        return $this->hasMany('App\Models\Administrativo');
    }


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Campos que quieres en mayúsculas
            $campos = ['nombre'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }

    public function estadisticas()
    {
        return $this->hasMany(EstadisticaEstudiante::class);
    }
}
