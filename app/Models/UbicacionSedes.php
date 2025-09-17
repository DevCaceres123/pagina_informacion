<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UbicacionSedes extends Model
{
    use SoftDeletes;
    protected $table = 'ubicacion_sedes';
    //
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
    public function puntosSalida()
    {
        return $this->hasMany('App\Models\PuntosSalida');
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
