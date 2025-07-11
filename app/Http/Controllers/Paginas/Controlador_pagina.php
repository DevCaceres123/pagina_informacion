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
        
        $sedeUnica= Sede::find($id);

        $sedes = Sede::all();
        return view('plantilla_web.paginas.sedes',compact('sedeUnica', 'sedes'));
    }
    public function inicio()
    {
        return view('plantilla_web.paginas.inicio');
    }
}
