<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaEstudiante extends Model
{
     protected $fillable = [
        'carrera_id', 'gestion',
        'cantidad_hombres', 'cantidad_mujeres', 'total'
    ];
    protected $table = 'estadisticas_estudiantes';


    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    // Calcula total automáticamente
    protected static function booted()
    {
        static::saving(function ($estadistica) {
            $estadistica->total = $estadistica->cantidad_hombres + $estadistica->cantidad_mujeres;
        });
    }
}
