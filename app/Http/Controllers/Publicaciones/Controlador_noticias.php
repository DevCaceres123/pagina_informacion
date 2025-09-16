<?php

namespace App\Http\Controllers\Publicaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Noticia;
use App\Models\ImgNoticia;
use App\Models\CategoriasNoticia;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Noticia\NoticiaRequest;
use Carbon\Carbon;
use Exception;

class Controlador_noticias extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        return view('administrador.publicaciones.noticias');
    }



    public function nuevaNoticia()
    {
        $sedes = Sede::where('estado', 'activo')->get();
        $tipos = CategoriasNoticia::where('estado', 'activo')->get();
        return view('administrador.publicaciones.nuevaNoticia',compact('sedes','tipos'));
    }

    public function editarNoticia($id_noticia)
    {
        $noticia = Noticia::select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id')->where('id',$id_noticia)->first();
        $sedes = Sede::where('estado', 'activo')->get();
        $tipos = CategoriasNoticia::where('estado', 'activo')->get();
        $imagenes = ImgNoticia::where('noticia_id',$id_noticia)->get();
        return view('administrador.publicaciones.nuevaNoticia',compact('sedes','tipos','noticia','imagenes'));
    }

    public function listarNoticias(Request $request)
    {
         $query = Noticia::select('id', 'titulo', 'estado_destacado', 'estado_noticia', 'created_at')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')
                  ->orWhere('estado_destacado', 'like', '%' . $request->search['value'] . '%')
                  ->orWhere('estado_noticia', 'like', '%' . $request->search['value'] . '%');
            });
        }

        // Total de registros antes del filtrado
        $recordsTotal = $query->count();

        // Paginación y orden
        $sedes = $query->skip($request->start)->take($request->length)->get();

        // Respuesta
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal, // Ajustar si hay filtros
            'data' => $sedes,
            'permisos' => [
                'editar' => auth()->user()->can('afiliado.editar'),
                'eliminar' => true,
                'estado' => auth()->user()->can('afiliado.estado'),
            ],
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoticiaRequest $request)
    {
        DB::beginTransaction();
        $archivosGuardados = [];


        try {
            // Guardar la sede
            $noticia = new Noticia();
            $noticia->titulo = $request->titulo;
            $noticia->contenido = $request->contenido;
            $noticia->user_id = auth()->user()->id;
            $noticia->estado_destacado = 'inactivo';
            $noticia->estado_noticia = 'activo';
            $noticia->url_video = $request->youtube_url ?? null;
            $noticia->sede_id = $request->sede;
            $noticia->categoria_id = $request->tipo;


            $noticia->save();

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgNoticia = new ImgNoticia();
                    $imgNoticia->imagen = $ruta; // ruta relativa
                    $imgNoticia->noticia_id = $noticia->id; // ID de la sede recién creada
                    $imgNoticia->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }

            DB::commit();

            $this->mensaje('exito', 'Noticia registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                // Aquí $ruta debería ser solo "imagenes_noticias/archivo.jpg"
                if (Storage::disk('public')->exists('imagenes_noticias/'.$ruta)) {
                    Storage::disk('public')->delete('imagenes_noticias/'.$ruta);
                }
            }

            $this->mensaje('error', 'Error ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }



    public function guardarGaleria(Request $request)
    {
        $rutas = [];

        // Inicializar el gestor de imágenes con el driver GD
        $manager = new ImageManager(new Driver());

        // Recorrer todos los archivos recibidos
        foreach ($request->allFiles() as $inputName => $files) {
            // Asegurar que sea un array para permitir múltiples archivos por input
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $imagen) {
                // Validar que sea imagen antes de procesar
                if (str_starts_with($imagen->getMimeType(), 'image/')) {
                    // Leer la imagen
                    $img = $manager->read($imagen->getPathname());

                    // Redimensionar manteniendo proporción
                    $img->resize(height: 800);

                    // Convertir a WEBP con calidad 80
                    $encoded = $img->toWebp(80);

                    // Generar nombre único
                    $nombre = $inputName.'_'.uniqid() . '.webp';

                    // Ruta de guardado
                    $ruta = storage_path("app/public/imagenes_noticias/{$nombre}");

                    // Guardar imagen procesada
                    $encoded->save($ruta);

                    // Guardar solo el nombre para BD
                    $rutas[] = $nombre;
                }
            }
        }

        return $rutas;
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    public function actualizarNoticia(Request $request, string $id_noticia)
    {
        DB::beginTransaction();
        $archivosGuardados = [];

        try {
            // Guardar la sede
            $noticia = Noticia::find($id_noticia);
            $noticia->titulo = $request->titulo;
            $noticia->contenido = $request->contenido;            
            $noticia->url_video = $request->youtube_url ?? null;
            $noticia->sede_id = $request->sede;            
            $noticia->categoria_id = $request->tipo;


            $noticia->save();

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgNoticia = new ImgNoticia();
                    $imgNoticia->imagen = $ruta; // ruta relativa
                    $imgNoticia->noticia_id = $noticia->id; // ID de la sede recién creada
                    $imgNoticia->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }                      
            DB::commit();

            $this->mensaje('exito', 'Noticia actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                // Aquí $ruta debería ser solo "imagenes_noticias/archivo.jpg"
                if (Storage::disk('public')->exists('imagenes_noticias/'.$ruta)) {
                    Storage::disk('public')->delete('imagenes_noticias/'.$ruta);
                }
            }

            $this->mensaje('error', 'Error ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $noticia = Noticia::find($id);
            if (!$noticia) {
                throw new Exception('Noticia no encontrado');
            }

            $noticia->delete();

            DB::commit();

            $this->mensaje("exito", "Noticia eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function cambiar_estado_destacado(Request $request, string $id_noticia)
    {
        DB::beginTransaction();
        try {

            $noticia = Noticia::find($id_noticia);
            if (!$noticia) {
                throw new Exception('noticia no encontrado');
            }
            if ($request->estado == "activo") {
                $noticia->estado_destacado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $noticia->estado_destacado = "activo";
            }

            $noticia->save();
            DB::commit();

            $this->mensaje("exito", "Estado cambiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function cambiar_estado_publicacion(Request $request, string $id_noticia)
    {
        DB::beginTransaction();
        try {

            $noticia = Noticia::find($id_noticia);
            if (!$noticia) {
                throw new Exception('noticia no encontrado');
            }
            if ($request->estado == "activo") {
                $noticia->estado_noticia = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $noticia->estado_noticia = "activo";
            }

            $noticia->save();
            DB::commit();

            $this->mensaje("exito", "Estado cambiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function eliminarImagenNoticia($id_imagen)
    {
        
        DB::beginTransaction();
        try {
            $imagen = ImgNoticia::find($id_imagen);
            if (!$imagen) {
                throw new Exception('Imagen no encontrada');
            }

            // Eliminar el archivo físico si existe
            if (Storage::disk('public')->exists('imagenes_noticias/'.$imagen->imagen)) {
                Storage::disk('public')->delete('imagenes_noticias/'.$imagen->imagen);
            }

            // Eliminar el registro de la base de datos
            $imagen->delete();

            DB::commit();

            $this->mensaje("exito", "Imagen eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
