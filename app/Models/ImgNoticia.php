<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImgNoticia extends Model
{
    //


    public function noticia()
    {
        return $this->belongsTo('App\Models\Noticia');
    }
}
