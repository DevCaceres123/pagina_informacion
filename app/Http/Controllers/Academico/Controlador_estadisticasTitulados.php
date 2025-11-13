<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Carrera;
use App\Models\EstadisticaTitulado;
use App\Models\EstadisticaEstudiante;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Imports\tituladosImport;

class Controlador_estadisticasTitulados extends Controller
{
    public function index(Request $request)
    {
        $gestionActual = date('Y');


        // Obtenemos todos los años que tienen colaciones registradas (PostgreSQL compatible)
        $anios = EstadisticaTitulado::selectRaw('EXTRACT(YEAR FROM fecha_colacion) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio');


        // Obtenemos las fechas de colación de ese año
        $colaciones = EstadisticaTitulado::select('fecha_colacion')
            ->whereYear('fecha_colacion', $gestionActual)
            ->distinct()
            ->orderByDesc('fecha_colacion')
            ->pluck('fecha_colacion');

        $anioSeleccionado = $anios->first();
        $colacionSeleccionada = $colaciones->first();


        // Cargamos todas las carreras con sus sedes y estadísticas de la gestión seleccionada
        $carreras = Carrera::where('estado', 'activo')
            ->orderBy('nombre', 'asc')
            ->get();


        $sedes = Sede::where('estado', 'activo')->orderBy('nombre', 'asc')->get();

        return view('administrador.academico.titulados', compact('sedes', 'carreras', 'anios', 'colaciones', 'gestionActual', 'anioSeleccionado', 'colacionSeleccionada'));
    }



