<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionSedes extends Model
{
    protected $table = 'ubicacion_sedes';
    //
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
    public function puntosSalida()
    {
        return $this->hasMany('App\Models\PuntosSalida');
    }
}
