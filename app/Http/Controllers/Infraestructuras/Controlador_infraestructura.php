<?php

namespace App\Http\Controllers\Infraestructuras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Infraestructura;
use App\Models\PlanosInfraestructura;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Infraestructura\InfraestructuraRequest;
use Exception;

class Controlador_infraestructura extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = Sede::all();
        return view('administrador.infraestructura.infraestructura', compact('sedes'));
    }



    public function listarInfraestructuras(Request $request)
    {
        $query = Infraestructura::with([
            'sede' => function ($query) {
                $query->select(['id','nombre']); // CORREGIDO
            },
        ])->select('id', 'estado_inmueble', 'estado_tramite', 'sede_id')->orderBy('id', 'desc');


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
        //
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


    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