    public function listarTitulados(Request $request)
    {
        $gestion = $request->input('gestion', date('Y')); // por defecto el año actual
        $colacion = $request->input('colacion', null); // puede venir vacío

        $query = EstadisticaTitulado::with(['carrera', 'sede' => function ($q) use ($gestion) {

            $q->select(['id','nombre']);
        }])
        ->select(['id','nombreCompleto','documentoIdentidad','genero','grado_academico','carrera_id','sede_id','fecha_colacion'])
        ->whereYear('fecha_colacion', $gestion) // filtra por año de colación
        ->orderBy('id', 'desc');

        // Si se seleccionó una colación específica, filtramos también por fecha exacta
        if ($colacion) {
            $query->whereDate('fecha_colacion', $colacion);
        }


        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {

                $q->orWhereHas('sede', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhereHas('carrera', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhere('nombreCompleto', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('grado_academico', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('genero', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('documentoIdentidad', 'like', '%' . $request->search['value'] . '%');
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

    public function actualizar_registro_titulado(Request $request, String $id)
    {


        try {

            // 1️⃣ Validar que se suba un archivo válido
            $request->validate([
                'nombreCompleto' => 'required|max:100|min:5',
                'documentoIdentidad' => [
                    'required',
                    'max:50',
                    'min:3',
                    // Rule::unique('estadistica_titulados', 'documentoIdentidad')->ignore($id),
                ],
                'genero' => 'required|in:masculino,femenino',
                'grado_academico' => 'required|in:licenciatura,tecnico medio,tecnico superior',
            ]);

            $titulado = EstadisticaTitulado::find($id);
            $titulado->nombreCompleto = $request->nombreCompleto;
            $titulado->documentoIdentidad = $request->documentoIdentidad;
            $titulado->genero = $request->genero;
            $titulado->grado_academico = $request->grado_academico;

            $titulado->save();

            $this->mensaje("exito", "Editado Correctamente");
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {


            $this->mensaje("error", "Error " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function previsualizarTitulados(Request $request)
    {
        try {
            $request->validate([
                 'archivo' => 'required|mimes:csv,txt',
            ]);

            // Convertir a colección
            $collection = Excel::toCollection(new \App\Imports\PreviewTituladosImport(), $request->file('archivo'))->first();

            if ($collection->isEmpty()) {
                $this->mensaje('error', 'El archivo está vacío o no tiene datos.');
                return response()->json($this->mensaje, 200);
            }

            // 🔹 1. Obtener cabeceras reales (primera fila)
            $headers = $collection->first()->keys()->toArray();

            // 🔹 2. Definir cabeceras esperadas
            $expectedHeaders = [
                'nombre_completo',
                'documento',
                'carrera',
                'sede',
                'genero',
                'fecha',
                'grado_academico',
            ];

            // 🔹 3. Comparar cabeceras
            $faltantes = array_diff($expectedHeaders, $headers);


            if (!empty($faltantes)) {
                $mensaje = [];
                if (!empty($faltantes)) {
                    $mensaje[] = 'Faltan las columnas: <b>' . implode(', ', $faltantes) . '</b>';
                }

                $this->mensaje('error', implode(' | ', $mensaje));
                return response()->json($this->mensaje, 200);
            }

            // 🔹 4. Si todo está bien, enviamos vista previa
            $this->mensaje('exito', $collection->take(3));
            return response()->json($this->mensaje, 200);

        } catch (\Exception $e) {
            $this->mensaje('error', 'Error al leer el archivo: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function subirDatosTituladoscsv(Request $request)
    {
        // 1️⃣ Validar que se suba un archivo válido
        $request->validate([
            'archivo' => 'required|mimes:csv,xlsx,xls',
        ]);

        DB::beginTransaction();

        try {
            // 2️⃣ Cargar el importador
            $import = new tituladosImport();

            // 3️⃣ Ejecutar la importación
            Excel::import($import, $request->file('archivo'));

            // 4️⃣ Capturar errores
            $erroresValidacion = $import->failures();
            $erroresPersonalizados = $import->erroresPersonalizados;

            // 5️⃣ Si hay cualquier tipo de error -> rollback y no guardar nada
            if (count($erroresValidacion) > 0 || count($erroresPersonalizados) > 0) {
                DB::rollBack();

                return response()->json([
                    'estado' => 'error_validacion',
                    'mensaje' => 'La importación fue cancelada. Se detectaron errores en los datos.',
                    'errores_validacion' => $erroresValidacion,
                    'errores_personalizados' => $erroresPersonalizados,
                ], 200);
            }

            // 6️⃣ Si todo está correcto, confirmamos la transacción
            DB::commit();

            return response()->json([
                'estado' => 'exito',
                'mensaje' => 'Importación completada exitosamente',
                'filas_insertadas' => $import->filasInsertadas,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Ocurrió un error inesperado durante la importación',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }
    public function listarFechasColacion(Request $request)
    {
        $gestion = $request->input('anio', date('Y'));

        $colaciones = EstadisticaTitulado::select('fecha_colacion')
            ->whereYear('fecha_colacion', $gestion)
            ->distinct()
            ->orderByDesc('fecha_colacion')
            ->pluck('fecha_colacion');

        // Formatear cada fecha
        $fechas = $colaciones->map(function ($fecha) {
            return [
                'valor' => $fecha, // lo que usas para filtrar
                'texto' => Carbon::parse($fecha)
                    ->locale('es')
                    ->translatedFormat('d \d\e F Y'), // lo que se muestra
            ];
        });

        return response()->json([
            'tipo' => 'exito',
            'mensaje' => $fechas,
        ]);
    }



    public function generar_reporte_titulados(Request $request)
    {
        try {
            $tipo = $request->input('tipo'); // 'carrera' o 'sede'
            $seleccionados = $request->input('seleccionados', []); // ids de carreras o sedes
            $grados = $request->input('grados', []); // ['tecnico medio', 'licenciatura', ...]
            $gestion = $request->input('gestion');

            // Validar tipo
            if (!in_array($tipo, ['carrera', 'sede'])) {
                return response()->json(['error' => 'Tipo inválido'], 400);
            }

            // Construcción base
            $query = DB::table('estadistica_titulados')
                ->join('carreras', 'estadistica_titulados.carrera_id', '=', 'carreras.id')
                ->join('sedes', 'estadistica_titulados.sede_id', '=', 'sedes.id')
                ->select(
                    'carreras.nombre as carrera',
                    'sedes.nombre as sede',
                    'estadistica_titulados.grado_academico',
                    DB::raw('COUNT(*) as total')
                )
                ->where('carreras.estado', 'activo')
                ->where('sedes.estado', 'activo')
                ->whereIn('estadistica_titulados.grado_academico', $grados)
                ->whereDate('estadistica_titulados.fecha_colacion', $gestion)
                ->groupBy('carreras.nombre', 'sedes.nombre', 'estadistica_titulados.grado_academico');

            // Filtrado dinámico
            if ($tipo === 'carrera') {                                                
                $query->whereIn('estadistica_titulados.carrera_id', $seleccionados);                
            } else {                
                $query->whereIn('estadistica_titulados.sede_id', $seleccionados);                
            }

            
            // Ejecutar
            $estadisticas = $query
                ->orderBy('carreras.nombre')
                ->orderBy('sedes.nombre')
                ->orderBy('estadistica_titulados.grado_academico')
                ->get();

            

            // Obtener datos del usuario
            $usuario = auth()->user()->only(['nombres', 'apellidos']);

            // Obtener nombres de filtros seleccionados
            if ($tipo === 'carrera') {
                $seleccionadosNombres = \App\Models\Carrera::whereIn('id', $seleccionados)->pluck('nombre')->toArray();
            } else {
                $seleccionadosNombres = \App\Models\Sede::whereIn('id', $seleccionados)->pluck('nombre')->toArray();
            }

            $gradosSeleccionadosNombres = $grados;

            // Generar PDF`
            $pdf = \PDF::loadView('administrador.academico.reporteTitulados', [
                'tipo' => $tipo,
                'estadisticas' => $estadisticas,
                'gestion' => $gestion,
                'usuarioGenerador' => $usuario,
                'seleccionadosNombres' => $seleccionadosNombres,
                'gradosSeleccionadosNombres' => $gradosSeleccionadosNombres,
            ]);

            $pdfb64 = base64_encode($pdf->output());

           
            $this->mensaje('exito', $pdfb64);
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
            return response()->json(['estado' => 'error', 'mensaje' => $e->getMessage()], 500);
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
