<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PuntosSalida extends Model
{
    use SoftDeletes;
    protected $table = 'puntos_salidas';
    //
    public function ubicacionSede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Campos que quieres en mayúsculas
            $campos = ['ubicacion'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }
}
