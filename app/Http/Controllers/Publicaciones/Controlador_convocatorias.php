<?php

namespace App\Http\Controllers\Publicaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use App\Models\Sede;
use App\Models\Convocatoria;
use App\Models\CategoriasNoticia; // tambien utilizada para las categorias de convocatorias
use Illuminate\Support\Facades\DB;

class Controlador_convocatorias extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view('administrador.publicaciones.convocatorias');
    }

    public function listarConvocatorias(Request $request)
    {
        $query = Convocatoria::with([
            'categoria' => function ($query) {
                $query->select(['id','nombre']);
            },
        ])->select('id', 'titulo', 'estado', 'created_at','categoria_id')->orderBy('id', 'desc');


        if (!empty($request->search['value'])) {

            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search['value'] . '%')
                  ->where('estado', 'like', '%' . $request->search['value'] . '%')                  
                  ->orWhereHas('categoria', function ($sedeQuery) use ($request) {
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
        
        DB::beginTransaction();
        try {
            
            $convocatoria=Convocatoria::find($id);

            if(!$convocatoria){
               throw new Exception('convocatoria no encontrado');
            }
            $convocatoria->delete();
            DB::commit();
        
            $this->mensaje("exito", "Eliminado Correctamente");
            return response()->json($this->mensaje, 200);
        
        } catch (Exception $e) {
            DB::rollBack();
        
            $this->mensaje("error", "error " . $e->getMessage());
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
