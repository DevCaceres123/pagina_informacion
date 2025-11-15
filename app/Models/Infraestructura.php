<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Infraestructura extends Model
{
    use SoftDeletes;
    protected $appends = ['created_at_formateado','tiempo_tramite'];
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede');
    }

    public function planosInfraestructura()
    {
        return $this->hasMany('App\Models\PlanosInfraestructura', 'infraestructura_id');
    }

    public function datosInfraestructuras()
    {
        return $this->hasMany('App\Models\DatosInfraestructura', 'infraestructura_id');
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
            $campos = ['propiedad','uso_asignado','observacion_estado','numero_nota'];

            foreach ($campos as $campo) {
                if (!empty($model->$campo)) {
                    $model->$campo = strtolower($model->$campo);
                }
            }
        });
    }



    public function getTiempoTramiteAttribute()
    {
        if (!$this->created_at) {
            return null;
        }

        $inicio = $this->created_at instanceof Carbon
            ? $this->created_at->copy()->startOfDay()
            : Carbon::parse($this->created_at)->startOfDay();

        $hoy = Carbon::now()->startOfDay();

        // 👇 Asegúrate de que sea diffInDays, NO diffInRealDays
        $totalDias = $inicio->diffInDays($hoy);

        // Si quieres que el mismo día ya cuente como 1 día:
        if ($totalDias === 0) {
            $totalDias = 1;
        }

        $anios = intdiv($totalDias, 365);
        $diasRestantes = $totalDias % 365;

        if ($anios >= 3) {
            return '3 años (finalizado)';
        }

        if ($anios === 0) {
            return $totalDias . ' día' . ($totalDias === 1 ? '' : 's');
        }

        $textoAnios = $anios . ' año' . ($anios === 1 ? '' : 's');

        if ($diasRestantes === 0) {
            return $textoAnios;
        }

        $textoDias = $diasRestantes . ' día' . ($diasRestantes === 1 ? '' : 's');

        return $textoAnios . ' y ' . $textoDias;
    }

}
