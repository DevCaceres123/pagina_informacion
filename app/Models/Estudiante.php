<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudiante extends Model
{
    use SoftDeletes;

    protected $table = 'estudiantes';

    protected $fillable = [
        'sede_id',
        'carrera_id',
        'nombre_completo',
        'matricula',
        'tipo_documento',
        'numero_documento',
        'gestion',
        'genero',
        'certificado_habilitacion',
        'copia_titulo_tec_medio',
        'copia_titulo_tec_superior',
        'copia_titulo_licenciatura',
        'aprobado_tec_medio',
        'aprobado_tec_superior',
        'aprobado_licenciatura',
        'observacion',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->nombre_completo) {
                $model->nombre_completo = mb_strtolower($model->nombre_completo, 'UTF-8');
            }
            if ($model->numero_documento) {
                $model->numero_documento = mb_strtolower($model->numero_documento, 'UTF-8');
            }
            if ($model->observacion) {
                $model->observacion = mb_strtolower($model->observacion, 'UTF-8');
            }
        });
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function formulariosInscripcion()
    {
        return $this->hasMany(FormularioInscripcion::class);
    }

    public function requisitosDefensa()
    {
        return $this->hasMany(RequisitoDefensa::class);
    }
}
