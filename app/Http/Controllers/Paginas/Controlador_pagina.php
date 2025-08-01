<?php

namespace App\Http\Controllers\Paginas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;

class Controlador_pagina extends Controller
{
    public function noticias($id)
    {
        return view('plantilla_web.paginas.noticias');
    }
    public function sedes($id)
    {
        $sedeUnica = Sede::with([
            'imagenesSede' => function ($query) {
                $query->select(['id', 'imagen', 'sede_id']);
            },
        ])->select('id', 'nombre', 'resolucion_pdf', 'whatsapp', 'youtobe', 'facebook')
        ->where('id', $id)
        ->first();

        $sedes = Sede::select('id', 'nombre', 'resolucion_pdf', 'whatsapp', 'youtobe', 'facebook')
        ->orderBy('id', 'desc')
        ->get(); // No olvides terminar con get() si vas a ejecutar la consulta

        return view('plantilla_web.paginas.sedes', compact('sedeUnica', 'sedes'));
    }
    public function inicio()
    {
        return view('plantilla_web.paginas.inicio');
    }

    public function buscarCarrera(string $nombreCarrera){
        return $nombreCarrera;
    }
}
