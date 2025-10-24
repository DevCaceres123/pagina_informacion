<?php

namespace App\Http\Controllers\Publicaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use App\Models\Sede;
use App\Models\ImgConvocatoria;
use App\Models\Convocatoria;
use App\Models\CategoriasNoticia; // tambien utilizada para las categorias de convocatorias
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Controlador_convocatorias extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('administrador.publicaciones.convocatorias');
    }

    public function listarConvocatorias(Request $request)
    {
        $query = Convocatoria::with([
            'categoria' => function ($query) {
                $query->select(['id','nombre']);
            },
        ])->select('id', 'titulo', 'estado', 'created_at', 'categoria_id')->orderBy('id', 'desc');


        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search['value'] . '%')
                  ->where('estado', 'like', '%' . $request->search['value'] . '%')
                  ->orWhereHas('categoria', function ($sedeQuery) use ($request) {
                      $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                  });
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

    public function nuevaConvocatoria()
    {
        $sedes = Sede::where('estado', 'activo')->get();
        $tipos = CategoriasNoticia::where('estado', 'activo')->get();
        return view('administrador.publicaciones.nuevaConvocatoria', compact('sedes', 'tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $archivosGuardados = [];

        try {
            // Guardar la sede
            $convocatoria = new Convocatoria();
            $convocatoria->titulo = $request->titulo;
            $convocatoria->descripcion = $request->contenido;
            $convocatoria->user_id = auth()->user()->id;
            $convocatoria->estado = 'activo';
            $convocatoria->sede_id = $request->sede;
            $convocatoria->categoria_id = $request->tipo;

            // guardar documento convocatoria
            $archivo = $request->file('convocatoria');
            $ruta = $archivo->store('convocatorias', 'public');

            $convocatoria->archivo = $ruta;
            $convocatoria->save();
            $archivosGuardados[] = $ruta;

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgNoticia = new ImgConvocatoria();
                    $imgNoticia->imagen = 'imagenes_convocatorias/'.$ruta; // ruta relativa
                    $imgNoticia->convocatoria_id = $convocatoria->id; // ID de la sede recién creada
                    $imgNoticia->save();

                    $archivosGuardados[] = 'imagenes_convocatorias/'.$ruta; // guardar para rollback
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
                if (Storage::disk('public')->exists($ruta)) {
                    Storage::disk('public')->delete($ruta);
                }
            }

            $this->mensaje('error', 'Error ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    public function editarConvocatoria($id_convocatoria)
    {
        $noticia = Convocatoria::select('id', 'titulo', 'descripcion', 'sede_id', 'categoria_id', 'archivo')->where('id', $id_convocatoria)->first();
        $sedes = Sede::where('estado', 'activo')->get();
        $tipos = CategoriasNoticia::where('estado', 'activo')->get();
        $imagenes = ImgConvocatoria::where('convocatoria_id', $id_convocatoria)->get();
        return view('administrador.publicaciones.nuevaConvocatoria', compact('sedes', 'tipos', 'noticia', 'imagenes'));
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    public function actualizarConvocatoria(Request $request, string $id_convocatoria)
    {
        DB::beginTransaction();
        $archivosGuardados = [];

        try {
            // Guardar la sede
            $convocatoria = Convocatoria::find($id_convocatoria);
            $convocatoria->titulo = $request->titulo;
            $convocatoria->descripcion = $request->contenido;
            $convocatoria->user_id = auth()->user()->id;
            $convocatoria->sede_id = $request->sede;
            $convocatoria->categoria_id = $request->tipo;

            if ($request->hasFile('convocatoria') && $request->file('convocatoria')->isValid()) {

                Storage::disk('public')->delete($convocatoria->archivo);
                $archivo = $request->file('convocatoria');
                $ruta = $archivo->store('convocatorias', 'public');
                $convocatoria->archivo = $ruta;
                $archivosGuardados[] = $ruta;
            }

            if ($request->hasFile('fotos')) {
                // Guardar la galería de imágenes si se envió
                $rutasGaleria = $this->guardarGaleria($request);
                if (!empty($rutasGaleria)) {
                    foreach ($rutasGaleria as $ruta) {
                        $imgNoticia = new ImgConvocatoria();
                        $imgNoticia->imagen = 'imagenes_convocatorias/'.$ruta; // ruta relativa
                        $imgNoticia->convocatoria_id = $convocatoria->id; // ID de la sede recién creada
                        $imgNoticia->save();

                        $archivosGuardados[] = 'imagenes_convocatorias/'.$ruta; // guardar para rollback
                    }
                }
            }



            $convocatoria->save();
            DB::commit();

            $this->mensaje('exito', 'Convocatoria actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {


                if (Storage::disk('public')->exists($ruta)) {
                    Storage::disk('public')->delete($ruta);
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

            $convocatoria = Convocatoria::find($id);

            if (!$convocatoria) {
                throw new Exception('convocatoria no encontrado');
            }
            $convocatoria->delete();
            DB::commit();

            $this->mensaje("exito", "Eliminado Correctamente");
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    public function eliminarImagenConvocatoria($id_imagen)
    {

        DB::beginTransaction();
        try {
            $imagen = ImgConvocatoria::find($id_imagen);
            if (!$imagen) {
                throw new Exception('Imagen no encontrada');
            }

            // Eliminar el archivo físico si existe
            if (Storage::disk('public')->exists($imagen->imagen)) {
                Storage::disk('public')->delete($imagen->imagen);
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

    public function guardarGaleria(Request $request)
    {

        // Verificar si se enviaron archivos
        // Verificar si efectivamente hay archivos
        if (empty($request->allFiles())) {
            return [];
        }
        $rutas = [];

        // Inicializar el gestor de imágenes con el driver GD
        $manager = new ImageManager(new Driver());

        // Recorrer todos los archivos recibidos
        foreach ($request->allFiles() as $inputName => $files) {
            // Asegurar que sea un array para permitir múltiples archivos por input
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $imagen) {
                // Validar que sea un UploadedFile válido
                if (!$imagen instanceof \Illuminate\Http\UploadedFile || !$imagen->isValid()) {
                    continue; // saltar archivos inválidos
                }

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
                    $ruta = storage_path("app/public/imagenes_convocatorias/{$nombre}");

                    // Guardar imagen procesada
                    $encoded->save($ruta);

                    // Guardar solo el nombre para BD
                    $rutas[] = $nombre;
                }
            }
        }

        return $rutas;
    }

    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
