<?php

namespace App\Http\Controllers\Carreras;

use App\Models\Sede;
use App\Models\Carrera;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Carrera\CarrerasRequest;


class Controlador_carrera extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = Sede::all()->where('estado', 'activo');
        return view('administrador.carreras.carreras', compact('sedes'));
    }


    public function listarCarreras(Request $request)
    {
        $query = Carrera::with([
            'sede' => function ($query) {
                $query->select(['nombre', 'id']); // CORREGIDO
            },
        ])->select('id', 'nombre', 'modalidad', 'estado', 'malla_curricular_pdf', 'sede_id')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')->orWhere('modalidad', 'like', '%' . $request->search['value'] . '%');
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
    public function store(CarrerasRequest $request)
    {
        
        DB::beginTransaction();

        try {
            // Guardar la sede
            $carrera = new Carrera();
            $carrera->nombre = $request->nombre;
            $carrera->modalidad = $request->modalidad;
            $carrera->estado = 'activo';
            $carrera->usuario_id = auth()->user()->id;
            $carrera->sede_id = $request->sede_id;
            $carrera->vinculo_web = $request->vinculo_web;
            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request);
            if ($rutaPdf) {
                $carrera->malla_curricular_pdf = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }

            
            $carrera->save();


            DB::commit();

            $this->mensaje('exito', 'Carrera registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                if (Storage::disk('public')->exists('mallas_curriculares/'. $ruta)) {
                    Storage::disk('public')->delete('mallas_curriculares/'. $ruta);
                }
            }

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
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


    public function guardarPdf(Request $request)
    {
        if ($request->hasFile('malla_curricular')) {
            $archivo = $request->file('malla_curricular');
            $ruta = $archivo->store('mallas_curriculares', 'public'); // se guarda en storage/app/public/mallas_curriculares
            return str_replace('mallas_curriculares/', '', $ruta); // devuelve: resoluciones/archivo.pdf
        }
        return null;
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
