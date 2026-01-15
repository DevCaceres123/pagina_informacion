<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Carrera;
use App\Models\EstadisticaAdministrativo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AdministrativoImport;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class Controlador_estadisticasAdministrativo extends Controller
{
    public function index(Request $request)
    {

        if (!auth()->user()->can('administrativos.inicio')) {
            return redirect()->route('inicio');
        }


        $gestionActual = date('Y');

        // obtenemos las gestiones distintas existentes
        $gestiones = EstadisticaAdministrativo::select('gestion')
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

        return view('administrador.academico.administrativos', compact('sedes', 'carreras', 'gestionActual', 'gestiones'));
    }


    public function listarAdministrativos(Request $request)
    {
        $gestion = $request->input('fecha', date('Y')); // por defecto el año actual

        $query = EstadisticaAdministrativo::with(['sede' => function ($q) use ($gestion) {

            $q->select(['id','nombre']);
        }])
        ->select(['id','nombre_completo','n_documento','genero','cargo','profesion','servicio','sede_id','estado'])
        ->where('gestion', $gestion)
        ->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {

                $q->orWhereHas('sede', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhere('nombre_completo', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('n_documento', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('genero', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('cargo', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('profesion', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('servicio', 'like', '%' . $request->search['value'] . '%');

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
                'editar' => auth()->user()->can('estudiantes.editar'),

            ],
        ]);
    }

    public function previsualizarAdministrativos(Request $request)
    {
        try {
            $request->validate([
                 'archivo' => 'required|mimes:csv,txt',
            ]);

            // Convertir a colección
            $collection = Excel::toCollection(new \App\Imports\PreviewAdministrativoImport(), $request->file('archivo'))->first();

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
                'sede',
                'genero',
                'gestion',
                'cargo',
                'profesion',
                'servicio'
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


    public function actualizar_registro_administrativo(Request $request, String $id)
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
                'servicio' => 'required|in:planta,contrato,linea',
            ]);

            $administrativo = EstadisticaAdministrativo::find($id);
            $administrativo->nombre_completo = $request->nombreCompleto;
            $administrativo->n_documento = $request->documentoIdentidad;
            $administrativo->genero = $request->genero;
            $administrativo->servicio = $request->servicio;

            $administrativo->save();

            $this->mensaje("exito", "Editado Correctamente");
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {


            $this->mensaje("error", "Error " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function subirDatosAdministrativoscsv(Request $request)
    {
        // 1️⃣ Validar que se suba un archivo válido
        $request->validate([
            'archivo' => 'required|mimes:csv,xlsx,xls',
        ]);

        DB::beginTransaction();

        try {
            // 2️⃣ Cargar el importador
            $import = new AdministrativoImport();

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


    public function generar_reporte_administrativo(Request $request)
    {

        try {

            $tipo = $request->input('tipo');
            $seleccionados = $request->input('seleccionados', []);
            $gestion = $request->input('gestion', date('Y'));

            // Contenedor para los resultados
            $estadisticas = collect();

            // Obtener los datos filtrados


            if ($tipo === 'sede') {

                $servicios = ['contrato', 'planta', 'linea'];

                $estadisticas = DB::table('sedes')
                    ->crossJoin(DB::raw(
                        "(SELECT '" . implode("' AS servicio UNION ALL SELECT '", $servicios) . "') servicios"
                    ))
                    ->leftJoin('estadistica_administrativos', function ($join) use ($gestion) {
                        $join->on('estadistica_administrativos.sede_id', '=', 'sedes.id')
                            ->on('estadistica_administrativos.servicio', '=', 'servicios.servicio')
                            ->where('estadistica_administrativos.gestion', $gestion);
                    })
                    ->select(
                        'sedes.nombre as sede',
                        DB::raw("$gestion as gestion"),
                        'servicios.servicio',
                        DB::raw('COUNT(estadistica_administrativos.id) as total')
                    )
                    ->whereIn('sedes.id', $seleccionados)
                    ->where('sedes.estado', 'activo')
                    ->where('estadistica_administrativos.estado', 'activo')
                    ->groupBy('sedes.nombre', 'servicios.servicio')
                    ->orderBy('sedes.nombre')
                    ->orderBy('servicios.servicio')
                    ->get();
            }

            

            
           if ($tipo === 'servicio') {

                $servicios = $seleccionados;

                $estadisticas = DB::table('sedes')
                    ->crossJoin(DB::raw(
                        "(SELECT '" . implode("' AS servicio UNION ALL SELECT '", $servicios) . "') servicios"
                    ))
                    ->leftJoin('estadistica_administrativos', function ($join) use ($gestion) {
                        $join->on('estadistica_administrativos.sede_id', '=', 'sedes.id')
                            ->on('estadistica_administrativos.servicio', '=', 'servicios.servicio')
                            ->where('estadistica_administrativos.gestion', $gestion);
                    })
                    ->select(
                        'sedes.nombre as sede',
                        DB::raw("$gestion as gestion"),
                        'servicios.servicio',
                        DB::raw('COUNT(estadistica_administrativos.id) as total')
                    )
                    ->where('sedes.estado', 'activo')
                    ->where('estadistica_administrativos.estado', 'activo')
                    ->groupBy('sedes.nombre', 'servicios.servicio')
                    ->orderBy('sedes.nombre')
                    ->orderBy('servicios.servicio')
                    ->get();
            }   

            $nombreCompletoUsuario = auth()
              ->user()
              ->only(['nombres', 'apellidos']);


            // Cargar la vista PDF
            $pdf = \PDF::loadView('administrador.academico.reporteAdministrativo', [
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
