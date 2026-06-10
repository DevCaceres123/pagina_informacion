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
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

/**
 * ============================================================================
 *  CONTROLADOR: Estadísticas de Titulados (módulo Académico)
 * ============================================================================
 *  Maneja la pantalla "Registro de Titulados": la tabla de titulados (filtrable
 *  por año/gestión y por fecha de colación), la edición rápida de un registro,
 *  la importación masiva por CSV/Excel y el reporte en PDF por carrera o sede.
 *
 *  GUÍA RÁPIDA (busca con Ctrl+F el texto del botón que ves en pantalla):
 *    "Filtrar"               -> listarTitulados()  (recarga la tabla)
 *    "Actualizar Informacion"-> actualizar_registro_titulado()
 *    "Subir datos" / "Importar" (vista previa) -> previsualizarTitulados()
 *    "Subir Definitivamente" -> subirDatosTituladoscsv()
 *    "Generar Reporte" / "Generar PDF" -> generar_reporte_titulados()
 * ============================================================================
 */
class Controlador_estadisticasTitulados extends Controller
{
    /**
     * 🔎 EN PANTALLA: carga la PÁGINA completa "Registro de Titulados"
     * (la que abrís desde el menú del administrador).
     *
     * Prepara todo lo que la vista necesita: los años (gestiones) que tienen
     * titulados registrados, las fechas de colación del año actual, las carreras
     * y sedes activas para los filtros del reporte, y la configuración del botón
     * del sistema (config_botones). Por defecto muestra el año actual.
     */
    public function index(Request $request)
    {
         if (!auth()->user()->can('titulados.inicio')) {
            return redirect()->route('inicio');
        }
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

        $boton = DB::table('config_botones')
                 ->where('clave', 'btn_sistema_titulado')
                 ->first();
        
        return view('administrador.academico.titulados', compact('sedes', 'carreras', 'anios', 'colaciones', 'gestionActual', 'anioSeleccionado', 'colacionSeleccionada','boton'));
    }



