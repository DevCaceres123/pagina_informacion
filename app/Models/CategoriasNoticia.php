<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CategoriasNoticia extends Model
{
    //
    use SoftDeletes,HasFactory;
    protected $table = 'categorias_noticias';

    public function noticias()
    {
        return $this->hasMany('App\Models\Noticia', 'categoria_id');
    }
}
