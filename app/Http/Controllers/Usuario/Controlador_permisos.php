<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\Permiso\StorePermisoRequest;
use App\Http\Requests\Usuario\Permiso\UpdatePermisoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class Controlador_permisos extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('admin.permiso.inicio')) {
            return redirect()->route('inicio');
        }
        return view('administrador.usuarios.permisos');
    }


    /**
     * para listar
     */
    public function listar()
    {
        // Permisos (para el frontend, si es necesario)
        $permissions = [
            'editar' => auth()->user()->can('admin.permiso.editar'),          
            'eliminar' => auth()->user()->can('admin.permiso.eliminar'),
            'desactivar' => auth()->user()->can('admin.permiso.desactivar'),
        ];

        $listar_permiso = Permission::OrderBy('id', 'asc')->get();

         $data=[
            'listar_permiso'=>$listar_permiso,
            'permissions'=>$permissions,
        ];
        return response()->json($data);

    }
    /**
     *Fin de listar
     */

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
    public function store(StorePermisoRequest $request)
    {
        DB::beginTransaction();
        try {
            $nuevo_permiso = new Permission();
            $nuevo_permiso->name = $request->permiso;
            $nuevo_permiso->save();
            // Confirma la transacción
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'El permiso se agrego con éxito'));
        } catch (\Exception $e) {
            // Si ocurre un error, deshacer todos los cambios realizados en la transacción
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio un error al crear nuevo Permiso'), 500);
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
        try {
            $permiso = Permission::find($id);
            if (!$permiso) {
                return response()->json(
                    mensaje_mostrar('error', 'No exite el permiso')
                );
            }
            return response()->json(mensaje_mostrar('success', $permiso));
        } catch (\Throwable $th) {
            return response()->json(mensaje_mostrar('error', 'Ocurrio ocurrio un error '), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermisoRequest $request, string $id)
    {
        $permiso = Permission::find($id);
        if (!$permiso) {
            return response()->json(mensaje_mostrar('error', 'Ocurrio un problema'), 404);
        }

        $permiso->name = $request->permiso;
        $permiso->save();

        return response()->json(mensaje_mostrar('success', 'El permiso se actualizo con éxito'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $permiso = Permission::find($id);
            // Validar si el permiso existe
            if (!$permiso) {
                return response()->json(mensaje_mostrar('success', 'Permiso no encontrado.'));
            }
            $permiso->delete();
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'Se elimino con éxito'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio ocurrio un error '), 500);
        }
    }
}
