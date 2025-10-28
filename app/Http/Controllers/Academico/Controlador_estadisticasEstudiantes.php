<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Carrera;
use App\Models\EstadisticaEstudiante;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class Controlador_estadisticasEstudiantes extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $gestionActual = date('Y');

        // obtenemos las gestiones distintas existentes
        $gestiones = EstadisticaEstudiante::select('gestion')
            ->distinct()
            ->where('gestion', '!=', $gestionActual)
            ->orderByDesc('gestion')
            ->pluck('gestion'); // devuelve solo los valores de la columna


        $sedes = Sede::where('estado', 'activo')->get();

        // Cargamos todas las carreras con sus sedes y estadísticas de la gestión seleccionada
        $carreras = Carrera::with(['sedes', 'estadisticas' => function ($q) {
            // $q->where('gestion', $gestion);
        }])->get();


        $sedes = Sede::where('estado', 'activo')->get();

        return view('administrador.academico.estudiantes', compact('sedes', 'carreras', 'gestionActual', 'gestiones'));
    }

    public function listarEstudiantes(Request $request)
    {
        $gestion = $request->input('fecha', date('Y')); // por defecto el año actual

        $query = Carrera::with(['sedes', 'estadisticas' => function ($q) use ($gestion) {

            $q->select(['id','cantidad_hombres','cantidad_mujeres','total','carrera_id'])
              ->where('gestion', $gestion);
        }])->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')
                  ->orWhereHas('sedes', function ($sedeQuery) use ($request) {
                      $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                  });
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

    public function actualizar_registro_estudiante(Request $request, String $id)
    {
        DB::beginTransaction();
        try {

            $anio = $request->gestion;

            // updateOrCreate: busca por condiciones y si existe actualiza, si no crea
            $estadistica = EstadisticaEstudiante::updateOrCreate(
                [
                    'carrera_id' => $id,  // condición de búsqueda
                    'gestion' => $anio,   // año actual
                ],
                [
                    'cantidad_hombres' => $request->hombres ?? 0,
                    'cantidad_mujeres' => $request->mujeres ?? 0,
                    'total' => ($request->hombres ?? 0) + ($request->mujeres ?? 0),
                ]
            );

            DB::commit();

            $this->mensaje("exito", "Registro actualizado correctamente.");
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 500);
        }
    }


    public function generar_reporte_estudiante(Request $request)
    {
        try {

            $tipo = $request->input('tipo');
            $seleccionados = $request->input('seleccionados', []);
            $gestion = $request->input('gestion', date('Y'));

            // Contenedor para los resultados
            $estadisticas = collect();


            // 🔹 Reporte por CARRERA
            if ($tipo === 'carrera') {
                $estadisticas = EstadisticaEstudiante::with('carrera')
                    ->whereIn('carrera_id', $seleccionados)
                    ->where('gestion', $gestion)
                    ->get();

                $pdf = \PDF::loadView('administrador.academico.reporteEstudiante', [
                    'tipo' => 'carrera',
                    'estadisticas' => $estadisticas,
                    'gestion' => $gestion
                ]);
            }

            if ($request->tipo === 'sede') {

                // Obtener las carreras asociadas a las sedes seleccionadas
                $carrerasPorSede = DB::table('carrera_sede')
                    ->whereIn('sede_id', $seleccionados)
                    ->get()
                    ->groupBy('sede_id');

                $resumenSedes = [];

                foreach ($carrerasPorSede as $sedeId => $carreras) {
                    $carrerasIds = $carreras->pluck('carrera_id');

                    // Obtener estadísticas de esas carreras
                    $estadisticas = EstadisticaEstudiante::whereIn('carrera_id', $carrerasIds)
                        ->where('gestion', $gestion)
                        ->get();

                    // Hacer sumatorias de campos numéricos
                    $total_hombres = $estadisticas->sum('cantidad_hombres');
                    $total_mujeres = $estadisticas->sum('cantidad_mujeres');
                    $total_general = $estadisticas->sum('total');

                    $sede = Sede::find($sedeId);

                    $resumenSedes[] = [
                        'sede' => $sede->nombre ?? 'Sin nombre',
                        'total_hombres' => $total_hombres,
                        'total_mujeres' => $total_mujeres,
                        'total_general' => $total_general,
                    ];
                }

                
                $pdf = \PDF::loadView('administrador.academico.reporteEstudiante', [
                    'tipo' => 'sede',
                    'resumenSedes' => $resumenSedes,
                    'gestion' => $gestion
                ]);
            }

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }



    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
