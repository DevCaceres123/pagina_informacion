<?php

namespace App\Http\Controllers\Paginas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Noticia;
use App\Models\ImgNoticia;
use App\Models\CategoriasNoticia;

class Controlador_pagina extends Controller
{
    public function noticias(Request $request)
    {
        $query = Noticia::with(['sede', 'categoria', 'imagenesNoticia' => function ($q) {
            $q->select(['imagen', 'noticia_id']);
        }])
        ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at')
        ->where('estado_noticia', 'activo');

        // Filtro por categoría si existe
        if ($request->filled('categoria')) {
            $query->where('categoria_id', decrypt($request->categoria));
        }

        // Filtro por búsqueda si se usa el input
        if ($request->filled('search')) {
            $query->where('titulo', 'like', "%{$request->search}%");
        }

        // Filtro si se busca por sede
        if ($request->filled('sede')) {
            $query->where('sede_id', 'like', decrypt($request->sede));
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $noticias = $query->orderBy('id', 'desc')->paginate(9)->withQueryString();
        $sedes = Sede::select('id', 'nombre')->where('estado', 'activo')->get();
        $noticiaDestacada = Noticia::with(['sede', 'categoria', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
        ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at')
        ->where('estado_destacado', 'activo')
        ->orderBy('id', 'desc')
        ->first();

        $categorias = CategoriasNoticia::where('estado', 'activo')->get();

        return view('plantilla_web.paginas.noticias', compact('noticias', 'noticiaDestacada', 'categorias', 'sedes'));
    }

    public function noticia($id)
    {
        $id = decrypt($id);
        $noticia = Noticia::with(['sede', 'categoria', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
        ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at')
        ->where('estado_noticia', 'activo')
        ->where('id', $id)
        ->first();



        $ultimasNoticias = Noticia::with(['sede', 'categoria', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
         ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at')
         ->where('estado_noticia', 'activo')
         ->orderBy('id', 'desc')
         ->limit(3)
         ->get();


        return view('plantilla_web.paginas.noticia', compact('noticia', 'ultimasNoticias'));
    }

    public function convocatorias()
    {
        return view('plantilla_web.paginas.convocatorias');
    }

    public function convocatoria()
    {
        return view('plantilla_web.paginas.convocatoria');
    }
    public function sedes($id)
    {
        $id = decrypt($id);
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

    public function buscarCarrera(string $nombreCarrera)
    {
        return $nombreCarrera;
    }
}
