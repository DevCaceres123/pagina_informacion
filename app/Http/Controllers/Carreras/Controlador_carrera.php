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
        $query = Carrera::select('id', 'nombre', 'modalidad', 'estado', 'malla_curricular_pdf', 'vinculo_web');

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
            $carrera->vinculo_web = $request->vinculo_web;
            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request);
            if ($rutaPdf) {
                $carrera->malla_curricular_pdf = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }


            $carrera->save();

            // asiganar carreras a sedes
            if ($request->has('sede_id') && is_array($request->sede_id)) {
                $carrera->sedes()->attach($request->sede_id);
            }

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

    public function cambiarEstado(Request $request, string $id)
    {

        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $carrera = Carrera::find($id);
            if (!$carrera) {
                throw new Exception('Afiliado no encontrado');
            }
            if ($request->estado == "activo") {
                $carrera->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $carrera->estado = "activo";
            }

            $carrera->save();
            DB::commit();



            $this->mensaje("exito", "Estado cambiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

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
        $carrera = Carrera::with(['sedes' => function ($query) {
            $query->select(['sedes.id', 'nombre']); // Selecciona solo los campos necesarios
        }])->select('id', 'nombre', 'modalidad', 'estado', 'malla_curricular_pdf', 'vinculo_web')->first();

        if (!$carrera) {
            $this->mensaje('error', 'Sede no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $carrera);

        return response()->json($this->mensaje, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_carrera)
    {
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $carrera = Carrera::find($id_carrera);
            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }

            $carrera->nombre = $request->nombre;
            $carrera->modalidad	= $request->modalidad;

            $carrera->vinculo_web = $request->vinculo_web;

            $carrera->save();
            DB::commit();

            $this->mensaje("exito", "Carrera editada Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function actualizar_malla(Request $request)
    {
        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($request->id); // más seguro
            $borrarArchivo = $carrera->malla_curricular_pdf;
            $nombreArchivo = null;

            if ($request->hasFile('malla_curricular')) {


                // Guardar nuevo archivo
                $archivo = $request->file('malla_curricular');
                $ruta = $archivo->store('mallas_curriculares', 'public');
                $nombreArchivo = basename($ruta);

                // Asignar nuevo nombre de archivo
                $carrera->malla_curricular_pdf = $nombreArchivo;
                $carrera->save();
            }

            DB::commit();

            // Eliminar archivo anterior si se guardo una nueva mmalla
            Storage::disk('public')->delete('mallas_curriculares/' . $borrarArchivo);

            $this->mensaje('exito', 'Malla Curricular actualizada correctamente');
            return response()->json($this->mensaje, 200);

        } catch (\Exception $e) {
            DB::rollBack();

            // // Si se subió un nuevo archivo, eliminarlo manualmente
            if ($nombreArchivo) {
                Storage::disk('public')->delete('mallas_curriculares/' . $nombreArchivo);
            }

            $this->mensaje('error', 'Error al actualizar la malla curricular: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
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
        DB::beginTransaction();
        try {
            $carrera = Carrera::find($id);
            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }

            $carrera->delete();

            DB::commit();

            $this->mensaje("exito", "Carrera eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function listarSedesCarrera($id_carrera)
    {
        try {
            $carrera = Carrera::with('sedes')->find($id_carrera);

            if (!$carrera) {
                throw new Exception('Carrera no encontrado');
            }


            // Todas las sedes disponibles
            $todasSedes = Sede::all();

            // IDs de las sedes que tiene esta carrera
            $sedesCarreraIds = $carrera->sedes->pluck('id')->toArray();

            // Armamos una lista con un campo extra "asignada"
            $sedesConEstado = $todasSedes->map(function ($sede) use ($sedesCarreraIds) {
                return [
                    'id' => $sede->id,
                    'nombre' => $sede->nombre,
                    'asignada' => in_array($sede->id, $sedesCarreraIds)
                ];
            });


            $this->mensaje('exito', $sedesConEstado);
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            $this->mensaje('error', 'Error al obtener las sedes: ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Asignar una sede a una carrera
     */

    public function asignarSede(Request $request, string $carreraId)
    {
        
        $carrera = Carrera::findOrFail($carreraId);
        $carrera->sedes()->attach($request->sede_id);
        return response()->json(['tipo' => 'exito', 'mensaje' => 'Sede asignada correctamente']);
    }

    public function quitarSede(Request $request, string $carreraId)
    {
        $carrera = Carrera::findOrFail($carreraId);
        $carrera->sedes()->detach($request->sede_id);
        return response()->json(['tipo' => 'exito', 'mensaje' => 'Sede quitada correctamente']);
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
