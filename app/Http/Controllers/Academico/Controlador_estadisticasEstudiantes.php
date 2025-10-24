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
        // Si el usuario no selecciona gestión, usamos el año actual
        $gestion = $request->input('gestion', date('Y'));

        // Cargamos todas las carreras con sus sedes y estadísticas de la gestión seleccionada
        $carreras = Carrera::with(['sedes', 'estadisticas' => function ($q) use ($gestion) {
            $q->where('gestion', $gestion);
        }])->get();


        $sedes = Sede::where('estado', 'activo')->get();

        return view('administrador.academico.estudiantes', compact('carreras','sedes'));
    }

    public function listarEstudiantes(Request $request)
    {
        $query = Carrera::with(['sedes', 'estadisticas' => function ($q) {
            // $q->where('gestion', $gestion);
            $q->select(['id','cantidad_hombres','cantidad_mujeres','total','carrera_id']);
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

            $anio = date('Y');

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
