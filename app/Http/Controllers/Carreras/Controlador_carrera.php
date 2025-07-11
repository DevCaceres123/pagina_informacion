<?php

namespace App\Http\Controllers\Carreras;

use App\Models\Sede;
use App\Models\Carrera;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Controlador_carrera extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = Sede::all();
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
}
