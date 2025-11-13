<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaEstudiante extends Model
{
    protected $fillable = [
        'carrera_id','sede_id', 'gestion',
        'hombres', 'mujeres', 'total'
    ];
    protected $table = 'estadisticas_estudiantes';


    public function carrera()
    {
        return $this->belongsTo('App\Models\Carrera');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    // // Calcula total automáticamente
    // protected static function booted()
    // {
    //     static::saving(function ($estadistica) {
    //         $estadistica->total = $estadistica->cantidad_hombres + $estadistica->cantidad_mujeres;
    //     });
    // }
}
