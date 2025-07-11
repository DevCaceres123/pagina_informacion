<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    //

    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }
}
