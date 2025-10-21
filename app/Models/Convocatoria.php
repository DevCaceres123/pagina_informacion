<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Convocatoria extends Model
{
    use SoftDeletes;
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriasNoticia', 'categoria_id');
    }
}
