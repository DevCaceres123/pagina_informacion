<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FormularioInscripcion extends Model
{
    use SoftDeletes;

    protected $table = 'formularios_inscripcion';
    protected $appends = ['fecha_de_recepcion_formateada',];

    protected $fillable = [
        'estudiante_id',
        'archivo',
        'fecha_recepcion',
    ];

    protected $casts = [
        'fecha_recepcion' => 'date',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

   public function getFechaDeRecepcionFormateadaAttribute()
{
    return $this->fecha_recepcion
        ? $this->fecha_recepcion
            ->locale('es')
            ->translatedFormat('d \d\e F \d\e Y')
        : 'N/A';
}
}
