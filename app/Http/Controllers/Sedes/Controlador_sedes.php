<?php

namespace App\Http\Controllers\Sedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\ImgSede;
use App\Models\UbicacionSedes;
use App\Models\PuntosSalida;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Sede\SedesRquest;
use Exception;

class Controlador_sedes extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('sede.inicio')) {
            return redirect()->route('inicio');
        }
        return view('administrador.sedes.sedes');
    }

    public function listarSedes(Request $request)
    {
        $query = Sede::with([
            'carreras' => function ($query) {
                $query->select(['nombre', 'sede_id']); // CORREGIDO
            },
        ])->select('id', 'nombre', 'descripcion', 'resolucion', 'resolucion_pdf', 'estado')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')->orWhere('resolucion', 'like', '%' . $request->search['value'] . '%');
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
                'editar' => auth()->user()->can('sede.editar'),
                'eliminar' => auth()->user()->can('sede.eliminar'),
                'estado' => auth()->user()->can('sede.desactivar'),
                'ver_carreras' => auth()->user()->can('sede.ver_carreras'),
                'ver_resolucion' => auth()->user()->can('sede.ver_resolucion'),
                'actualizar_imagenes' => auth()->user()->can('sede.actualizar_imagenes'),
                'agregar_rutas' => auth()->user()->can('sede.agregar_rutas'),
            ],
        ]);
    }


    public function listarImagenes($id_sede)
    {


        $imagenes = ImgSede::select('id', 'imagen')
            ->where('sede_id', $id_sede)
            ->get();


        if ($imagenes->isEmpty()) {
            $this->mensaje('error', 'No hay imágenes para esta sede');
            return response()->json($this->mensaje, 200);
        }

        $this->mensaje('exito', $imagenes);
        return response()->json($this->mensaje, 200);
    }

    public function agregarImagenes(Request $request, string $id_sede)
    {
        DB::beginTransaction();

        try {

            $sede = Sede::find($id_sede);
            if (!$request->hasFile('nuevasImagenes')) {
                throw new Exception('No se han enviado imágenes.');
            }

            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgSede = new ImgSede();
                    // $imgSede->descripcion = $sede->nombre;
                    $imgSede->imagen = $ruta; // ruta relativa
                    $imgSede->sede_id = $id_sede;
                    $imgSede->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }

            DB::commit();

            $this->mensaje('exito', 'Imágenes subidas correctamente');
            return response()->json($this->mensaje, 200);

        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($archivosGuardados as $ruta) {
                if (Storage::exists($ruta)) {
                    Storage::delete($ruta);
                }
            }
            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }


    public function eliminarImagen(string $id_imagen)
    {


        DB::beginTransaction();
        try {
            $imagen = ImgSede::find($id_imagen);
            if (!$imagen) {
                throw new Exception('Imagen no encontrada');
            }
            // Eliminar la imagen del almacenamiento
            if (Storage::disk('public')->exists('galeria_sedes/' . $imagen->imagen)) {
                Storage::disk('public')->delete('galeria_sedes/' . $imagen->imagen);
            }
            // Eliminar el registro de la base de datos
            $imagen->delete();

            DB::commit();

            $this->mensaje('exito', 'Imagen eliminada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
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
    public function store(SedesRquest $request)
    {
        DB::beginTransaction();

        try {
            // Guardar la sede
            $sede = new Sede();
            $sede->nombre = $request->nombre;
            $sede->descripcion = $request->descripcion;
            $sede->resolucion = $request->resolucion_numero;
            // $sede->mapa_url = $request->mapa_url;
            $sede->whatsapp = $request->whatsapp;
            $sede->facebook = $request->facebook;
            $sede->youtobe = $request->youtube;
            $sede->estado = 'activo';
            $sede->usuario_id = auth()->user()->id;

            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request);
            if ($rutaPdf) {
                $sede->resolucion_pdf = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }

            $sede->save();

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgSede = new ImgSede();
                    // $imgSede->descripcion = $request->nombre;
                    $imgSede->imagen = $ruta; // ruta relativa
                    $imgSede->sede_id = $sede->id; // ID de la sede recién creada
                    $imgSede->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }

            DB::commit();

            $this->mensaje('exito', 'Sede registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                if (Storage::exists($ruta)) {
                    Storage::delete($ruta);
                }
            }

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // guardamos el pdf o resolucion
    public function guardarPdf(Request $request)
    {
        if ($request->hasFile('resolucion_archivo')) {
            $archivo = $request->file('resolucion_archivo');
            $ruta = $archivo->store('resoluciones', 'public'); // se guarda en storage/app/public/resoluciones
            return str_replace('resoluciones/', '', $ruta); // devuelve: resoluciones/archivo.pdf
        }
        return null;
    }

    // guardarmos las imagenes

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
                    $nombre = uniqid() . '.webp';

                    // Ruta de guardado
                    $ruta = storage_path("app/public/galeria_sedes/{$nombre}");

                    // Guardar imagen procesada
                    $encoded->save($ruta);

                    // Guardar solo el nombre para BD
                    $rutas[] = $nombre;
                }
            }
        }

        return $rutas;
    }

    public function actualizar_pdf(Request $request, $id)
    {

        $request->validate([
            'nuevoPdf' => 'required|file|mimes:pdf|max:3072', // 3MB
        ]);

        DB::beginTransaction();

        try {
            $sede = Sede::findOrFail($id);

            if ($request->hasFile('nuevoPdf')) {
                // Opcional: eliminar archivo anterior si deseas
                if ($sede->resolucion_pdf) {
                    Storage::disk('public')->delete('resoluciones/' . $sede->resolucion_pdf);
                }

                $archivo = $request->file('nuevoPdf');
                $ruta = $archivo->store('resoluciones', 'public');
                $sede->resolucion_pdf = str_replace('resoluciones/', '', $ruta); //devuelve solo el nombre del archivo
                $sede->save();
            }

            DB::commit();

            $this->mensaje('exito', 'Resolucion actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $sede = Sede::Find($id);
        if (!$sede) {
            $this->mensaje('error', 'Sede no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $sede);

        return response()->json($this->mensaje, 200);
    }


    public function actualizarDatos(SedesRquest $request)
    {
        
        DB::beginTransaction();
        try {
            // Encontrar el usuario por ID
            $sede = Sede::find($request->id_sede_edit);
            if (!$sede) {
                throw new Exception('Sede no encontrada');
            }

            // Actualizar los campos
            $sede->nombre = $request->nombre_edit;
            $sede->descripcion = $request->descripcion_edit;
            $sede->resolucion = $request->resolucion_numero_edit;
            $sede->whatsapp = $request->whatsapp_edit;
            $sede->facebook = $request->facebook_edit;
            $sede->youtobe = $request->youtube_edit;

            // Guardar la sede actualizada
            $sede->save();

            DB::commit();

            $this->mensaje("exito", "Datos actualizados correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $sede = Sede::findOrFail($request->id_afiliado);
            if (!$sede) {
                throw new Exception('Afiliado no encontrado');
            }
            if ($request->estado == "activo") {
                $sede->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $sede->estado = "activo";
            }


            $sede->save();
            DB::commit();

            $this->mensaje("exito", "Estado cambiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

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
            $sede = Sede::find($id);
            if (!$sede) {
                throw new Exception('Sede no encontrado');
            }

            $sede->delete();

            DB::commit();

            $this->mensaje("exito", "Sede eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    // listamos todas las ubicaciones de la sede
    public function ubicacionSede($id_sede)
    {
        $sede = Sede::find($id_sede);

        $poligonos = DB::table('ubicacion_sedes')
            ->selectRaw("id, ubicacion, ST_AsGeoJSON(poligono)::json as geometry")
            ->where('sede_id', $id_sede)
            ->whereNotNull('poligono')
            ->whereNull('deleted_at')  // listamos todos aquellos que no se hayan eliminado
            ->get()
            ->map(function ($p) {
                $p->geometry = json_decode($p->geometry);
                return $p;
            });

        $puntos = DB::table('puntos_salidas')
            ->selectRaw("puntos_salidas.id, puntos_salidas.ubicacion, ST_AsGeoJSON(punto)::json as geometry")
            ->leftJoin('ubicacion_sedes', 'puntos_salidas.sede_id', '=', 'ubicacion_sedes.id')   
            ->where('puntos_salidas.sede_id', $id_sede)
            ->whereNotNull('punto')
            ->whereNull('puntos_salidas.deleted_at') // listamos todos aquellos que no se hayan eliminado
            ->get()
            ->map(function ($p) {
                $p->geometry = json_decode($p->geometry);
                return $p;
            });


        return view('administrador.sedes.mapas', [
            'sede' => $sede,
            'poligonos' => $poligonos,
            'puntos' => $puntos
        ]);
    }



    public function guardarUbicaciones(Request $request)
    {
        $nombre = $request->input('nombre');
        $idSede = $request->input('idSede') ?? null;
        $geojson = json_decode($request->input('geojson'), true);

        if (!$geojson || !isset($geojson['features'])) {
            return response()->json([
                'tipo' => 'errores',
                'mensaje' => 'No se recibió un geojson válido.'
            ]);
        }

        try {
            DB::beginTransaction();

            foreach ($geojson['features'] as $feature) {
                $geometryType = $feature['geometry']['type'];
                $geometryJson = json_encode($feature['geometry']);
                $nombreFeature = $feature['properties']['nombre'] ?? $nombre;

                if ($geometryType === 'Polygon' || $geometryType === 'MultiPolygon') {
                    $ubicacion = new UbicacionSedes();
                    $ubicacion->ubicacion = $nombreFeature;
                    $ubicacion->sede_id = $idSede;
                    $ubicacion->poligono = DB::raw("ST_GeomFromGeoJSON('{$geometryJson}')");
                    $ubicacion->save();
                }

                if ($geometryType === 'Point') {
                    $puntoSalida = new PuntosSalida();
                    $puntoSalida->ubicacion = $nombreFeature;
                    $puntoSalida->sede_id = $idSede; // Cambio aquí
                    $puntoSalida->punto = DB::raw("ST_GeomFromGeoJSON('{$geometryJson}')");
                    $puntoSalida->save();
                }
            }
            DB::commit();

            $this->mensaje("exito", "Ubicacion Agregada Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }

    }


    public function eliminarUbicacion(String $id_ubicacion, Request $request)
    {

        $tipo = $request->input('geometry.type'); // ✅ obtener tipo correctamente

        DB::beginTransaction();
        try {
            if ($tipo === 'Point') {
                // Es un punto: buscar en puntos_salida
                $punto = PuntosSalida::find($id_ubicacion);
                if (!$punto) {
                    throw new Exception('Punto de salida no encontrado');
                }
                $punto->delete();
            } elseif ($tipo === 'Polygon' || $tipo === 'MultiPolygon') {
                // Es un polígono: buscar en ubicacion_sedes
                $ubicacion = UbicacionSedes::find($id_ubicacion);
                if (!$ubicacion) {
                    throw new Exception('Ubicación no encontrada');
                }
                $ubicacion->delete();
            } else {
                throw new Exception('Tipo de geometría no reconocido');
            }

            DB::commit();

            $this->mensaje('exito', 'Ubicación eliminada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // editar ubocacion
    public function actualizarUbicacion(String $id_ubicacion, Request $request)
    {
        DB::beginTransaction();
        try {
            $geometry = $request->input('geometry');
            $tipo = $geometry['type'] ?? null;
            $nombre = $request->input('nombre');


            if ($tipo !== 'Point' && $tipo != 'Polygon' && $tipo != 'MultiPolygon') {
                throw new Exception('Tipo de geometría no reconocido');
            }
            if ($tipo === 'Point') {
                // Es un punto: buscar en puntos_salida
                $punto = PuntosSalida::find($id_ubicacion);
                if (!$punto) {
                    throw new Exception('Punto de salida no encontrado');
                }
                $punto->punto = DB::raw("ST_GeomFromGeoJSON('" . json_encode($geometry) . "')");
                $punto->save();

            } elseif ($tipo === 'Polygon' || $tipo === 'MultiPolygon') {
                // Es un polígono: buscar en ubicacion_sedes
                $ubicacion = UbicacionSedes::find($id_ubicacion);
                if (!$ubicacion) {
                    throw new Exception('Ubicación no encontrada');
                }
                $ubicacion->poligono = DB::raw("ST_GeomFromGeoJSON('" . json_encode($geometry) . "')");
                $ubicacion->save();
            }

            DB::commit();

            $this->mensaje('exito', 'Ubicación actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
