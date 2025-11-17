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
        ->select(['id','nombre_completo','n_documento','genero','cargo','profesion','servicio','sede_id'])
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


    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }

}
