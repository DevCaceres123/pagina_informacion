<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Carrera extends Model
{
    use SoftDeletes;
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
