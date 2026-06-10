<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Carrera;
use App\Models\EstadisticaDocente;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Imports\DocenteImport;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

/**
 * ============================================================================
 *  CONTROLADOR: Estadísticas de Docentes (módulo Académico)
 * ============================================================================
 *  Maneja la pantalla "Registro de docentes": la tabla de docentes (filtrable
 *  por gestión), la edición rápida de un registro, la importación masiva por
 *  CSV/Excel y el reporte en PDF por carrera o sede.
 *
 *  GUÍA RÁPIDA (busca con Ctrl+F el texto del botón que ves en pantalla):
 *    (desplegable de gestión)  -> listarDocentes()  (recarga la tabla)
 *    "Actualizar Informacion"  -> actualizar_registro_docente()
 *    "Subir datos" / "Importar" (vista previa) -> previsualizarDocentes()
 *    "Subir Definitivamente"   -> subirDatosDocentescsv()
 *    "Generar Reporte" / "Generar PDF" -> generar_reporte_docente()
 * ============================================================================
 */
class Controlador_estadisticasDocente extends Controller
{
    /**
     * 🔎 EN PANTALLA: carga la PÁGINA completa "Registro de docentes"
     * (la que abrís desde el menú del administrador).
     *
     * Prepara lo que la vista necesita: las gestiones (años) registradas, las
     * carreras con sus sedes y las sedes activas para los filtros del reporte, y
     * la configuración del botón del sistema (config_botones). Por defecto muestra
     * la gestión actual.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('docentes.inicio')) {
            return redirect()->route('inicio');
        }

        $gestionActual = date('Y');

        // obtenemos las gestiones distintas existentes
        $gestiones = EstadisticaDocente::select('gestion')
            ->distinct()
            ->where('gestion', '!=', $gestionActual)
            ->orderByDesc('gestion')
            ->pluck('gestion'); // devuelve solo los valores de la columna


        // Cargamos todas las carreras con sus sedes y estadísticas de la gestión seleccionada
        $carreras = Carrera::with(['sedes', 'estadisticas' => function ($q) {
            // $q->where('gestion', $gestion);
        }])->orderBy('nombre', 'asc')
          ->get();


        $sedes = Sede::where('estado', 'activo')->orderBy('nombre', 'asc')->get();

        $boton = DB::table('config_botones')
                 ->where('clave', 'btn_sistema_docentes')
                 ->first();
        
        return view('administrador.academico.docentes', compact('sedes', 'carreras', 'gestionActual', 'gestiones', 'boton'));
    }


    /**
     * 🔎 EN PANTALLA: NO tiene botón propio: es la que LLENA LA TABLA de docentes.
     * Se recarga según la gestión elegida en el desplegable de la parte superior.
     *
     * Responde en JSON al DataTable (carga por servidor): trae los docentes de la
     * gestión elegida, aplica el buscador (sede, carrera, documento o estado) y
     * devuelve el permiso de editar para que el JS muestre el botón de actualizar
     * de cada fila.
     */
    public function listarDocentes(Request $request)
    {
        $gestion = $request->input('fecha', date('Y')); // por defecto el año actual

        $query = EstadisticaDocente::with([
            'carrera' => function ($q) {
                $q->select(['id', 'nombre']);
            },
            'sede' => function ($q) {
                $q->select(['id', 'nombre']);
            }
        ])
         ->select(['id','nombreCompleto','documento_identidad','genero','profesion','grado_academico','estado','carrera_id','sede_id'])
         ->where('gestion', $gestion)
         ->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {

                $q->orWhereHas('sede', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhereHas('carrera', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhere('documento_identidad', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('estado', 'like', '%' . $request->search['value'] . '%');
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
                'editar' => auth()->user()->can('docentes.editar'),
               
            ],
        ]);
    }



    /**
     * 🔎 EN PANTALLA: botón con title="Actualizar Informacion" (el ✓ azul al final
     * de cada fila de la tabla). Arranca deshabilitado; se activa al marcar el
     * checkbox de la fila, que la pone en modo edición (los campos se vuelven
     * editables ahí mismo, en la tabla, sin modal).
     *
     * Guarda los cambios de UN docente: solo permite editar nombre, documento,
     * género, grado académico y profesión (la carrera, sede y gestión NO se tocan
     * desde acá). El documento debe seguir siendo único.
     */
    public function actualizar_registro_docente(Request $request, String $id)
    {


        try {

            // 1️⃣ Validar que se suba un archivo válido
            $request->validate([
                'nombreCompleto' => 'required|max:100|min:5',
                'documentoIdentidad' => [
                    'required',
                    'max:50',
                    'min:3',
                    Rule::unique('estadistica_docentes', 'documento_identidad')->ignore($id),
                ],
                'genero' => 'required|in:masculino,femenino',
                'grado_academico' => 'required|max:100|min:3',
                'profesion' => 'required|max:100|min:3',
            ]);



            $docente = EstadisticaDocente::find($id);
            $docente->nombreCompleto = $request->nombreCompleto;
            $docente->documento_identidad = $request->documentoIdentidad;
            $docente->genero = $request->genero;
            $docente->grado_academico = $request->grado_academico;
            $docente->profesion = $request->profesion;

            $docente->save();

            $this->mensaje("exito", "Editado Correctamente");
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {


            $this->mensaje("error", "Error " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }


    /**
     * 🔎 EN PANTALLA: botón "Subir datos" (arriba) -> abre el modal "Importar
     * Planilla Docentes" -> botón "Importar" (muestra la vista previa).
     *
     * Primer paso de la importación (NO guarda nada todavía): lee el archivo CSV,
     * verifica que tenga las columnas requeridas (nombre_completo, documento,
     * carrera, sede, genero, gestion, profesion, grado_academico) y devuelve las
     * primeras 3 filas para mostrar la vista previa. La carga real la hace
     * subirDatosDocentescsv().
     */
    public function previsualizarDocentes(Request $request)
    {
        try {
            $request->validate([
                 'archivo' => 'required|mimes:csv,txt',
            ]);

            // Convertir a colección
            $collection = Excel::toCollection(new \App\Imports\PreviewDocenteImport(), $request->file('archivo'))->first();

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
                'gestion',
                'profesion',
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
     * 🔎 EN PANTALLA: modal "Importar Planilla Docentes", después de previsualizar
     * -> botón "Subir Definitivamente".
     *
     * Segundo paso: importa de verdad los docentes del archivo. Es "todo o nada":
     * si hay CUALQUIER error en los datos, deshace la transacción y NO inserta nada,
     * devolviendo la lista de errores por fila. Si todo está correcto, confirma e
     * informa cuántas filas se insertaron.
     */
    public function subirDatosDocentescsv(Request $request)
    {
        // 1️⃣ Validar que se suba un archivo válido
        $request->validate([
            'archivo' => 'required|mimes:csv,xlsx,xls',
        ]);

        DB::beginTransaction();

        try {
            // 2️⃣ Cargar el importador
            $import = new DocenteImport();

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
     * 🔎 EN PANTALLA: botón "Generar Reporte" (arriba) -> abre el modal "Filtrar
     * Reporte" -> botón "Generar PDF".
     *
     * Genera el PDF con el conteo de docentes, agrupado de dos formas según el
     * "tipo" elegido: por "carrera" o por "sede". Usa un cruce sede–carrera para
     * mostrar también las combinaciones que quedaron en 0 (sin docentes). Filtra
     * por la gestión elegida. El PDF sale en base64 y el JS lo abre en una pestaña
     * nueva.
     */
    public function generar_reporte_docente(Request $request)
    {

        try {

            $tipo = $request->input('tipo');
            $seleccionados = $request->input('seleccionados', []);
            $gestion = $request->input('gestion', date('Y'));

            // Contenedor para los resultados
            $estadisticas = collect();

            if ($tipo === 'carrera') {

                $estadisticas = DB::table('carrera_sede')
                    ->join('carreras', 'carrera_sede.carrera_id', '=', 'carreras.id')
                    ->join('sedes', 'carrera_sede.sede_id', '=', 'sedes.id')

                    ->leftJoin('estadistica_docentes', function ($join) use ($gestion) {
                            $join->on('estadistica_docentes.carrera_id', '=', 'carreras.id')
                                ->on('estadistica_docentes.sede_id', '=', 'sedes.id')
                                ->where('estadistica_docentes.gestion', $gestion);                          
                    })
                   ->select(
                        'carreras.nombre as carrera',
                        'sedes.nombre as sede',
                        'estadistica_docentes.gestion',
                        DB::raw('COALESCE(COUNT(estadistica_docentes.id), 0) as total')
                    )


                    ->whereIn('carreras.id', $seleccionados)
                    ->where('carreras.estado', 'activo')
                    ->where('estadistica_docentes.estado', 'activo')
                    ->where('sedes.estado', 'activo')

                    ->groupBy(
                        'carreras.nombre',
                        'sedes.nombre', 
                        'estadistica_docentes.gestion'                      
                    )
                    ->orderBy('carreras.nombre')
                    ->orderBy('sedes.nombre')                    
                    ->get();
            }


            if ($tipo === 'sede') {

                $estadisticas = DB::table('carrera_sede')
                    ->join('carreras', 'carrera_sede.carrera_id', '=', 'carreras.id')
                    ->join('sedes', 'carrera_sede.sede_id', '=', 'sedes.id')

                    ->leftJoin('estadistica_docentes', function ($join) use ($gestion) {
                            $join->on('estadistica_docentes.carrera_id', '=', 'carreras.id')
                                ->on('estadistica_docentes.sede_id', '=', 'sedes.id')
                                ->where('estadistica_docentes.gestion', $gestion);                          
                    })
                   ->select(
                        'carreras.nombre as carrera',
                        'sedes.nombre as sede',
                        'estadistica_docentes.gestion',
                        DB::raw('COALESCE(COUNT(estadistica_docentes.id), 0) as total')
                    )


                    ->whereIn('sedes.id', $seleccionados)
                    ->where('carreras.estado', 'activo')
                    ->where('estadistica_docentes.estado', 'activo')
                    ->where('sedes.estado', 'activo')

                    ->groupBy(
                        'carreras.nombre',
                        'sedes.nombre', 
                        'estadistica_docentes.gestion'                      
                    )
                    ->orderBy('carreras.nombre')
                    ->orderBy('sedes.nombre')                    
                    ->get();
            }

            $nombreCompletoUsuario = auth()
              ->user()
              ->only(['nombres', 'apellidos']);


            // Cargar la vista PDF
            $pdf = \PDF::loadView('administrador.academico.reporteDocente', [
                'tipo' => $tipo,
                'estadisticas' => $estadisticas,
                'gestion' => $gestion,
                'usuarioGenerador' => $nombreCompletoUsuario,
            ]);
            // Render PDF y devolverlo en base64
            $pdfContent = $pdf->output();
            $pdfb64 = base64_encode($pdfContent);

            $this->mensaje('exito', $pdfb64);
            return response()->json($this->mensaje, 200);


        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
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
