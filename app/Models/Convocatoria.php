<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    //
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaNoticia', 'categoria_id');
    }
}
