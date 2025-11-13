<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EstadisticaTitulado extends Model
{
    use SoftDeletes,HasFactory;
    protected $fillable = [
        'carrera_id','sede_id', 'nombreCompleto',
        'documentoIdentidad','genero','fecha_colacion',
        'grado_academico',
       
    ];
    protected $table = 'estadistica_titulados';


    public function carrera()
    {
        return $this->belongsTo('App\Models\Carrera');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
}
