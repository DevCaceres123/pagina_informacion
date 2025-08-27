<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PlanosInfraestructura extends Model
{
    use softDeletes;
    protected $table = 'planos_infraestructura';

    public function infraestructura()
    {
        return $this->belongsTo('App\Models\Infraestructura', 'infraestructura_id');
    }

}
