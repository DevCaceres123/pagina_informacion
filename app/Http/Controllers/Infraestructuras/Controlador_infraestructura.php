<?php

namespace App\Http\Controllers\Infraestructuras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Infraestructura;
use App\Models\DatosInfraestructura;
use App\Models\PlanosInfraestructura;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Infraestructura\InfraestructuraRequest;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class Controlador_infraestructura extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = Sede::select('id', 'nombre')->where('estado', 'activo')->get();
        return view('administrador.infraestructura.infraestructura', compact('sedes'));
    }



    public function listarInfraestructuras(Request $request)
    {
        $query = Infraestructura::with([
            'sede' => function ($query) {
                $query->select(['id','nombre']); // CORREGIDO
            },
        ])->select('id', 'estado_inmueble', 'estado_tramite', 'observacion_estado', 'sede_id', 'created_at')->orderBy('id', 'desc');


        if (!empty($request->search['value'])) {
            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {
                // Buscar en relación 'sede'
                $q->whereHas('sede', function ($sub) use ($search) {
                    $sub->where('nombre', 'like', "%{$search}%");
                })
                // Buscar en estado_inmueble
                ->orWhere('estado_inmueble', 'like', "%{$search}%")
                // Buscar en estado_tramite
                ->orWhere('estado_tramite', 'like', "%{$search}%");
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InfraestructuraRequest $request)
    {

        DB::beginTransaction();

        try {
            // Guardar la sede
            $infraestructura = new Infraestructura();
            $infraestructura->propiedad = $request->propiedad;
            $infraestructura->uso_asignado = $request->uso_asignado;


            $infraestructura->estado_inmueble = $request->estado_inmueble;
            ;
            $infraestructura->estado_tramite = 'inicial';
            $infraestructura->observacion_estado = $request->observacion_estado;

            $infraestructura->sede_id = $request->sede_id;


            $infraestructura->usuario_id = auth()->user()->id;

            $archivosGuardados = [];
            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request, 'solicitud', 'S');
            if ($rutaPdf) {
                $infraestructura->solicitud = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }
            $infraestructura->save();

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $PlanosInfraestructura = new PlanosInfraestructura();
                    $PlanosInfraestructura->nombre = $ruta; // ruta relativa
                    $PlanosInfraestructura->infraestructura_id = $infraestructura->id; // ID de la sede recién creada
                    $PlanosInfraestructura->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }

            DB::commit();
            $this->mensaje('exito', 'Infraestructura registrada correctamente');
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

        $infraestructura = Infraestructura::select('id', 'propiedad', 'uso_asignado', 'estado_inmueble', 'estado_inmueble', 'sede_id', 'observacion_estado')->where('id', $id)->first();
        if (!$infraestructura) {
            $this->mensaje('error', 'Infraestructura no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $infraestructura);

        return response()->json($this->mensaje, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $infraestructura = Infraestructura::find($id);
            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }

            $infraestructura->delete();

            DB::commit();

            $this->mensaje("exito", "infraestructura eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function estadoTramite(string $idInfraestructura)
    {


        try {
            $infraestructura = Infraestructura::select('id', 'estado_tramite')->where('id', $idInfraestructura)->first();
            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }


            DB::commit();

            $this->mensaje("exito", $infraestructura);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {


            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function cambiarEstadoTramite(Request $request)
    {

        DB::beginTransaction();
        try {
            $infraestructura = Infraestructura::find($request->id);
            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }

            // Validar el estado
            if (!in_array($request->estado, ['inicial', 'proceso', 'finalizado'])) {
                throw new Exception('Estado inválido');
            }

            // Actualizar el estado
            $infraestructura->estado_tramite = $request->estado;
            $infraestructura->save();

            DB::commit();

            $this->mensaje("exito", "Estado de trámite actualizado correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function docuementosInfraestructura(string $idInfraestructura)
    {
        try {
            $infraestructura = Infraestructura::with('planosInfraestructura')
                ->where('id', $idInfraestructura)
                ->first();

            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }

            return view('administrador.infraestructura.docuementosInfraestructura', compact('infraestructura'));

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function guardarPdf(Request $request, string $nombre_campo, string $prefijo = '')
    {
        if ($request->hasFile($nombre_campo)) {
            $archivo = $request->file($nombre_campo);

            // extensión (normalmente pdf)
            $extension = $archivo->getClientOriginalExtension();

            // nombre único (similar al que genera Laravel con store)
            $nombreUnico = uniqid() . '.' . $extension;

            // agregamos el prefijo al inicio
            $nombreFinal = $prefijo . $nombreUnico;

            // guardamos con storeAs para controlar el nombre
            $ruta = $archivo->storeAs('documentos_infraestructura', $nombreFinal, 'private');

            return $ruta; // ej: documentos_infraestructura/S66bfa2c52d3e1.pdf
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

                    // Redimensionar (máx 1200px en cualquier lado)
                    $img->scaleDown(1200);


                    // Redimensionar manteniendo proporción
                    //$img->resize(height: 1200);

                    // Convertir a WEBP con calidad 80
                    $encoded = $img->toWebp(70);

                    // Generar nombre único
                    $nombre = uniqid() . '.webp';

                    // Carpeta dentro del disco private
                    $carpeta = 'planos';

                    // Ruta de guardado en el sistema de archivos
                    $ruta = storage_path("app/private/{$carpeta}/{$nombre}");

                    // Guardar imagen procesada
                    $encoded->save($ruta);

                    // Guardar solo el nombre para BD
                    $rutas[] = "{$carpeta}/{$nombre}";
                }
            }
        }

        return $rutas;
    }



    public function verDocumentos($tipo, $id)
    {


        // Obtener el registro
        $infraestructura = Infraestructura::findOrFail($id);


        // Determinar qué campo usar según el tipo
        switch ($tipo) {
            case 'solicitud':
                $path = $infraestructura->solicitud;
                break;
            case 'nota':
                $path = $infraestructura->nota;
                break;
            case 'contrato':
                $path = $infraestructura->contrato;
                break;
            default:
                abort(404, 'Documento no encontrado');
        }

        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

        $mime = Storage::disk('private')->mimeType($path);

        if (!in_array($mime, $allowedMimes)) {
            abort(403, 'Tipo de archivo no permitido');
        }

        return response()->file(
            Storage::disk('private')->path($path),
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.basename($path).'"'
            ]
        );
    }

    public function guardarDocumentos(Request $request)
    {

        $archivosGuardados = [];
        $tipo = $request->tipo;
        DB::beginTransaction();
        try {
            $infraestructura = Infraestructura::find($request->idInfraestructura);
            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }

            $nombreArchivo = $infraestructura->$tipo;

            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request, $request->tipo, strtoupper(substr($request->tipo, 0, 1)));
            if ($rutaPdf) {
                $infraestructura->$tipo = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }

            // Guardar número de nota si es tipo nota
            if ($request->tipo === 'nota') {
                $infraestructura->numero_nota = $request->numero_nota ?? null;
            }

            $infraestructura->save();

            DB::commit();
            // ELIMINAR EL ARCHIVO ANTERIOR
            if (Storage::exists($nombreArchivo)) {
                Storage::delete($nombreArchivo);
            }
            $this->mensaje('exito', 'Documentos actualizados correctamente');
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

    public function datosUbicacion(Request $request)
    {

        try {
            $datosinfraestructura = DatosInfraestructura::where('infraestructura_id', $request->infraestructura_id)->first() ?? 'null';

            DB::commit();

            $this->mensaje("exito", $datosinfraestructura);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function guardarDatosUbicacion(InfraestructuraRequest $request)
    {
        
        DB::beginTransaction();
        try {

            $datosinfraestructura = DatosInfraestructura::updateOrCreate(
                ['infraestructura_id' => $request->infraestructura_id],
                [
                    'distrito' => $request->distrito,
                    'ubicacion' => $request->ubicacion,
                    'urb' => $request->urb,
                    'manzano' => $request->manzano,
                    'lote' => $request->lote,
                    'sup_test' => $request->sup_test,
                    'sup_lev' => $request->sup_lev,
                    'sup_adju' => $request->sup_adju,
                    'sup_util' => $request->sup_util,
                    'escala' => $request->escala,

                ]
            );
            DB::commit();

            $this->mensaje("exito", "Datos de ubicación actualizados correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function listarImagenesPlanos($id_infraestructura)
    {
        $imagenes = PlanosInfraestructura::select('id', 'nombre')
            ->where('infraestructura_id', $id_infraestructura)
            ->get();

        if ($imagenes->isEmpty()) {
            return response()->json([
                'tipo' => 'error',
                'mensaje' => 'No hay planos registrados'
            ]);
        }

        $galeria = $imagenes->map(function ($img) {
            $path = $img->nombre;
            if (!Storage::disk('private')->exists($path)) {
                return null;
            }
            $mime = Storage::disk('private')->mimeType($path);
            return [
                'id' => $img->id,
                'nombre' => $img->nombre,
                'mime' => $mime,
                // URL protegida a un endpoint que sirve la imagen
                'url' => route('infraestructura.planosVer', $img->id),
            ];
        })->filter(); // quita los null

        return response()->json([
            'tipo' => 'exito',
            'mensaje' => $galeria
        ]);
    }


    // Endpoint para servir la imagen individual
    public function verPlano($id)
    {
        $plano = PlanosInfraestructura::findOrFail($id);
        $path = $plano->nombre;

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->file(
            storage_path("app/private/" . $path),
            ['Content-Type' => Storage::disk('private')->mimeType($path)]
        );
    }



    public function agregarImagenesPlanos(InfraestructuraRequest $request, string $id_infraestructura)
    {
        $archivosGuardados = [];

        DB::beginTransaction();

        try {

            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $infraestructura = new PlanosInfraestructura();
                    $infraestructura->nombre = $ruta; // ruta relativa
                    $infraestructura->infraestructura_id = $id_infraestructura;
                    $infraestructura->save();
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

    public function eliminarImagenPlano($id_imagen)
    {
        DB::beginTransaction();
        try {
            $imagen = PlanosInfraestructura::find($id_imagen);
            if (!$imagen) {
                throw new Exception('Imagen no encontrada');
            }

            // Eliminar el registro de la base de datos
            $imagen->delete();

            DB::commit();

            $this->mensaje("exito", "Imagen eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function actualizarInfraestructura(Request $request)
    {

        DB::beginTransaction();
        try {
            $infraestructura = Infraestructura::find($request->id);
            if (!$infraestructura) {
                throw new Exception('infraestructura no encontrado');
            }

            // Actualizar los campos
            $infraestructura->propiedad = $request->propiedadEdit;
            $infraestructura->uso_asignado = $request->uso_asignadoEdit;
            $infraestructura->estado_inmueble = $request->estado_inmuebleEdit;
            $infraestructura->observacion_estado = $request->observacion_estadoEdit;
            $infraestructura->sede_id = $request->sede_idEdit;

            $infraestructura->save();

            DB::commit();

            $this->mensaje("exito", "Infraestructura actualizada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function reporteInfraestructura($id_infraestructura)
    {
        try {
            //listar las imagenes de los planos
            $planos = PlanosInfraestructura::select('id', 'nombre')
                ->where('infraestructura_id', $id_infraestructura)
                ->get();
            
            $infraestructura= Infraestructura::select('estado_inmueble','estado_tramite','observacion_estado','created_at','sede_id','propiedad','uso_asignado')->where('id',$id_infraestructura)->first();

            $sede=Sede::select('nombre')->where('id',$infraestructura->sede_id)->first();

            $ubicacion = DatosInfraestructura::select('distrito','ubicacion','urb','manzano','lote','sup_test','sup_lev','sup_adju','sup_util')->where('infraestructura_id', $id_infraestructura)->first() ?? new \stdClass();

            // reducir calidad y convertir en base 64 una imagen
            $manager = new ImageManager(new Driver());

            foreach ($planos as $plano) {
                $path = storage_path('app/private/'.$plano->nombre);
                if (file_exists($path)) {

                    $imagen = $manager->read($path);
                    // Escalar (máximo 1200px de ancho o alto, mantiene proporción)
                    $img = $imagen->scaleDown(1200);

                    // Convertir a WebP y calidad 80 (ajustable: 0 = más liviano, 100 = más pesado)
                    $webp = $imagen->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 70));

                   $plano->base64 = 'data:image/webp;base64,' . base64_encode($webp);
                } else {
                    $plano->base64 = null;
                }
            }
                  

            $pdf = \PDF::loadView('administrador.infraestructura.reporteInfraestructura', compact('planos','infraestructura','sede','ubicacion'));

            $pdfContent = $pdf->output();
            
            $pdfb64=base64_encode($pdfContent);   
            $this->mensaje('exito',$pdfb64);
            return response()->json($this->mensaje, 200);    


        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

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
