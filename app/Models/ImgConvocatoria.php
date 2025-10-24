<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImgConvocatoria extends Model
{
    
    public function convocatoria()
    {
        return $this->belongsTo('App\Models\Convocatoria');
    }

}
