<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\FormularioInscripcion;
use App\Models\RequisitoDefensa;
use App\Models\Sede;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PreviewSeguimientoEstudianteImport;
use App\Imports\SeguimientoEstudianteImport;
use Exception;
use App\Pdf\ReporteGeneralEstudiantesPdf;
use App\Pdf\ReportePendientesEstudiantesPdf;
use App\Pdf\ReporteIndividualEstudiantePdf;

/**
 * ============================================================================
 *  CONTROLADOR: Seguimiento de Estudiantes (módulo Académico)
 * ============================================================================
 *  Maneja la pantalla "Seguimiento de Estudiantes" del panel de administración:
 *  el registro de estudiantes, sus documentos de titulación (certificado,
 *  copias de título, formularios de inscripción y requisitos de defensa),
 *  la aprobación de expedientes, los reportes en PDF y la importación por CSV.
 *
 *  GUÍA RÁPIDA (busca con Ctrl+F el texto del botón que ves en pantalla):
 *    "Nuevo"                  -> store()        (crear)  /  edit()+update() (editar)
 *    "Documentos"             -> listarDocumentos()
 *    "Subir" (doc principal)  -> subirDocumento()
 *    "Subir Formulario"       -> agregarFormulario()
 *    "Subir" (requisito)      -> agregarRequisitoDefensa()
 *    "Marcar Aprobado"        -> aprobarExpediente()
 *    "Ficha de Seguimiento PDF" -> reporteIndividual()
 *    "Reporte General"        -> reporteGeneral()
 *    "Pendientes"             -> reportePendientes()
 *    "Subir CSV"              -> previsualizarCSV() / importarCSV()
 *    "Eliminar"               -> destroy()
 * ============================================================================
 */
class Controlador_seguimientoEstudiantes extends Controller
{
    /**
     * 🔎 EN PANTALLA: carga la PÁGINA completa "Seguimiento de Estudiantes"
     * (la que abrís desde el menú del administrador).
     *
     * Prepara los datos que necesitan los formularios y filtros de la vista:
     * la lista de sedes y carreras activas, y un mapa de qué carreras pertenecen
     * a cada sede (relacionesSedeCarre), que el JS usa para filtrar las carreras
     * según la sede elegida en los modales de reportes.
     */
    public function index()
    {
        if (!auth()->user()->can('seguimiento_estudiantes.inicio')) {
            return redirect()->route('inicio');
        }

        $sedes    = Sede::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']);
        $carreras = Carrera::where('estado', 'activo')->orderBy('nombre')->get(['id', 'nombre']);

        $relacionesSedeCarre = DB::table('carrera_sede')
            ->select('sede_id', 'carrera_id')
            ->get()
            ->groupBy('sede_id')
            ->map(fn($items) => $items->pluck('carrera_id')->toArray())
            ->toArray();

        return view('administrador.academico.seguimiento', compact('sedes', 'carreras', 'relacionesSedeCarre'));
    }

