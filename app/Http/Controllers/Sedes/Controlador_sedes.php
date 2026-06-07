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
     * 🔎 EN PANTALLA: pantalla principal del módulo "Sedes" (la tabla de sedes)
     * VISTA PRINCIPAL DE SEDES (la pantalla del listado).
     * Solo muestra la página HTML. Valida que el usuario tenga el permiso
     * 'sede.inicio'; si no lo tiene, lo devuelve a la página de inicio.
     * Los datos de la tabla NO se cargan aquí, se cargan por AJAX con listarSedes().
     */
    public function index()
    {
        if (!auth()->user()->can('sede.inicio')) {
            return redirect()->route('inicio');
        }
        return view('administrador.sedes.sedes');
    }

    /**
     * 🔎 EN PANTALLA: arma las filas y los botones de la tabla de sedes ("Ver Resolucion", "Editar Sede", "Actualizar Imagenes", "Agregar Rutas", "Eliminar Sede" y el switch de estado)
     * DATOS DE LA TABLA DE SEDES (responde en formato JSON para DataTables).
     * Aquí se arma cada fila del listado: nombre, descripción, resolución, etc.
     * También se envían los PERMISOS del usuario ('editar', 'eliminar',
     * 'ver_resolucion', etc.). Esos permisos son los que el JavaScript usa para
     * decidir qué BOTONES mostrar en cada fila (por ejemplo, el botón
     * "Ver resolución" aparece solo si 'ver_resolucion' es true).
     * Soporta búsqueda por nombre o número de resolución y paginación.
     */
    public function listarSedes(Request $request)
    {
        $query = Sede::with([
            'carreras' => function ($query) {
                $query->select(['nombre', 'sede_id']); // CORREGIDO
            },
        ])->select('id', 'nombre', 'descripcion', 'resolucion', 'resolucion_pdf', 'estado','publicar_resolucion')->orderBy('id', 'desc');

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


    /**
     * 🔎 EN PANTALLA: botón "Actualizar Imagenes"  ->  modal "Galería de Imágenes" (al abrirlo, muestra las fotos ya guardadas)
     * GALERÍA: devuelve (en JSON) todas las imágenes de una sede.
     * Se usa cuando se abre el modal/sección de imágenes de una sede para
     * mostrar las fotos ya guardadas. Si no hay imágenes, avisa con un mensaje.
     */
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

    /**
     * 🔎 EN PANTALLA: modal "Galería de Imágenes"  ->  botón "Subir Imágenes"
     * GALERÍA: agrega NUEVAS imágenes a una sede ya existente.
     * Se ejecuta cuando, dentro del módulo de imágenes de una sede, se suben
     * fotos nuevas. Procesa/optimiza las imágenes (ver guardarGaleria) y guarda
     * cada una en la base de datos. Usa transacción: si algo falla, borra los
     * archivos que ya se habían guardado para no dejar basura.
     */
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


    /**
     * 🔎 EN PANTALLA: modal "Galería de Imágenes"  ->  botón rojo (ícono) de borrar sobre cada foto
     * GALERÍA: elimina UNA imagen de la sede.
     * Se ejecuta al presionar el botón de borrar sobre una foto. Borra tanto el
     * archivo físico del almacenamiento como el registro de la base de datos.
     */
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
     * 🔎 EN PANTALLA: botón "Nuevo" -> modal "CREAR NUEVA SEDE" -> botón "Guardar"
     * CREAR / REGISTRAR una nueva sede.
     * Se ejecuta al guardar el formulario de "Nueva sede". Guarda los datos de
     * la sede (nombre, descripción, número de resolución, redes sociales), el
     * PDF de la resolución (ver guardarPdf) y la galería de imágenes (ver
     * guardarGaleria). Usa transacción: si algo falla, borra los archivos ya
     * subidos para no dejar archivos sueltos.
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
            $sede->publicar_resolucion = 'activo';
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

    /**
     * 🔎 EN PANTALLA: no tiene botón propio (se usa por dentro al CREAR NUEVA SEDE)
     * AYUDANTE (no es una acción de botón por sí sola).
     * Guarda el PDF de la resolución en storage/app/public/resoluciones y
     * devuelve solo el NOMBRE del archivo (sin la carpeta) para guardarlo en BD.
     * Lo usan store() (al crear la sede) como parte del proceso de guardado.
     */
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

    /**
     * 🔎 EN PANTALLA: no tiene botón propio (se usa por dentro al CREAR NUEVA SEDE y al "Actualizar Imagenes")
     * AYUDANTE (no es una acción de botón por sí sola).
     * Procesa y optimiza las imágenes recibidas: las redimensiona (máx 1200x800
     * manteniendo proporción), las convierte a formato WEBP con calidad 80, les
     * pone un nombre único y las guarda en storage/app/public/galeria_sedes.
     * Devuelve la lista de nombres de archivo para guardarlos en BD.
     * Lo usan store() y agregarImagenes().
     */
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

                    $img->resize(
                        1200,  // ancho, null para mantener proporción
                        800,   // alto máximo
                        function ($constraint) {
                            $constraint->aspectRatio(); // mantiene proporción
                            $constraint->upsize();      // evita agrandar imágenes pequeñas
                        }
                    );


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

    /**
     * 🔎 EN PANTALLA: botón "Ver Resolucion"  ->  modal "Visualización de Resolución"  ->  botón "Actualizar"
     * ★ RESOLUCIÓN: este es el método del botón "VER / MODIFICAR RESOLUCIÓN".
     * Aquí es donde se trabaja con la resolución de la sede. Permite dos cosas:
     *   1) Reemplazar el PDF de la resolución (si se sube un 'nuevoPdf'): borra
     *      el PDF anterior y guarda el nuevo en storage/app/public/resoluciones.
     *   2) Definir si la resolución se PUBLICA o no en la web pública, según el
     *      campo 'publicar_resolucion' (1 = activo / 0 = inactivo).
     * Valida que el archivo sea PDF y máx 5MB. Usa transacción por seguridad.
     * NOTA: la PARTE DE "VER" el PDF (mostrarlo en pantalla) se apoya en el dato
     * 'resolucion_pdf' que devuelve listarSedes(); aquí se hace la MODIFICACIÓN.
     */
    public function actualizar_pdf(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'nuevoPdf' => 'nullable|file|mimes:pdf|max:5120', // 5MB
                'publicar_resolucion' => 'required|in:0,1',
            ]);
            $sede = Sede::findOrFail($id);

            if ($request->hasFile('nuevoPdf')) {
                // Opcional: eliminar archivo anterior si deseas
                if ($sede->resolucion_pdf) {
                    Storage::disk('public')->delete('resoluciones/' . $sede->resolucion_pdf);
                }

                $archivo = $request->file('nuevoPdf');
                $ruta = $archivo->store('resoluciones', 'public');
                $sede->resolucion_pdf = str_replace('resoluciones/', '', $ruta); //devuelve solo el nombre del archivo
                
            }

            $sede->publicar_resolucion = $request->publicar_resolucion == 1 ? 'activo' : 'inactivo';
            $sede->save();

            DB::commit();

            $this->mensaje('exito', 'Resolucion actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->mensaje('error', 'Error ' . $e->getMessage());
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
     * 🔎 EN PANTALLA: botón "Editar Sede" (abre el modal "EDITAR SEDE" y llena los campos)
     * EDITAR (cargar datos): devuelve en JSON los datos de UNA sede.
     * Se ejecuta al presionar el botón "Editar" de una fila, para llenar el
     * formulario de edición con la información actual de esa sede.
     * (Aquí solo se traen los datos; el guardado lo hace actualizarDatos()).
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


    /**
     * 🔎 EN PANTALLA: modal "EDITAR SEDE" -> botón "Guardar"
     * EDITAR (guardar cambios): actualiza los datos de una sede existente.
     * Se ejecuta al guardar el formulario de edición. Modifica nombre,
     * descripción, número de resolución y redes sociales (whatsapp, facebook,
     * youtube). Usa transacción para revertir si ocurre un error.
     * OJO: aquí NO se cambia el PDF de la resolución (eso lo hace actualizar_pdf).
     */
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
     * 🔎 EN PANTALLA: el switch (interruptor) verde/gris de estado en cada fila de la tabla
     * ACTIVAR / DESACTIVAR sede (cambiar estado).
     * Se ejecuta al presionar el botón/switch de estado de una fila. Si la sede
     * está "activo" la pasa a "inactivo" y viceversa (es un interruptor).
     * Requiere el permiso 'sede.desactivar' que se manda en listarSedes().
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
     * 🔎 EN PANTALLA: botón "Eliminar Sede"
     * ELIMINAR sede.
     * Se ejecuta al presionar el botón "Eliminar" de una fila. Borra la sede
     * (borrado lógico / SoftDeletes, no se borra realmente de la base de datos).
     * Requiere el permiso 'sede.eliminar' que se manda en listarSedes().
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


    /**
     * 🔎 EN PANTALLA: botón "Agregar Rutas" (abre la pantalla del mapa de esa sede)
     * MAPA DE UNA SEDE: muestra la pantalla del mapa de una sede específica.
     * Trae los polígonos (áreas dibujadas) y los puntos de salida guardados de
     * esa sede, los convierte a GeoJSON para que Leaflet los pueda dibujar, y
     * los manda a la vista del mapa. Solo lista lo que NO está eliminado.
     */
    
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



    /**
     * 🔎 EN PANTALLA: dentro del mapa, botón "Agregar ubicación" (dibujar) y luego botón "Guardar ubicación"
     * MAPA: guarda las ubicaciones dibujadas en el mapa.
     * Se ejecuta al guardar lo dibujado en el mapa (recibe un GeoJSON). Recorre
     * cada figura y, según su tipo, la guarda en la tabla correcta:
     *   - Polygon / MultiPolygon  -> tabla ubicacion_sedes (áreas de la sede)
     *   - Point                   -> tabla puntos_salidas (puntos de salida)
     * Usa transacción para revertir todo si una figura falla.
     */
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


    /**
     * 🔎 EN PANTALLA: dentro del mapa, al BORRAR una figura (punto o área)
     * MAPA: elimina UNA ubicación dibujada (un punto o un polígono).
     * Se ejecuta al borrar una figura del mapa. Según el tipo de geometría
     * busca en la tabla correcta (Point -> puntos_salidas, Polygon -> ubicacion_sedes)
     * y la elimina. Usa transacción por seguridad.
     */
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

    /**
     * 🔎 EN PANTALLA: dentro del mapa, al EDITAR / MOVER una figura existente
     * MAPA: edita / mueve UNA ubicación ya dibujada (punto o polígono).
     * Se ejecuta al modificar una figura existente en el mapa. Según el tipo,
     * actualiza la geometría en la tabla correcta (puntos_salidas o ubicacion_sedes).
     * Usa transacción por seguridad.
     */
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

    /**
     * 🔎 EN PANTALLA: botón "Ver Ubicaciones" (mapa general con todas las sedes)
     * MAPA GENERAL: vista con las ubicaciones de TODAS las sedes juntas.
     * Muestra en un solo mapa todos los polígonos y puntos de todas las sedes
     * (con el nombre de la sede a la que pertenecen). Requiere el permiso
     * 'sede.agregar_rutas'. Solo trae lo que NO está eliminado.
     */
    // Vista con todas las ubicaciones (polígonos y puntos) de todas las sedes
    public function todasUbicaciones()
    {
        if (!auth()->user()->can('sede.agregar_rutas')) {
            return redirect()->route('inicio');
        }

        $sedes = Sede::select('id', 'nombre')->orderBy('nombre')->get();

        $poligonos = DB::table('ubicacion_sedes')
            ->join('sedes', 'ubicacion_sedes.sede_id', '=', 'sedes.id')
            ->selectRaw("
                ubicacion_sedes.id,
                ubicacion_sedes.ubicacion as nombre,
                sedes.id as sede_id,
                sedes.nombre as sede_nombre,
                'Poligono' as tipo,
                ST_AsGeoJSON(ubicacion_sedes.poligono)::json as geometry
            ")
            ->whereNull('ubicacion_sedes.deleted_at')
            ->whereNull('sedes.deleted_at')
            ->whereNotNull('ubicacion_sedes.poligono')
            ->get()
            ->map(function ($p) {
                $p->geometry = json_decode($p->geometry);
                return $p;
            });

        $puntos = DB::table('puntos_salidas')
            ->join('sedes', 'puntos_salidas.sede_id', '=', 'sedes.id')
            ->selectRaw("
                puntos_salidas.id,
                puntos_salidas.ubicacion as nombre,
                sedes.id as sede_id,
                sedes.nombre as sede_nombre,
                'Punto' as tipo,
                ST_AsGeoJSON(puntos_salidas.punto)::json as geometry
            ")
            ->whereNull('puntos_salidas.deleted_at')
            ->whereNull('sedes.deleted_at')
            ->whereNotNull('puntos_salidas.punto')
            ->get()
            ->map(function ($p) {
                $p->geometry = json_decode($p->geometry);
                return $p;
            });

        $ubicaciones = collect($poligonos)->merge($puntos)->values();

        return view('administrador.sedes.ubicaciones', compact('sedes', 'ubicaciones'));
    }

    /**
     * 🔎 EN PANTALLA: el filtro/selector de sede del mapa general (carga la lista por AJAX)
     * MAPA GENERAL (datos): lista en JSON las ubicaciones de todas las sedes.
     * Alimenta por AJAX la vista anterior (todasUbicaciones). Permite filtrar
     * por una sede específica si se envía 'sede_id'. Devuelve nombre, sede y
     * tipo (Polígono o Punto) de cada ubicación no eliminada.
     */
    // JSON: lista de ubicaciones con filtro opcional por sede (para AJAX)
    public function listarTodasUbicaciones(Request $request)
    {
        $poligonos = DB::table('ubicacion_sedes')
            ->join('sedes', 'ubicacion_sedes.sede_id', '=', 'sedes.id')
            ->select(
                'ubicacion_sedes.id',
                'ubicacion_sedes.ubicacion as nombre',
                'sedes.id as sede_id',
                'sedes.nombre as sede_nombre',
                DB::raw("'Poligono' as tipo")
            )
            ->whereNull('ubicacion_sedes.deleted_at')
            ->whereNull('sedes.deleted_at');

        $puntos = DB::table('puntos_salidas')
            ->join('sedes', 'puntos_salidas.sede_id', '=', 'sedes.id')
            ->select(
                'puntos_salidas.id',
                'puntos_salidas.ubicacion as nombre',
                'sedes.id as sede_id',
                'sedes.nombre as sede_nombre',
                DB::raw("'Punto' as tipo")
            )
            ->whereNull('puntos_salidas.deleted_at')
            ->whereNull('sedes.deleted_at');

        if ($request->filled('sede_id')) {
            $poligonos->where('ubicacion_sedes.sede_id', $request->sede_id);
            $puntos->where('puntos_salidas.sede_id', $request->sede_id);
        }

        $ubicaciones = collect($poligonos->get())->merge($puntos->get())->values();

        $this->mensaje('exito', $ubicaciones);
        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: no tiene botón (es la notificación/alerta de éxito o error que ves)
     * AYUDANTE: arma el mensaje (tipo + texto) que se devuelve al usuario.
     * Lo usan casi todos los métodos para responder con "exito" o "error" y un
     * texto, que luego el JavaScript muestra como alerta/notificación.
     */
    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
