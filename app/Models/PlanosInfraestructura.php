<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanosInfraestructura extends Model
{
    protected $table = 'planos_infraestructura';

    public function infraestructura()
    {
        return $this->belongsTo('App\Models\Infraestructura', 'infraestructura_id');
    }

}
