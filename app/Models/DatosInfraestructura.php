<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosInfraestructura extends Model
{
    protected $table = 'datos_infraestructura';
    protected $fillable = [
    'infraestructura_id',
    'distrito',
    'ubicacion',
    'urb',
    'manzano',
    'lote',
    'sup_test',
    'sup_lev',
    'sup_adju',
    'sup_util',
    'escala',
];

    // Relación con el modelo Infraestructura
    public function infraestructura()
    {
        return $this->belongsTo(Infraestructura::class);
    }



    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Campos que quieres en mayúsculas
            $campos = ['distrito','ubicacion','urb'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }
}
