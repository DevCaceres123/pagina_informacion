<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadisticaDocente extends Model
{
    protected $table = 'estadistica_docentes';

     protected $fillable = [
        'carrera_id','sede_id', 'gestion',
        'nombreCompleto', 'documentoIdentidad', 'genero', 'profesion', 'grado_academico',
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
