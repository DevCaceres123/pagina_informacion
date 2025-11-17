<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadisticaAdministrativo extends Model
{
    use HasFactory;
    protected $table = 'estadistica_administrativos';


    protected $fillable = [
        'sede_id',
        'nombre_completo',
        'n_documento',
        'genero',
        'cargo',
        'profesion',
        'servicio',
        'gestion',
        'estado'
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
