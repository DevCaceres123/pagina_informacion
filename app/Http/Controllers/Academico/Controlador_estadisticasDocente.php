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

class Controlador_estadisticasDocente extends Controller
{
    public function index(Request $request)
    {
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

        return view('administrador.academico.docentes', compact('sedes', 'carreras', 'gestionActual', 'gestiones'));
    }


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
                'editar' => auth()->user()->can('afiliado.editar'),
                'eliminar' => true,
                'estado' => auth()->user()->can('afiliado.estado'),
            ],
        ]);
    }



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



    public function generar_reporte_docente(Request $request)
    {

        try {

            $tipo = $request->input('tipo');
            $seleccionados = $request->input('seleccionados', []);
            $gestion = $request->input('gestion', date('Y'));

            // Contenedor para los resultados
            $estadisticas = collect();



            // Obtener los datos filtrados
            $query = DB::table('estadistica_docentes')
                ->join('carreras', 'estadistica_docentes.carrera_id', '=', 'carreras.id')
                ->join('sedes', 'estadistica_docentes.sede_id', '=', 'sedes.id')
                ->select(
                    'carreras.nombre as carrera',
                    'sedes.nombre as sede',
                    'estadistica_docentes.gestion',
                    DB::raw('COUNT(*) as total')
                )
                ->where('carreras.estado', 'activo')
                ->where('sedes.estado', 'activo')                
                ->where('estadistica_docentes.gestion', $gestion)
                ->groupBy('carreras.nombre', 'sedes.nombre', 'estadistica_docentes.gestion');

            // Filtrado dinámico
            if ($tipo === 'carrera') {
                $query->whereIn('estadistica_docentes.carrera_id', $seleccionados);
            } else {
                $query->whereIn('estadistica_docentes.sede_id', $seleccionados);
            }

            // Ejecutar
            $estadisticas = $query
                ->orderBy('carreras.nombre')
                ->orderBy('sedes.nombre')                
                ->get();
            

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

    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
