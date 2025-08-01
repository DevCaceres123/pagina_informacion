<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sede extends Model
{
    use SoftDeletes;
    //
    public function carreras()
    {
        return $this->hasMany('App\Models\Carrera');
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
}