    /**
     * 🔎 EN PANTALLA: NO tiene botón. Es la que LLENA LA TABLA de estudiantes
     * (la tabla principal que ves apenas entrás a la página).
     *
     * Responde en JSON al DataTable (carga por servidor): aplica el buscador
     * (nombre, matrícula o número de documento), pagina los resultados y además
     * devuelve los permisos del usuario (editar/eliminar/documentos/ficha) para
     * que el JS muestre o esconda los botones de cada fila.
     */
    public function listar(Request $request)
    {
        $query = Estudiante::with(['sede:id,nombre', 'carrera:id,nombre'])
            ->select('id', 'nombre_completo', 'matricula', 'tipo_documento', 'numero_documento', 'sede_id', 'carrera_id', 'gestion')
            ->orderBy('nombre_completo');

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%$search%")
                  ->orWhere('matricula', 'like', "%$search%")
                  ->orWhere('numero_documento', 'like', "%$search%");
            });
        }

        $recordsTotal = $query->count();
        $data = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw'            => $request->draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data'            => $data,
            'permisos'        => [
                'editar'     => auth()->user()->can('seguimiento_estudiantes.editar'),
                'eliminar'   => auth()->user()->can('seguimiento_estudiantes.eliminar'),
                'documentos' => auth()->user()->can('seguimiento_estudiantes.documentos'),
                'ficha'      => auth()->user()->can('seguimiento_estudiantes.ficha'),
            ],
        ]);
    }

    /**
     * 🔎 EN PANTALLA: botón "Nuevo" (arriba a la derecha) -> abre el modal
     * "Nuevo Estudiante" -> botón "Guardar".
     *
     * Registra un estudiante nuevo. Valida los datos (matrícula única, tipo de
     * documento, sede/carrera existentes, gestión y género) y lo crea dentro de
     * una transacción. Si algo falla, deshace todo y devuelve el error.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo'  => 'required|string|max:150',
            'matricula'        => 'required|string|unique:estudiantes,matricula',
            'tipo_documento'   => 'required|in:CI,Pasaporte,Otro',
            'numero_documento' => 'required|string|max:50',
            'sede_id'          => 'required|exists:sedes,id',
            'carrera_id'       => 'required|exists:carreras,id',
            'gestion'          => 'required|numeric|min:2000|max:2100',
            'genero'           => 'required|in:masculino,femenino',
        ]);

        DB::beginTransaction();
        try {
            Estudiante::create($request->only([
                'nombre_completo', 'matricula', 'tipo_documento',
                'numero_documento', 'sede_id', 'carrera_id', 'gestion', 'genero', 'observacion',
            ]));
            DB::commit();
            $this->mensaje('exito', 'Estudiante registrado correctamente.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: botón "Editar" (el lápiz azul de cada fila).
     *
     * NO guarda nada: solo BUSCA los datos del estudiante y los devuelve en JSON
     * para que el JS los cargue en el modal "Editar Estudiante". El guardado del
     * cambio lo hace update().
     */
    public function edit(string $id)
    {
        $estudiante = Estudiante::with(['sede:id,nombre', 'carrera:id,nombre'])->find($id);
        if (!$estudiante) {
            $this->mensaje('error', 'Estudiante no encontrado.');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje('exito', $estudiante);
        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: modal "Editar Estudiante" -> botón "Guardar".
     *
     * Guarda los cambios de un estudiante existente (es el mismo botón "Guardar"
     * del modal, pero el JS manda PUT cuando hay un id cargado). Valida igual que
     * store() pero permite mantener la propia matrícula del estudiante.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre_completo'  => 'required|string|max:255',
            'matricula'        => 'required|string|unique:estudiantes,matricula,' . $id,
            'tipo_documento'   => 'required|in:CI,Pasaporte,Otro',
            'numero_documento' => 'required|string|max:50',
            'sede_id'          => 'required|exists:sedes,id',
            'carrera_id'       => 'required|exists:carreras,id',
            'gestion'          => 'required|numeric|min:2000|max:2100',
            'genero'           => 'required|in:masculino,femenino',
        ]);

        DB::beginTransaction();
        try {
            $estudiante = Estudiante::findOrFail($id);
            $estudiante->fill($request->only([
                'nombre_completo', 'matricula', 'tipo_documento',
                'numero_documento', 'sede_id', 'carrera_id', 'gestion', 'genero', 'observacion',
            ]));
            $estudiante->save();
            DB::commit();
            $this->mensaje('exito', 'Estudiante actualizado correctamente.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: botón "Eliminar" (el tacho rojo de cada fila) ->
     * confirmación "¿Eliminar estudiante?".
     *
     * Borra el estudiante. Es borrado lógico (SoftDeletes): el registro queda
     * marcado como eliminado pero no desaparece de la base de datos.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Estudiante::findOrFail($id)->delete();
            DB::commit();
            $this->mensaje('exito', 'Estudiante eliminado correctamente.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // --- Documentos ---

    /**
     * ★ 🔎 EN PANTALLA: botón "Documentos" (la carpeta verde de cada fila) ->
     * abre el modal "Documentos del Estudiante".
     *
     * Es el método CLAVE del expediente: junta TODO lo del estudiante para
     * mostrarlo en ese modal:
     *   - documentos principales (certificado y copias de título),
     *   - formularios de inscripción,
     *   - requisitos de defensa agrupados por tipo de título
     *     (tec_medio / tec_superior / licenciatura),
     *   - y el estado de aprobación de cada expediente.
     */
    public function listarDocumentos(string $id)
    {
        $estudiante = Estudiante::with(['formulariosInscripcion', 'requisitosDefensa'])->findOrFail($id);

        $requisitosPorTipo = $estudiante->requisitosDefensa
            ->groupBy('tipo_titulo')
            ->map(fn($items) => $items->values())
            ->toArray();

        $this->mensaje('exito', [
            'estudiante'        => $estudiante,
            'formularios'       => $estudiante->formulariosInscripcion,
            'requisitos_por_tipo' => $requisitosPorTipo,
            'aprobaciones'      => [
                'tec_medio'    => (bool) $estudiante->aprobado_tec_medio,
                'tec_superior' => (bool) $estudiante->aprobado_tec_superior,
                'licenciatura' => (bool) $estudiante->aprobado_licenciatura,
            ],
        ]);
        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: modal "Documentos del Estudiante", pestaña "Documentos" ->
     * botón "Subir" que está al lado de cada documento (Certificado de
     * habilitación, Copia de título Téc. Medio / Téc. Superior / Licenciatura).
     *
     * Sube (o reemplaza) uno de los documentos PRINCIPALES del estudiante. El
     * archivo PDF se guarda en el disco PRIVADO (no es accesible por URL directa;
     * se ve mediante verDocumento()). Si ya había un archivo de ese tipo, lo borra
     * antes de guardar el nuevo. El campo de la base de datos que se actualiza es
     * el mismo "tipo" que llega (ej. copia_titulo_licenciatura).
     */
    public function subirDocumento(Request $request, string $id)
    {
        $request->validate([
            'tipo'    => 'required|in:certificado_habilitacion,copia_titulo_tec_medio,copia_titulo_tec_superior,copia_titulo_licenciatura',
            'archivo' => 'required|file|mimes:pdf|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $estudiante = Estudiante::findOrFail($id);
            $tipo = $request->tipo;

            if ($estudiante->$tipo) {
                Storage::disk('private')->delete("documentos_estudiantes/{$tipo}/{$estudiante->$tipo}");
            }

            $archivo = $request->file('archivo');
            $nombre  = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs("documentos_estudiantes/{$tipo}", $nombre, 'private');

            $estudiante->$tipo = $nombre;
            $estudiante->save();

            DB::commit();
            $this->mensaje('exito', 'Documento subido correctamente.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Documentos del Estudiante", pestaña "Formularios de
     * Inscripción" -> tarjeta "Agregar Formulario" -> botón "Subir Formulario".
     *
     * Agrega un formulario de inscripción (PDF + fecha de recepción). A diferencia
     * de los documentos principales, de estos puede haber VARIOS por estudiante,
     * por eso se guardan como registros aparte (FormularioInscripcion). El PDF va
     * al disco privado.
     */
    public function agregarFormulario(Request $request, string $id)
    {
        $request->validate([
            'archivo'         => 'required|file|mimes:pdf|max:5120',
            'fecha_recepcion' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $archivo = $request->file('archivo');
            $nombre  = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs('documentos_estudiantes/formularios', $nombre, 'private');

            FormularioInscripcion::create([
                'estudiante_id'   => $id,
                'archivo'         => $nombre,
                'fecha_recepcion' => $request->fecha_recepcion,
            ]);

            DB::commit();
            $this->mensaje('exito', 'Formulario agregado correctamente.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Documentos del Estudiante", pestaña "Formularios de
     * Inscripción" -> el botón del tacho (rojo) con title="Eliminar Formulario" en
     * la fila de cada formulario.
     *
     * Borra un formulario de inscripción: elimina primero el PDF del disco privado
     * y luego el registro de la base de datos.
     */
    public function eliminarFormulario(string $id)
    {
        DB::beginTransaction();
        try {
            $formulario = FormularioInscripcion::findOrFail($id);
            Storage::disk('private')->delete("documentos_estudiantes/formularios/{$formulario->archivo}");
            $formulario->delete();
            DB::commit();
            $this->mensaje('exito', 'Formulario eliminado.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Documentos del Estudiante", pestaña "Expediente de
     * Titulación" -> dentro de la sub-pestaña de cada título (Téc. Medio /
     * Téc. Superior / Licenciatura) -> escribís el nombre, elegís el archivo y
     * tocás el botón "Subir".
     *
     * Agrega un requisito de defensa para uno de los tres tipos de título. Acepta
     * imagen o PDF y se guarda en el disco privado. Pueden ser varios por título.
     */
    public function agregarRequisitoDefensa(Request $request, string $id)
    {
        $request->validate([
            'tipo_titulo' => 'required|in:tec_medio,tec_superior,licenciatura',
            'nombre'      => 'required|string|max:255',
            'archivo'     => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $archivo = $request->file('archivo');
            $nombre  = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs('documentos_estudiantes/requisitos', $nombre, 'private');

            RequisitoDefensa::create([
                'estudiante_id' => $id,
                'tipo_titulo'   => $request->tipo_titulo,
                'nombre'        => $request->nombre,
                'archivo'       => $nombre,
            ]);

            DB::commit();
            $this->mensaje('exito', 'Requisito de defensa agregado.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Documentos del Estudiante", pestaña "Expediente de
     * Titulación" -> el botón del tacho (rojo) con title="Eliminar Requisito" en la
     * fila de cada requisito -> confirmación "¿Eliminar este requisito?".
     *
     * Borra un requisito de defensa: elimina el archivo del disco privado y luego
     * el registro.
     */
    public function eliminarRequisitoDefensa(string $id)
    {
        DB::beginTransaction();
        try {
            $requisito = RequisitoDefensa::findOrFail($id);
            Storage::disk('private')->delete("documentos_estudiantes/requisitos/{$requisito->archivo}");
            $requisito->delete();
            DB::commit();
            $this->mensaje('exito', 'Requisito eliminado.');
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: NO tiene botón propio. Alimenta los GRÁFICOS de las
     * pestañas de estadísticas de la página (Aprobados y Tendencia por gestión).
     *
     * Devuelve en JSON dos resúmenes para las gráficas:
     *   - aprobaciones: cuántos están aprobados / no aprobados en cada título.
     *   - tendencia: total de estudiantes por gestión, separados por género.
     */
    public function estadisticas()
    {
        $aprobaciones = Estudiante::selectRaw("
            SUM(CASE WHEN aprobado_tec_medio    = true THEN 1 ELSE 0 END) as aprobado_tm,
            SUM(CASE WHEN aprobado_tec_medio    = false THEN 1 ELSE 0 END) as no_aprobado_tm,
            SUM(CASE WHEN aprobado_tec_superior = true THEN 1 ELSE 0 END) as aprobado_ts,
            SUM(CASE WHEN aprobado_tec_superior = false THEN 1 ELSE 0 END) as no_aprobado_ts,
            SUM(CASE WHEN aprobado_licenciatura = true THEN 1 ELSE 0 END) as aprobado_lic,
            SUM(CASE WHEN aprobado_licenciatura = false THEN 1 ELSE 0 END) as no_aprobado_lic
        ")->first();

        $tendencia = Estudiante::selectRaw("
            gestion,
            COUNT(*) as total,
            SUM(CASE WHEN genero = 'masculino' THEN 1 ELSE 0 END) as masculino,
            SUM(CASE WHEN genero = 'femenino'  THEN 1 ELSE 0 END) as femenino
        ")
        ->groupBy('gestion')
        ->orderBy('gestion')
        ->get();

        return response()->json([
            'aprobaciones' => $aprobaciones,
            'tendencia'    => $tendencia,
        ]);
    }

    /**
     * 🔎 EN PANTALLA: botón "Reporte General" (arriba) -> abre el modal
     * "Reporte General de Estudiantes" -> botón "Generar PDF".
     *
     * Genera el PDF del reporte general de estudiantes según los filtros elegidos
     * (sedes, carreras, género, rango de gestión). Tiene dos formas de salida:
     *   - vista "listado": lista detallada de estudiantes.
     *   - vista "totales": solo conteos por sede o por carrera (según "agrupar_por"),
     *     usando un cruce sede–carrera para mostrar también las combinaciones sin
     *     estudiantes (en 0).
     * El PDF se devuelve en base64 y el JS lo abre en una pestaña nueva.
     */
    public function reporteGeneral(Request $request)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.generar_reporte')) {
            return redirect()->route('inicio');
        }

        $sedeIds    = array_filter((array) $request->input('sede_id', []));
        $carreraIds = array_filter((array) $request->input('carrera_id', []));
        $vista      = $request->input('vista', 'listado');
        $agruparPor = $request->input('agrupar_por', 'sede');

        $filtros = [
            'sedes'    => !empty($sedeIds)   ? Sede::whereIn('id', $sedeIds)->pluck('nombre')->map(fn($n) => strtoupper($n))->join(', ')    : 'Todas',
            'carreras' => !empty($carreraIds) ? Carrera::whereIn('id', $carreraIds)->pluck('nombre')->map(fn($n) => ucfirst($n))->join(', ') : 'Todas',
            'genero'   => $request->filled('genero') ? ucfirst($request->genero) : 'Todos',
            'gestion'  => ($request->filled('gestion_desde') || $request->filled('gestion_hasta'))
                            ? ($request->gestion_desde ?? '—') . ' a ' . ($request->gestion_hasta ?? '—') : 'Todas',
        ];

        if ($vista === 'totales') {
            $joinConditions = function ($join) use ($request) {
                $join->on('e.sede_id', '=', 'carrera_sede.sede_id')
                     ->on('e.carrera_id', '=', 'carrera_sede.carrera_id')
                     ->whereNull('e.deleted_at');
                if ($request->filled('genero'))        $join->where('e.genero', $request->genero);
                if ($request->filled('gestion_desde')) $join->where('e.gestion', '>=', $request->gestion_desde);
                if ($request->filled('gestion_hasta')) $join->where('e.gestion', '<=', $request->gestion_hasta);
            };

            $baseQuery = DB::table('carrera_sede')
                ->join('sedes',    'sedes.id',    '=', 'carrera_sede.sede_id')
                ->join('carreras', 'carreras.id', '=', 'carrera_sede.carrera_id')
                ->leftJoin('estudiantes as e', $joinConditions)
                ->whereNull('sedes.deleted_at')
                ->whereNull('carreras.deleted_at')
                ->where('sedes.estado', 'activo')
                ->where('carreras.estado', 'activo');

            if (!empty($sedeIds))    $baseQuery->whereIn('sedes.id', $sedeIds);
            if (!empty($carreraIds)) $baseQuery->whereIn('carreras.id', $carreraIds);

            if ($agruparPor === 'carrera') {
                $totales = $baseQuery
                    ->selectRaw("carreras.nombre as carrera_nombre, sedes.nombre as sede_nombre,
                        SUM(CASE WHEN e.genero = 'masculino' THEN 1 ELSE 0 END) as masculino,
                        SUM(CASE WHEN e.genero = 'femenino'  THEN 1 ELSE 0 END) as femenino,
                        COUNT(e.id) as total")
                    ->groupBy('carreras.nombre', 'sedes.nombre')
                    ->orderBy('carreras.nombre')->orderBy('sedes.nombre')
                    ->get();
            } else {
                $totales = $baseQuery
                    ->selectRaw("sedes.nombre as sede_nombre, carreras.nombre as carrera_nombre,
                        SUM(CASE WHEN e.genero = 'masculino' THEN 1 ELSE 0 END) as masculino,
                        SUM(CASE WHEN e.genero = 'femenino'  THEN 1 ELSE 0 END) as femenino,
                        COUNT(e.id) as total")
                    ->groupBy('sedes.nombre', 'carreras.nombre')
                    ->orderBy('sedes.nombre')->orderBy('carreras.nombre')
                    ->get();
            }

            $estudiantes    = collect();
            $limitado       = false;
            $totalSinLimite = 0;
        } else {
            $query = Estudiante::with(['sede:id,nombre', 'carrera:id,nombre'])
                ->select('id', 'nombre_completo', 'matricula', 'tipo_documento', 'numero_documento', 'genero', 'gestion', 'sede_id', 'carrera_id')
                ->orderBy('sede_id')->orderBy('nombre_completo');

            if (!empty($sedeIds))    $query->whereIn('sede_id', $sedeIds);
            if (!empty($carreraIds)) $query->whereIn('carrera_id', $carreraIds);
            if ($request->filled('genero'))        $query->where('genero', $request->genero);
            if ($request->filled('gestion_desde')) $query->where('gestion', '>=', $request->gestion_desde);
            if ($request->filled('gestion_hasta')) $query->where('gestion', '<=', $request->gestion_hasta);

            $totalSinLimite = $query->count();
            $estudiantes    = $query->get();
            $limitado       = false;
            $totales        = collect();
        }

        $user = auth()->user();
        $nombreUsuario = ucwords($user->nombres . ' ' . $user->apellidos);
        $rolUsuario    = $user->getRoleNames()->first() ?? '';

        $pdf = new ReporteGeneralEstudiantesPdf();
        $pdf->setGeneradoPor($nombreUsuario, $rolUsuario);
        $output = $pdf->generate($estudiantes, $filtros, $vista, $agruparPor, $totales, $limitado, $totalSinLimite);

        $this->mensaje('exito', base64_encode($output));
        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: botón "Pendientes" (arriba) -> abre el modal que, según el
     * tipo elegido, se titula "Reporte de Documentos Pendientes" o "Reporte de
     * Aprobados para Titularse" -> botón "Generar PDF" / "Generar PDF de Aprobados".
     *
     * Genera UNO de dos reportes en PDF según "tipo_reporte":
     *   - "aprobados": estudiantes según su estado de aprobación de expediente
     *     (cumplen / no cumplen / ambos) en las modalidades elegidas.
     *   - "pendientes" (por defecto): estudiantes según el estado de sus DOCUMENTOS
     *     de titulación (a quién le falta el certificado o las copias de título).
     * Las modalidades (tec_medio / tec_superior / licenciatura) se pueden filtrar;
     * si no se elige ninguna, se asumen las tres. El PDF sale en base64.
     */
    public function reportePendientes(Request $request)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.generar_reporte')) {
            return redirect()->route('inicio');
        }

        $sedeIds     = array_filter((array) $request->input('sede_id', []));
        $carreraIds  = array_filter((array) $request->input('carrera_id', []));
        $tipoReporte = $request->input('tipo_reporte', 'pendientes');
        $modalidades = array_values(array_filter((array) $request->input('modalidad', [])));
        $estado      = $request->input('estado', 'no_cumplen');

        if (empty($modalidades)) {
            $modalidades = ['tec_medio', 'tec_superior', 'licenciatura'];
        }

        $etiquetasModalidad = [
            'tec_medio'    => 'Tecnico Medio',
            'tec_superior' => 'Tecnico Superior',
            'licenciatura' => 'Licenciatura',
        ];

        $modalidadesLabel = count($modalidades) === 3
            ? 'Todas las modalidades'
            : implode(', ', array_map(fn($m) => $etiquetasModalidad[$m] ?? $m, $modalidades));

        $filtros = [
            'sedes'      => !empty($sedeIds)   ? Sede::whereIn('id', $sedeIds)->pluck('nombre')->map(fn($n) => strtoupper($n))->join(', ')    : 'Todas',
            'carreras'   => !empty($carreraIds) ? Carrera::whereIn('id', $carreraIds)->pluck('nombre')->map(fn($n) => ucfirst($n))->join(', ') : 'Todas',
            'genero'     => $request->filled('genero') ? ucfirst($request->genero) : 'Todos',
            'gestion'    => ($request->filled('gestion_desde') || $request->filled('gestion_hasta'))
                             ? ($request->gestion_desde ?? '—') . ' a ' . ($request->gestion_hasta ?? '—') : 'Todas',
            'modalidades' => $modalidadesLabel,
        ];

        // ── Reporte de aprobados ──────────────────────────────────────────────────
        if ($tipoReporte === 'aprobados') {
            $estadoLabels = ['cumplen' => 'Aprobados', 'no_cumplen' => 'No aprobados', 'ambos' => 'Todos'];
            $filtros['estado'] = $estadoLabels[$estado] ?? 'Todos';

            $camposAprobacion = array_map(fn($m) => 'aprobado_' . $m, $modalidades);

            $query = Estudiante::with(['sede:id,nombre', 'carrera:id,nombre'])
                ->select(array_merge(
                    ['id', 'nombre_completo', 'matricula', 'genero', 'gestion', 'sede_id', 'carrera_id'],
                    $camposAprobacion
                ))
                ->orderBy('sede_id')->orderBy('nombre_completo');

            if (!empty($sedeIds))    $query->whereIn('sede_id', $sedeIds);
            if (!empty($carreraIds)) $query->whereIn('carrera_id', $carreraIds);
            if ($request->filled('genero'))        $query->where('genero', $request->genero);
            if ($request->filled('gestion_desde')) $query->where('gestion', '>=', $request->gestion_desde);
            if ($request->filled('gestion_hasta')) $query->where('gestion', '<=', $request->gestion_hasta);

            if ($estado === 'cumplen') {
                $query->where(function ($q) use ($camposAprobacion) {
                    foreach ($camposAprobacion as $campo) {
                        $q->orWhere($campo, true);
                    }
                });
            } elseif ($estado === 'no_cumplen') {
                foreach ($camposAprobacion as $campo) {
                    $query->where($campo, false);
                }
            }

            $estudiantes = $query->get();

            $user = auth()->user();
            $pdf  = new ReportePendientesEstudiantesPdf();
            $pdf->setGeneradoPor(ucwords($user->nombres . ' ' . $user->apellidos), $user->getRoleNames()->first() ?? '');
            $output = $pdf->generateAprobados($estudiantes, $filtros, $modalidades);

            $this->mensaje('exito', base64_encode($output));
            return response()->json($this->mensaje, 200);
        }

        // ── Reporte de pendientes ─────────────────────────────────────────────────
        $estadoLabels = ['no_cumplen' => 'Con pendientes', 'cumplen' => 'Sin pendientes (completos)', 'ambos' => 'Todos'];
        $filtros['estado'] = $estadoLabels[$estado] ?? 'Todos';

        $camposPorModalidad = [
            'tec_medio'    => ['copia_titulo_tec_medio'],
            'tec_superior' => ['copia_titulo_tec_superior'],
            'licenciatura' => ['copia_titulo_licenciatura'],
        ];

        $camposModalidad = [];
        foreach ($modalidades as $mod) {
            $camposModalidad = array_merge($camposModalidad, $camposPorModalidad[$mod] ?? []);
        }

        $todosLosCampos = array_merge(['certificado_habilitacion'], $camposModalidad);

        $selectCampos = array_merge(
            ['id', 'nombre_completo', 'matricula', 'genero', 'gestion', 'sede_id', 'carrera_id'],
            $todosLosCampos
        );

        $query = Estudiante::with(['sede:id,nombre', 'carrera:id,nombre'])
            ->select($selectCampos)
            ->orderBy('sede_id')->orderBy('nombre_completo');

        if ($estado === 'no_cumplen') {
            $query->where(function ($q) use ($todosLosCampos) {
                foreach ($todosLosCampos as $campo) {
                    $q->orWhereNull($campo);
                }
            });
        } elseif ($estado === 'cumplen') {
            foreach ($todosLosCampos as $campo) {
                $query->whereNotNull($campo);
            }
        }

        if (!empty($sedeIds))    $query->whereIn('sede_id', $sedeIds);
        if (!empty($carreraIds)) $query->whereIn('carrera_id', $carreraIds);
        if ($request->filled('genero'))        $query->where('genero', $request->genero);
        if ($request->filled('gestion_desde')) $query->where('gestion', '>=', $request->gestion_desde);
        if ($request->filled('gestion_hasta')) $query->where('gestion', '<=', $request->gestion_hasta);

        $estudiantes = $query->get();

        $resumen = ['certificado' => $estudiantes->whereNull('certificado_habilitacion')->count()];
        foreach ($camposModalidad as $campo) {
            $resumen[$campo] = $estudiantes->whereNull($campo)->count();
        }

        $user = auth()->user();
        $pdf  = new ReportePendientesEstudiantesPdf();
        $pdf->setGeneradoPor(ucwords($user->nombres . ' ' . $user->apellidos), $user->getRoleNames()->first() ?? '');
        $output = $pdf->generate($estudiantes, $filtros, $resumen, $modalidades, $camposModalidad, false, 0);

        $this->mensaje('exito', base64_encode($output));
        return response()->json($this->mensaje, 200);
    }

    /**
     * 🔎 EN PANTALLA: botón "Ficha de Seguimiento PDF" (el ícono rojo de PDF en
     * cada fila de la tabla).
     *
     * Genera la ficha individual del estudiante en PDF (con sus datos, formularios
     * y requisitos) y la MUESTRA directamente en el navegador (inline), a diferencia
     * de los reportes generales que vienen en base64.
     */
    public function reporteIndividual(string $id)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.ficha')) {
            return redirect()->route('inicio');
        }

        $estudiante = Estudiante::with([
            'sede:id,nombre',
            'carrera:id,nombre',
            'formulariosInscripcion',
            'requisitosDefensa',
        ])->findOrFail($id);

        $user = auth()->user();
        $pdf  = new ReporteIndividualEstudiantePdf();
        $pdf->setGeneradoPor(ucwords($user->nombres . ' ' . $user->apellidos), $user->getRoleNames()->first() ?? '');
        $output = $pdf->generate($estudiante);

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"seguimiento_{$estudiante->matricula}.pdf\"",
        ]);
    }

    /**
     * 🔎 EN PANTALLA: botón del ojito con title="Ver Documento" que aparece al
     * lado de cada documento en la pestaña "Documentos" del modal "Documentos del
     * Estudiante" (cuando ya está "Subido").
     *
     * Sirve para VER el documento principal: como los archivos están en el disco
     * PRIVADO, no se pueden abrir con un enlace directo; este método los entrega de
     * forma protegida (inline) tras verificar que el archivo exista.
     */
    public function verDocumento(string $tipo, string $id)
    {
        if (!in_array($tipo, ['certificado_habilitacion', 'copia_titulo_tec_medio', 'copia_titulo_tec_superior', 'copia_titulo_licenciatura'])) {
            abort(404);
        }

        $estudiante = Estudiante::findOrFail($id);

        if (!$estudiante->$tipo) {
            abort(404, 'Documento no encontrado');
        }

        $path = "documentos_estudiantes/{$tipo}/{$estudiante->$tipo}";

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        $mime = Storage::disk('private')->mimeType($path);

        return response()->file(
            Storage::disk('private')->path($path),
            [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]
        );
    }

    /**
     * 🔎 EN PANTALLA: botón del ojito con title="Ver Formulario" en la fila de cada
     * formulario, dentro de la pestaña "Formularios de Inscripción" del modal
     * "Documentos del Estudiante".
     *
     * Entrega de forma protegida (inline) el PDF de un formulario de inscripción
     * guardado en el disco privado.
     */
    public function verFormulario(string $id)
    {
        $formulario = FormularioInscripcion::findOrFail($id);
        $path = "documentos_estudiantes/formularios/{$formulario->archivo}";

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        $mime = Storage::disk('private')->mimeType($path);

        return response()->file(
            Storage::disk('private')->path($path),
            [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]
        );
    }

    /**
     * 🔎 EN PANTALLA: botón del ojito con title="Ver Requisito" en la fila de cada
     * requisito, dentro de la pestaña "Expediente de Titulación" del modal
     * "Documentos del Estudiante".
     *
     * Entrega de forma protegida (inline) el archivo (imagen o PDF) de un requisito
     * de defensa guardado en el disco privado.
     */
    public function verRequisito(string $id)
    {
        $requisito = RequisitoDefensa::findOrFail($id);
        $path = "documentos_estudiantes/requisitos/{$requisito->archivo}";

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        $mime = Storage::disk('private')->mimeType($path);

        return response()->file(
            Storage::disk('private')->path($path),
            [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]
        );
    }

    /**
     * 🔎 EN PANTALLA: botón "Marcar Aprobado" / "Revocar Aprobación" que está en
     * la cabecera de cada sub-pestaña de título dentro de la pestaña "Expediente
     * de Titulación" del modal "Documentos del Estudiante".
     *
     * Es un INTERRUPTOR (toggle): cambia el estado de aprobación del expediente del
     * tipo de título indicado. Si estaba aprobado lo revoca, y si no lo estaba lo
     * aprueba. Devuelve el nuevo estado para que el JS actualice el badge y el botón.
     */
    public function aprobarExpediente(Request $request, string $id)
    {
        $request->validate([
            'tipo_titulo' => 'required|in:tec_medio,tec_superior,licenciatura',
        ]);

        DB::beginTransaction();
        try {
            $estudiante = Estudiante::findOrFail($id);
            $campo      = 'aprobado_' . $request->tipo_titulo;
            $estudiante->$campo = !$estudiante->$campo;
            $estudiante->save();

            $aprobado = $estudiante->$campo;
            DB::commit();

            $this->mensaje('exito', [
                'aprobado' => $aprobado,
                'mensaje'  => $aprobado
                    ? 'Expediente aprobado correctamente.'
                    : 'Aprobación revocada.',
            ]);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje('error', 'Error: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // --- CSV ---

    /**
     * 🔎 EN PANTALLA: botón "Subir CSV" (arriba) -> abre el modal "Importar
     * Estudiantes CSV" -> botón "Previsualizar".
     *
     * Primer paso de la importación (NO guarda nada todavía): lee el archivo CSV,
     * verifica que tenga las columnas requeridas (nombre_completo, matricula,
     * tipo_documento, numero_documento, sede, carrera, gestion, genero) y devuelve
     * los primeros 10 registros para mostrar la "Vista Previa". La carga real la
     * hace importarCSV().
     */
    public function previsualizarCSV(Request $request)
    {
        try {
            $request->validate(['archivo' => 'required|mimes:csv,txt']);

            $collection = Excel::toCollection(new PreviewSeguimientoEstudianteImport(), $request->file('archivo'))->first();

            if ($collection->isEmpty()) {
                $this->mensaje('error', 'El archivo está vacío.');
                return response()->json($this->mensaje, 200);
            }

            $headers  = $collection->first()->keys()->toArray();
            $expected = ['nombre_completo', 'matricula', 'tipo_documento', 'numero_documento', 'sede', 'carrera', 'gestion', 'genero'];
            $faltantes = array_diff($expected, $headers);

            if (!empty($faltantes)) {
                $this->mensaje('error', 'Faltan las columnas: <b>' . implode(', ', $faltantes) . '</b>');
                return response()->json($this->mensaje, 200);
            }

            $this->mensaje('exito', $collection->take(10));
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje('error', 'Error al leer el archivo: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * 🔎 EN PANTALLA: modal "Importar Estudiantes CSV", tras previsualizar ->
     * botón "Subir Definitivamente".
     *
     * Segundo paso: importa de verdad los estudiantes del CSV. Es "todo o nada":
     * si hay CUALQUIER error de validación (de Laravel o personalizado), deshace la
     * transacción y NO inserta nada, devolviendo la lista de errores por fila. Si
     * todo está correcto, confirma e informa cuántas filas se insertaron.
     */
    public function importarCSV(Request $request)
    {
        $request->validate(['archivo' => 'required|mimes:csv,txt']);

        DB::beginTransaction();
        try {
            $import = new SeguimientoEstudianteImport();
            Excel::import($import, $request->file('archivo'));

            $erroresValidacion     = $import->failures();
            $erroresPersonalizados = $import->erroresPersonalizados;

            if (count($erroresValidacion) > 0 || count($erroresPersonalizados) > 0) {
                DB::rollBack();
                return response()->json([
                    'estado'                 => 'error_validacion',
                    'mensaje'                => 'Importación cancelada por errores en los datos.',
                    'errores_validacion'     => $erroresValidacion,
                    'errores_personalizados' => $erroresPersonalizados,
                ], 200);
            }

            $import->finalize();
            DB::commit();
            return response()->json([
                'estado'           => 'exito',
                'mensaje'          => 'Importación completada exitosamente.',
                'filas_insertadas' => $import->filasInsertadas,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => 'error', 'mensaje' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔎 EN PANTALLA: NO tiene botón. Es un AYUDANTE interno.
     *
     * Arma la respuesta estándar { tipo, mensaje } que casi todos los métodos
     * devuelven en JSON, y que el JS usa para mostrar las alertas de éxito/error.
     */
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = ['tipo' => $titulo, 'mensaje' => $mensaje];
    }
}
