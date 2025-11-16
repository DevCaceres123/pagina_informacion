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
        $gestiones = EstadisticaEstudiante::select('gestion')
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
}
