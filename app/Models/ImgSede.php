<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImgSede extends Model
{
    protected $table = 'img_sedes';
    //

    public function sede(){
        return $this->belongsTo('App\Models\Sede');
    }
}
