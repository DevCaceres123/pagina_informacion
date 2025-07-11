<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    public function carrera()
    {
        return $this->belongsTo('App\Models\Carrera');
    }
}
