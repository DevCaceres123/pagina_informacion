<?php

namespace App\Http\Controllers\Carreras;

use App\Models\Sede;
use App\Models\Carrera;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Carrera\CarrerasRequest;

class Controlador_carrera extends Controller
{
    /**
     * 🔎 EN PANTALLA: pantalla principal del módulo "Carreras" (la tabla de carreras)
     * VISTA PRINCIPAL DE CARRERAS (la pantalla del listado).
     * Muestra la página HTML. Valida el permiso 'carrera.inicio'; si no lo tiene,
     * devuelve al inicio. Además trae las sedes ACTIVAS para llenar la lista
     * desplegable de sedes del formulario de "Nuevo".
     * Los datos de la tabla NO se cargan aquí, se cargan por AJAX con listarCarreras().
     */
    public function index()
    {
        if (!auth()->user()->can('carrera.inicio')) {
            return redirect()->route('inicio');
        }

        $sedes = Sede::select('id', 'nombre')->where('estado', 'activo')->get();
        return view('administrador.carreras.carreras', compact('sedes'));
    }


    /**
     * 🔎 EN PANTALLA: arma las filas y los botones de la tabla de carreras ("Sedes", "Editar carrera", "Ver Malla curricular", "Eliminar carrera" y el switch de estado)
     * DATOS DE LA TABLA DE CARRERAS (responde en formato JSON para DataTables).
     * Aquí se arma cada fila del listado: nombre, modalidad, estado, etc.
     * También envía los PERMISOS del usuario ('editar', 'eliminar', 'ver_sedes',
     * 'ver_malla', etc.); el JavaScript los usa para decidir qué BOTONES mostrar
     * en cada fila. Soporta búsqueda por nombre o modalidad y paginación.
     */
    public function listarCarreras(Request $request)
    {
        $query = Carrera::select('id', 'nombre', 'modalidad', 'estado', 'malla_curricular_pdf', 'vinculo_web')->orderBy('id', 'desc');


        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')->orWhere('modalidad', 'like', '%' . $request->search['value'] . '%');
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
                'editar' => auth()->user()->can('carrera.editar'),
                'eliminar' =>  auth()->user()->can('carrera.eliminar'),
                'estado' => auth()->user()->can('carrera.desactivar'),
                'ver_sedes' => auth()->user()->can('carrera.ver_sedes'),
                'ver_malla' => auth()->user()->can('carrera.ver_malla'),
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
     * 🔎 EN PANTALLA: botón "Nuevo" -> modal "CREAR NUEVA CARRERA" -> botón "Guardar Carrera"
     * CREAR / REGISTRAR una nueva carrera.
     * Guarda los datos de la carrera (nombre, modalidad, vínculo web), el PDF de
     * la malla curricular (ver guardarPdf) y le ASIGNA las sedes seleccionadas
     * (relación carrera-sedes). Usa transacción: si algo falla, borra el PDF que
     * ya se había subido para no dejar archivos sueltos.
     */
    public function store(CarrerasRequest $request)
    {

        DB::beginTransaction();

        try {
            // Guardar la sede
            $carrera = new Carrera();
            $carrera->nombre = $request->nombre;
            $carrera->modalidad = $request->modalidad;
            $carrera->estado = 'activo';
            $carrera->usuario_id = auth()->user()->id;
            $carrera->vinculo_web = $request->vinculo_web;
            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request);
            if ($rutaPdf) {
                $carrera->malla_curricular_pdf = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }


            $carrera->save();

            // asiganar carreras a sedes
            if ($request->has('sede_id') && is_array($request->sede_id)) {
                $carrera->sedes()->attach($request->sede_id);
            }

            DB::commit();

            $this->mensaje('exito', 'Carrera registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                if (Storage::disk('public')->exists('mallas_curriculares/'. $ruta)) {
                    Storage::disk('public')->delete('mallas_curriculares/'. $ruta);
                }
            }

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: el switch (interruptor) verde/gris de estado en cada fila de la tabla
     * ACTIVAR / DESACTIVAR carrera (cambiar estado).
     * Si la carrera está "activo" la pasa a "inactivo" y viceversa (interruptor).
     * Requiere el permiso 'carrera.desactivar' que se manda en listarCarreras().
     */
    public function cambiarEstado(Request $request, string $id)
    {

        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $carrera = Carrera::find($id);
            if (!$carrera) {
                throw new Exception('Afiliado no encontrado');
            }
            if ($request->estado == "activo") {
                $carrera->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $carrera->estado = "activo";
            }

            $carrera->save();
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * 🔎 EN PANTALLA: botón "Editar carrera" (abre el modal "EDITAR CARRERA" y llena los campos, incluidas sus sedes)
     * EDITAR (cargar datos): devuelve en JSON los datos de UNA carrera y las
     * sedes que tiene asignadas, para llenar el formulario de edición.
     * (Aquí solo se traen los datos; el guardado lo hace update()).
     */
    public function edit(string $id)
    {
        $carrera = Carrera::with(['sedes' => function ($query) {
            $query->select(['sedes.id', 'nombre']); // Selecciona solo los campos necesarios
        }])->select('id', 'nombre', 'modalidad', 'estado', 'malla_curricular_pdf', 'vinculo_web')->where('id',$id)->first();

        if (!$carrera) {
            $this->mensaje('error', 'Sede no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $carrera);

        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: modal "EDITAR CARRERA" -> botón "Guardar Carrera"
     * EDITAR (guardar cambios): actualiza los datos de una carrera existente
     * (nombre, modalidad y vínculo web). Usa transacción para revertir si falla.
     * OJO: aquí NO se cambia el PDF de la malla (eso lo hace actualizar_malla).
     */
    public function update(Request $request, string $id_carrera)
    {
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $carrera = Carrera::find($id_carrera);
            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }

            $carrera->nombre = $request->nombre;
            $carrera->modalidad	= $request->modalidad;

            $carrera->vinculo_web = $request->vinculo_web;

            $carrera->save();
            DB::commit();

            $this->mensaje("exito", "Carrera editada Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: botón "Ver Malla curricular" -> modal "MALLA CURRICULAR" -> botón "Subir nuevo PDF"
     * ★ MALLA CURRICULAR: este es el método del botón "VER / MODIFICAR MALLA".
     * Permite reemplazar el PDF de la malla curricular de la carrera: guarda el
     * PDF nuevo en storage/app/public/mallas_curriculares y recién entonces borra
     * el PDF anterior. Usa transacción; si algo falla, borra el nuevo archivo.
     * NOTA: la parte de "VER" el PDF se apoya en el dato 'malla_curricular_pdf'
     * que devuelve listarCarreras(); aquí se hace la MODIFICACIÓN del archivo.
     */
    public function actualizar_malla(Request $request)
    {
        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($request->id); // más seguro
            $borrarArchivo = $carrera->malla_curricular_pdf;
            $nombreArchivo = null;

            if ($request->hasFile('malla_curricular')) {


                // Guardar nuevo archivo
                $archivo = $request->file('malla_curricular');
                $ruta = $archivo->store('mallas_curriculares', 'public');
                $nombreArchivo = basename($ruta);

                // Asignar nuevo nombre de archivo
                $carrera->malla_curricular_pdf = $nombreArchivo;
                $carrera->save();
            }

            DB::commit();

            // Eliminar archivo anterior si se guardo una nueva mmalla
            Storage::disk('public')->delete('mallas_curriculares/' . $borrarArchivo);

            $this->mensaje('exito', 'Malla Curricular actualizada correctamente');
            return response()->json($this->mensaje, 200);

        } catch (\Exception $e) {
            DB::rollBack();

            // // Si se subió un nuevo archivo, eliminarlo manualmente
            if ($nombreArchivo) {
                Storage::disk('public')->delete('mallas_curriculares/' . $nombreArchivo);
            }

            $this->mensaje('error', 'Error al actualizar la malla curricular: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: no tiene botón propio (se usa por dentro al crear una carrera)
     * AYUDANTE: guarda el PDF de la malla curricular en
     * storage/app/public/mallas_curriculares y devuelve solo el NOMBRE del
     * archivo (sin la carpeta) para guardarlo en BD. Lo usa store().
     */
    public function guardarPdf(Request $request)
    {
        if ($request->hasFile('malla_curricular')) {
            $archivo = $request->file('malla_curricular');
            $ruta = $archivo->store('mallas_curriculares', 'public'); // se guarda en storage/app/public/mallas_curriculares
            return str_replace('mallas_curriculares/', '', $ruta); // devuelve: resoluciones/archivo.pdf
        }
        return null;
    }



    /**
     * 🔎 EN PANTALLA: botón "Eliminar carrera"
     * ELIMINAR carrera (borrado lógico / SoftDeletes, no se borra de verdad).
     * Requiere el permiso 'carrera.eliminar' que se manda en listarCarreras().
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $carrera = Carrera::find($id);
            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }

            $carrera->delete();

            DB::commit();

            $this->mensaje("exito", "Carrera eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    /**
     * 🔎 EN PANTALLA: botón "Sedes" -> modal "Sedes Asignadas"
     * Devuelve (en JSON) TODAS las sedes, marcando con 'asignada' (true/false)
     * cuáles ya pertenecen a esta carrera. Sirve para mostrar en el modal la
     * lista de sedes con su check activado/desactivado según corresponda.
     */
    public function listarSedesCarrera($id_carrera)
    {
        try {
            $carrera = Carrera::with('sedes')->find($id_carrera);

            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }


            // Todas las sedes disponibles
            $todasSedes = Sede::all();

            // IDs de las sedes que tiene esta carrera
            $sedesCarreraIds = $carrera->sedes->pluck('id')->toArray();

            // Armamos una lista con un campo extra "asignada"
            $sedesConEstado = $todasSedes->map(function ($sede) use ($sedesCarreraIds) {
                return [
                    'id' => $sede->id,
                    'nombre' => $sede->nombre,
                    'asignada' => in_array($sede->id, $sedesCarreraIds)
                ];
            });


            $this->mensaje('exito', $sedesConEstado);
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            $this->mensaje('error', 'Error al obtener las sedes: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Sedes Asignadas" -> al MARCAR (activar) una sede
     * Asigna (vincula) una sede a la carrera.
     */
    public function asignarSede(Request $request, string $carreraId)
    {
        
        $carrera = Carrera::findOrFail($carreraId);
        $carrera->sedes()->attach($request->sede_id);
        return response()->json(['tipo' => 'exito', 'mensaje' => 'Sede asignada correctamente']);
    }

    /**
     * 🔎 EN PANTALLA: modal "Sedes Asignadas" -> al DESMARCAR (quitar) una sede
     * Quita (desvincula) una sede de la carrera.
     */
    public function quitarSede(Request $request, string $carreraId)
    {
        $carrera = Carrera::findOrFail($carreraId);
        $carrera->sedes()->detach($request->sede_id);
        return response()->json(['tipo' => 'exito', 'mensaje' => 'Sede quitada correctamente']);
    }

    /**
     * 🔎 EN PANTALLA: no tiene botón (es la notificación/alerta de éxito o error que ves)
     * AYUDANTE: arma el mensaje (tipo + texto) que se devuelve al usuario para
     * mostrarlo como alerta/notificación. Lo usan casi todos los métodos.
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
