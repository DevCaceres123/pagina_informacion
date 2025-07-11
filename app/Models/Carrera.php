<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
    public function estudiantes()
    {
        return $this->hasMany('App\Models\Estudiante');
    }
    public function docentes()
    {
        return $this->hasMany('App\Models\Docente');
    }
    public function administrativos()
    {
        return $this->hasMany('App\Models\Administrativo');
    }
}
