<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $rol1       = new Role();
        $rol1->name = 'administrador';
        $rol1->save();

        $usuario = new User();
        $usuario->usuario = 'admin';
        $usuario->password = Hash::make('rodry');
        $usuario->ci = '10028685';
        $usuario->nombres = 'Admin';
        $usuario->apellidos = 'admin admin';
        $usuario->estado = 'activo';
        $usuario->email = 'rodrigo@gmail.com';
        $usuario->save();

        $usuario->syncRoles(['administrador']);

        $usuario2 = new User();
        $usuario2->usuario = 'gloria_123';
        $usuario2->password = Hash::make('1234');
        $usuario2->ci = '10028685';
        $usuario2->nombres = 'GLORIA';
        $usuario2->apellidos = 'RAMOS BLANCO';
        $usuario2->estado = 'activo';
        $usuario2->email = 'gloriaramosblanco9@gmail.com';
        $usuario2->save();

        $usuario2->syncRoles(['administrador']);
    }
}
