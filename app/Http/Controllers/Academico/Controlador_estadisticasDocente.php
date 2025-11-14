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
         ->select(['id','nombreCompleto','documentoIdentidad','genero','profesion','grado_academico','carrera_id','sede_id'])
         ->where('gestion', $gestion)
         ->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {

                $q->orWhereHas('sede', function ($sedeQuery) use ($request) {
                    $sedeQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                })
                ->orWhereHas('carrera', function ($sedeQuery) use ($request) {
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
                    Rule::unique('estadistica_docentes', 'documentoIdentidad')->ignore($id),
                ],
                'genero' => 'required|in:masculino,femenino',
                'grado_academico' => 'required|max:100|min:3',
                'profesion' => 'required|max:100|min:3',
            ]);



            $docente = EstadisticaDocente::find($id);
            $docente->nombreCompleto = $request->nombreCompleto;
            $docente->documentoIdentidad = $request->documentoIdentidad;
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


    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
