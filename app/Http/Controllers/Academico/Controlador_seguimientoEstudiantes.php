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

class Controlador_seguimientoEstudiantes extends Controller
{
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
            ],
        ]);
    }

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

    public function reporteGeneral(Request $request)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.inicio')) {
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

    public function reportePendientes(Request $request)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.inicio')) {
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

    public function reporteIndividual(string $id)
    {
        if (!auth()->user()->can('seguimiento_estudiantes.inicio')) {
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

    public function verDocumento(string $tipo, string $id)
    {
        if (!in_array($tipo, ['certificado_habilitacion', 'fotocopia_titulo', 'copia_titulo'])) {
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

    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = ['tipo' => $titulo, 'mensaje' => $mensaje];
    }
}
