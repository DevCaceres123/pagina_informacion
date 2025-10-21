<?php

namespace App\Http\Controllers\Paginas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Carrera;
use App\Models\Noticia;
use App\Models\ImgNoticia;
use App\Models\CategoriasNoticia;
use Illuminate\Support\Facades\DB;

class Controlador_pagina extends Controller
{
    public function noticias(Request $request)
    {
        $query = Noticia::with([
        'sede' => function ($q) {
            $q->select('id', 'nombre'); // solo traigo id y nombre de la sede
        },
        'categoria' => function ($q) {
            $q->select('id', 'nombre'); // solo id y nombre de la categoría
        },
         'usuario' => function ($q) {
             $q->select('id', 'nombres', 'apellidos'); // solo traigo id y nombre de la sede
         },
        'imagenesNoticia' => function ($q) {
            $q->select(['imagen', 'noticia_id']);
        }

        ])->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'user_id', 'created_at')
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
        $noticiaDestacada = Noticia::with(['sede', 'categoria','usuario', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
        ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'user_id', 'created_at')
        ->where('estado_destacado', 'activo')
        ->orderBy('id', 'desc')
        ->first();

        $categorias = CategoriasNoticia::where('estado', 'activo')->get();

        return view('plantilla_web.paginas.noticias', compact('noticias', 'noticiaDestacada', 'categorias', 'sedes'));
    }

    public function noticia($id)
    {
        $id = decrypt($id);
        $noticia = Noticia::with(['sede', 'categoria','usuario', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
        ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at', 'user_id')
        ->where('estado_noticia', 'activo')
        ->where('id', $id)
        ->first();



        $ultimasNoticias = Noticia::with(['sede', 'categoria', 'imagenesNoticia' => function ($query) {
            $query->select(['imagen', 'noticia_id']);
        }])
         ->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'created_at')
         ->where('estado_noticia', 'activo')
         ->where('categoria_id', $noticia->categoria_id)
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
        ])->select('id', 'nombre', 'resolucion_pdf', 'whatsapp', 'youtobe', 'facebook', 'resolucion')
        ->where('id', $id)
        ->first();

        $carreras = Carrera::whereHas('sedes', function ($query) use ($id) {
            $query->where('sedes.id', $id);
        })->select('nombre', 'malla_curricular_pdf', 'vinculo_web')
        ->orderBy('nombre')
        ->take(6)
        ->get();


        $sedeUnica = Sede::findOrFail($id);

        // Total de carreras de la sede
        $totalCarreras = $sedeUnica->carreras()->count();

        $poligonos = DB::table('ubicacion_sedes')
             ->selectRaw("id, ubicacion, ST_AsGeoJSON(poligono)::json as geometry")
             ->whereNotNull('poligono')
             ->whereNull('deleted_at')  // listamos todos aquellos que no se hayan eliminado
             ->where('sede_id', $id)
             ->get()
             ->map(function ($p) {
                 $p->geometry = json_decode($p->geometry);
                 return $p;
             });

        $puntos = DB::table('puntos_salidas')
           ->selectRaw("puntos_salidas.id, puntos_salidas.ubicacion, ST_AsGeoJSON(punto)::json as geometry")
           ->leftJoin('ubicacion_sedes', 'puntos_salidas.sede_id', '=', 'ubicacion_sedes.id')
           ->where('puntos_salidas.sede_id', $id)
           ->whereNotNull('punto')
           ->whereNull('puntos_salidas.deleted_at') // listamos todos aquellos que no se hayan eliminado
           ->get()
           ->map(function ($p) {
               $p->geometry = json_decode($p->geometry);
               return $p;
           });


        return view('plantilla_web.paginas.sedes', compact('sedeUnica', 'carreras', 'poligonos', 'puntos', 'totalCarreras'));
    }
    public function inicio()
    {
        return view('plantilla_web.paginas.inicio');
    }

    public function buscarCarrera(Request $request)
    {
        $nombreCarrera = $request->input('q');
        $id_sede = decrypt($request->input('id_sede'));
        // Buscar carreras de esa sede que coincidan con el texto
        $carreras = Carrera::whereHas('sedes', function ($query) use ($id_sede) {
            $query->where('sedes.id', $id_sede);
        })
            ->select('nombre', 'malla_curricular_pdf', 'vinculo_web')
            ->where('nombre', 'like', "%{$nombreCarrera}%")
            ->orderBy('nombre')
            ->take(10)
            ->get();


        
    return response()->json([
        'tipo' => 'exito',
        'carreras' => $carreras
    ]);


        
    }
}
