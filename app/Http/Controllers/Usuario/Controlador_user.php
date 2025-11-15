<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\User\StoreUserRequest;
use App\Http\Requests\Usuario\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class Controlador_user extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (!auth()->user()->can('admin.usuario.inicio')) {
            return redirect()->route('inicio');
        }
        $roles = Role::get();
        return view("administrador.usuarios.usuarios", compact("roles"));
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
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $usuario                = new User();
            $usuario->usuario       = $request->usuario;
            $usuario->password      = Hash::make($request->password);
            $usuario->ci            = $request->ci;
            $usuario->nombres       = $request->nombres;
            $usuario->apellidos     = $request->apellidos;
            $usuario->email         = $request->email;
            $usuario->estado        = 'activo';
            $usuario->save();

            // Seleccionamos el rol específico
            $role = Role::find($request->roles);
            if (!$role) {
                throw new \Exception('Rol no válido');
            }
            $usuario->assignRole($role->name);

            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El usuario se guardó con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Error al guardar usuario: '.$th->getMessage());
            return response()->json(mensaje_mostrar('error', 'Ocurrió un problema inesperado!'));
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        DB::beginTransaction();
        try {
            $usuario = User::find($id);
            $usuario->estado = ($usuario->estado=='activo') ? 'inactivo' : 'activo';
            $usuario->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El estado se cambio con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error al optener los datos')
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $usuario = User::with(['roles'])->find($id);
            if($usuario){
                return response()->json(
                    mensaje_mostrar('success', $usuario)
                );
            }else{
                return response()->json(
                    mensaje_mostrar('error', 'Ocurrio un error al obtener los datos')
                );
            }
        } catch (\Throwable $th) {
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error ')
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $usuario                = User::find($id);
            $usuario->usuario       = $request->usuario;
            if($request->password != null || $request->password != ''){
                $usuario->password      = Hash::make($request->password);
            }
            $usuario->ci            = $request->ci;
            $usuario->nombres       = $request->nombres;
            $usuario->apellidos     = $request->apellidos;
            $usuario->email         = $request->email;
            $usuario->save();

            // Seleccionamos el rol específico
            $role = Role::find($request->roles);
            if (!$role) {
                throw new \Exception('Rol no válido');
            }
            $usuario->syncRoles([$role->name]);

            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El usuario se edito con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error al editar')
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
            $usuario = User::find($id);
            $usuario->delete();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'Se elimino el registro con éxito!')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error inesperado')
            );
        }
    }

   //para listar usuario
    public function listar(){

         // Permisos (para el frontend, si es necesario)
        $permissions = [
            'editar' => auth()->user()->can('admin.usuario.editar'),          
            'eliminar' => auth()->user()->can('admin.usuario.eliminar'),            
            'desactivar' => auth()->user()->can('admin.usuario.desactivar'),
        ];


        //$usuario = User::where('id', '!=', 1)->get();
        $usuario = User::with(['roles'])
        //->where('id', '!=', 1)
        ->where('deleted_at', null)
        ->OrderBy('id','desc')
        ->get();


        $data=[
            'usuario'=>$usuario,
            'permissions'=>$permissions,
        ];
        return response()->json($data);
    }
}
