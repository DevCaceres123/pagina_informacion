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
];

    // Relación con el modelo Infraestructura
    public function infraestructura()
    {
        return $this->belongsTo(Infraestructura::class);
    }
}
