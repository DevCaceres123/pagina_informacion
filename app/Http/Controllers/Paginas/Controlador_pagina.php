<?php

namespace App\Http\Controllers\Paginas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Controlador_pagina extends Controller
{
    

    public function noticias($id)
    {
        
        return view('plantilla_web.paginas.noticias');
    }
    public function sedes($id)
    {
        
        return view('plantilla_web.paginas.sedes');
    }
    public function inicio()
    {
        return view('plantilla_web.paginas.inicio');
    }
}
