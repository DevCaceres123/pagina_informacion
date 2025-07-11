<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntosSalida extends Model
{
    protected $table = 'puntos_salida';
    //
    public function ubicacionSede()
    {
        return $this->belongsTo('App\Models\UbicacionSedes');
    }
}
