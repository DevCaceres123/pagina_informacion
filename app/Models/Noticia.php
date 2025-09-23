<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Noticia extends Model
{
    //
    protected $appends = ['created_at_formateado'];
    use SoftDeletes,HasFactory;

    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    public function imagenesNoticia()
    {
        return $this->hasMany('App\Models\ImgNoticia');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriasNoticia', 'categoria_id');
    }
      //funcion para formatear la fecha en español
    public function getCreatedAtFormateadoAttribute()
    {
        if (empty($this->created_at)) {
            return 'N/A';
        }
    
        // Asegurar que $this->created_at sea un objeto Carbon
        $fecha = $this->created_at instanceof Carbon
            ? $this->created_at
            : Carbon::parse($this->created_at);
    
        return $fecha->locale('es')->translatedFormat('d \d\e F \d\e Y H:i');
    }


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Campos que quieres en mayúsculas
            $campos = ['titulo'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }
}
