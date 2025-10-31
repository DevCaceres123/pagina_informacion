<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Sede extends Model
{
    use SoftDeletes,HasFactory;
    //
    public function carreras()
    {
        return $this->belongsToMany('App\Models\Carrera','carrera_sede','sede_id', 'carrera_id');
    }
    public function ubicacionSedes()
    {
        return $this->hasMany('App\Models\UbicacionSedes');
    }
    public function administrativos()
    {
        return $this->hasMany('App\Models\Administrativo');
    }
    public function convocatorias()
    {
        return $this->hasMany('App\Models\Convocatoria');
    }

    public function imagenesSede(){
        return $this->hasMany('App\Models\ImgSede');
    }

    public function infraestructura(){
        return $this->hasOne('App\Models\ImgSede');
    }

    public function estadistica_estudiantes(){
        return $this->hasMany('App\Models\EstadisticaEstudiante');
    }



    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Campos que quieres en mayúsculas
            $campos = ['nombre', 'descripcion', 'resolucion'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }
}

