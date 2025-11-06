<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaAdministrativo extends Model
{
    protected $table = 'estadistica_administrativos';

    protected $fillable = [
        'carrera_id','sede_id', 'gestion',
        'hombres', 'mujeres', 'total'
    ];

    public function carrera()
    {
        return $this->belongsTo('App\Models\Carrera');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
}
