<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Infraestructura extends Model
{
    use SoftDeletes;
    public function sede(){
       return $this->belongsTo('App\Models\Sede');
    }

    public function planosInfraestructura()
    {
        return $this->hasMany('App\Models\PlanosInfraestructura', 'infraestructura_id');
    }

    public function datosInfraestructuras()
    {
        return $this->hasMany('App\Models\DatosInfraestructura', 'infraestructura_id');
    }
}