    /**
     * 🔎 EN PANTALLA: NO tiene botón propio: es la que LLENA LA TABLA de titulados.
     * Se recarga al elegir un año y tocar el botón "Filtrar" (también filtra por la
     * fecha de colación elegida en el desplegable).
     *
     * Responde en JSON al DataTable (carga por servidor): trae los titulados del
     * año/gestión elegido (y de la colación exacta si se seleccionó una), aplica el
     * buscador (nombre, documento, carrera, sede, género o grado) y devuelve el
     * permiso de editar para que el JS muestre el botón de actualizar de cada fila.
     */
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
                'editar' => auth()->user()->can('titulados.editar'),               
            ],
        ]);
    }

    /**
     * 🔎 EN PANTALLA: botón con title="Actualizar Informacion" (el ✓ azul al final
     * de cada fila de la tabla). Ese botón está deshabilitado hasta que marcás el
     * checkbox de la fila, que la pone en modo edición (los campos se vuelven
     * editables ahí mismo, en la tabla, sin modal).
     *
     * Guarda los cambios de UN titulado: solo permite editar nombre, documento,
     * género y grado académico (la carrera, sede y fecha de colación NO se tocan
     * desde acá).
     */
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



    /**
     * 🔎 EN PANTALLA: botón "Subir datos" (arriba) -> abre el modal "Importar
     * Planilla Titulados" -> botón "Importar" (muestra la vista previa).
     *
     * Primer paso de la importación (NO guarda nada todavía): lee el archivo CSV,
     * verifica que tenga las columnas requeridas (nombre_completo, documento,
     * carrera, sede, genero, fecha, grado_academico) y devuelve las primeras 3
     * filas para mostrar la vista previa. La carga real la hace
     * subirDatosTituladoscsv().
     */
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



    /**
     * 🔎 EN PANTALLA: modal "Importar Planilla Titulados", después de previsualizar
     * -> botón "Subir Definitivamente".
     *
     * Segundo paso: importa de verdad los titulados del archivo. Es "todo o nada":
     * si hay CUALQUIER error en los datos, deshace la transacción y NO inserta nada,
     * devolviendo la lista de errores por fila. Si todo está correcto, confirma e
     * informa cuántas filas se insertaron.
     */
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
            
            $erroresPersonalizados = $import->erroresPersonalizados;

            // 5️⃣ Si hay cualquier tipo de error -> rollback y no guardar nada
            if (count($erroresPersonalizados) > 0) {
                DB::rollBack();

                return response()->json([
                    'estado' => 'error_validacion',
                    'mensaje' => 'La importación fue cancelada. Se detectaron errores en los datos.',
                    
                    'errores_personalizados' => $erroresPersonalizados,
                ], 200);
            }

             // 6️⃣ Si todo está correcto, confirmamos la transacción
            $import->finalize();
            // 6️⃣ Si todo está correcto, confirmamos la transacción
            DB::commit();

            return response()->json([
                'estado' => 'exito',
                'mensaje' => 'Importación completada exitosamente',
                'filas_insertadas' => $import->filasInsertadas,
            ], 200);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'estado' => 'error_validacion',
                'errores_validacion' => $e->failures()
            ]);            
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Ocurrió un error inesperado durante la importación',
                'detalle' => $e->getMessage(),
            ], 200);
        }
    }
    /**
     * 🔎 EN PANTALLA: NO tiene botón. Alimenta el DESPLEGABLE de fechas de colación.
     *
     * Cuando elegís un año, devuelve en JSON las fechas de colación de ese año (ya
     * formateadas en español, ej. "12 de marzo 2024") para llenar el selector de
     * fecha con el que después filtrás la tabla.
     */
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



    /**
     * 🔎 EN PANTALLA: botón "Generar Reporte" (arriba) -> abre el modal "Filtrar
     * Reporte" -> botón "Generar PDF".
     *
     * Genera el PDF con el conteo de titulados, agrupado de dos formas según el
     * "tipo" elegido: por "carrera" o por "sede". Usa un cruce sede–carrera para
     * mostrar también las combinaciones que quedaron en 0 (sin titulados). Filtra
     * por los grados académicos marcados y por la gestión. El PDF sale en base64 y
     * el JS lo abre en una pestaña nueva.
     */
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

            if ($tipo === 'carrera') {

                    $estadisticas = DB::table('carrera_sede')
                        ->join('carreras', 'carrera_sede.carrera_id', '=', 'carreras.id')
                        ->join('sedes', 'carrera_sede.sede_id', '=', 'sedes.id')

                        ->leftJoin('estadistica_titulados', function ($join) use ($gestion, $grados) {
                            $join->on('estadistica_titulados.carrera_id', '=', 'carreras.id')
                                ->on('estadistica_titulados.sede_id', '=', 'sedes.id')
                                ->where('estadistica_titulados.fecha_colacion', $gestion)
                                ->whereIn('estadistica_titulados.grado_academico', $grados);
                        })

                        ->select(
                            'carreras.nombre as carrera',
                            'sedes.nombre as sede',
                            'estadistica_titulados.grado_academico',
                            DB::raw('COUNT(estadistica_titulados.id) as total')
                        )

                        ->whereIn('carreras.id', $seleccionados)
                        ->where('carreras.estado', 'activo')
                        ->where('sedes.estado', 'activo')

                        ->groupBy(
                            'carreras.nombre',
                            'sedes.nombre',
                            'estadistica_titulados.grado_academico'
                        )

                        ->orderBy('carreras.nombre')
                        ->orderBy('sedes.nombre')
                        ->orderBy('estadistica_titulados.grado_academico')
                        ->get();
            }


             if ($tipo === 'sede') {

                  $estadisticas = DB::table('carrera_sede')
                        ->join('carreras', 'carrera_sede.carrera_id', '=', 'carreras.id')
                        ->join('sedes', 'carrera_sede.sede_id', '=', 'sedes.id')

                        ->leftJoin('estadistica_titulados', function ($join) use ($gestion, $grados) {
                            $join->on('estadistica_titulados.carrera_id', '=', 'carreras.id')
                                ->on('estadistica_titulados.sede_id', '=', 'sedes.id')
                                ->where('estadistica_titulados.fecha_colacion', $gestion)
                                ->whereIn('estadistica_titulados.grado_academico', $grados);
                        })

                        ->select(                            
                            'sedes.nombre as sede',
                            'carreras.nombre as carrera',
                            'estadistica_titulados.grado_academico',
                            DB::raw('COUNT(estadistica_titulados.id) as total')
                        )

                        ->whereIn('sedes.id', $seleccionados)
                        ->where('carreras.estado', 'activo')
                        ->where('sedes.estado', 'activo')

                        ->groupBy(
                            'carreras.nombre',
                            'sedes.nombre',
                            'estadistica_titulados.grado_academico'
                        )

                        ->orderBy('carreras.nombre')
                        ->orderBy('sedes.nombre')
                        ->orderBy('estadistica_titulados.grado_academico')
                        ->get();
             }
            

            // Obtener datos del usuario
            $usuario = auth()->user()->only(['nombres', 'apellidos']);


            $gradosSeleccionadosNombres = $grados;

            // Generar PDF`
            $pdf = \PDF::loadView('administrador.academico.reporteTitulados', [
                'tipo' => $tipo,
                'estadisticas' => $estadisticas,
                'gestion' => $gestion,
                'usuarioGenerador' => $usuario,                
                'gradosSeleccionadosNombres' => $gradosSeleccionadosNombres,
            ]);

            $pdfb64 = base64_encode($pdf->output());

           
            $this->mensaje('exito', $pdfb64);
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
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
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }

}
