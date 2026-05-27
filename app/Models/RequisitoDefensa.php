<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitoDefensa extends Model
{
    use SoftDeletes;

    protected $table = 'requisitos_defensa';

    protected $fillable = [
        'estudiante_id',
        'tipo_titulo',
        'nombre',
        'archivo',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->nombre) {
                $model->nombre = mb_strtolower($model->nombre, 'UTF-8');
            }
        });
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
}
