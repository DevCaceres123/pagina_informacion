<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\Rol\StoreRolRequest;
use App\Http\Requests\Usuario\Rol\UpdateRolRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;


class Controlador_rol extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('admin.rol.inicio')) {
            return redirect()->route('inicio');
        }
        $roles      = Role::OrderBy('id', 'desc')->get();
        $permisos   = Permission::OrderBy('id', 'desc')->get();
        return view('administrador.usuarios.roles', [
            'listar_roles'  => $roles,
            'permisos'      => $permisos
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
    public function store(StoreRolRequest $request)
    {
        DB::beginTransaction();
        try {
            $rol = new Role();
            $rol->name = $request->rol;
            $rol->save();

            if ($rol->id) {
                // Sincronizar permisos después de guardar el rol
                $rol->syncPermissions($request->permisos);
                DB::commit();
                return response()->json(
                    mensaje_mostrar('success', 'Se guardó con éxito el Rol y los permisos asignados')
                );
            } else {
                DB::rollBack();
                return response()->json(
                    mensaje_mostrar('error', 'Ocurrió un error al crear un nuevo rol')
                );
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrió un error al crear el rol. Intente nuevamente.'),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $rol = Role::find($id);
            if (!$rol) {
                return response()->json(
                    mensaje_mostrar('error', 'No existe el rol')
                );
            }
            $permisos = $rol->permissions->pluck('name');
            return response()->json(mensaje_mostrar('success', [
                'rol' => $rol->name,
                'permisos' => $permisos
            ]));
        } catch (\Throwable $th) {
            return response()->json(
                mensaje_mostrar('error', 'Ocurrió un error al mostrar y el rol y los permisos. Intente nuevamente.'),
                500
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        try {
            $rol = Role::find($id);
            if (!$rol) {
                return response()->json(
                    mensaje_mostrar('error', 'No existe el rol')
                );
            }
            $permisos = $rol->permissions;
            return response()->json(mensaje_mostrar('success', [
                'rol' => $rol,
                'permisos' => $permisos
            ]));
        } catch (\Throwable $th) {
            return response()->json(mensaje_mostrar('error', 'Ocurrió un error al intentar obtener el rol y permisos'), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRolRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $rol        = Role::find($id);
            $rol->name  = $request->rol;
            $rol->save();
            if ($rol->id) {
                $rol->syncPermissions($request->permisos);
                DB::commit();
                return response()->json(
                    mensaje_mostrar('success', 'Se editó con exito el Rol y los permisos asiganados')
                );
            } else {
                DB::rollBack();
                return response()->json(
                    mensaje_mostrar('error', 'Ocurrio un error al editar el rol y los permisos')
                );
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrió un error al editar el rol. Intente nuevamente.'),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $rol = Role::find($id);
            if (!$rol) {
                return response()->json(mensaje_mostrar('success', 'Rol no encontrado.'));
            }
            $rol->delete();
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'Se elimino con éxito'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio ocurrio un error '), 500);
        }
    }
}
