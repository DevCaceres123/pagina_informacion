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


        $rol2       = new Role();
        $rol2->name = 'cordinador';
        $rol2->save();


        $rol3       = new Role();
        $rol3->name = 'encargado';
        $rol3->save();


        $usuario = new User();
        $usuario->usuario = 'admin';
        $usuario->password = Hash::make('1234');
        $usuario->ci = '1234567890';
        $usuario->nombres = 'Admin';
        $usuario->apellidos = 'admin admin';
        $usuario->estado = 'activo';
        $usuario->email = 'admin@gmail.com';
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

        $usuario2->syncRoles(['encargado']);

        // PERMISOS PARA INCIO DEL SISTEMA
        Permission::create(['name' => 'inicio'])->syncRoles([$rol1,$rol2,$rol3]);
        Permission::create(['name' => 'inicio.usuarios'])->syncRoles([$rol1,$rol2,$rol3]);
        Permission::create(['name' => 'inicio.carreras'])->syncRoles([$rol1,$rol2,$rol3]);
        Permission::create(['name' => 'inicio.sedes'])->syncRoles([$rol1,$rol2,$rol3]);
        Permission::create(['name' => 'inicio.ultima_noticia'])->syncRoles([$rol1,$rol2,$rol3]);
        Permission::create(['name' => 'inicio.grafica_estudiantes_sede_carrera'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'inicio.grafica_titulados'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'inicio.grafica_estudiantes'])->syncRoles([$rol1,$rol2]);

        // PERMISOS PARA ADMINISTRADOR DE  USUARIOS
        Permission::create(['name' => 'admin'])->syncRoles([$rol1]);

        // USAURIO
        Permission::create(['name' => 'admin.usuario.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.usuario.crear'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.editar'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.eliminar'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.desactivar'])->assignRole($rol1);    


         //ROL
        Permission::create(['name' => 'admin.rol.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.visualizar'])->syncRoles([$rol1]);


        //PERMISOS
        Permission::create(['name' => 'admin.permiso.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.eliminar'])->syncRoles([$rol1]);

        // CARRERA y SEDE
        Permission::create(['name' => 'sede_carrea'])->syncRoles([$rol1,$rol2]);
        
        //permisos para sede
        Permission::create(['name' => 'sede.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'sede.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'sede.desactivar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'sede.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'sede.editar'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'sede.ver_carreras'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'sede.ver_resolucion'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'sede.actualizar_imagenes'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'sede.agregar_rutas'])->syncRoles([$rol1,$rol2]);      

        // permisos para carrera
        Permission::create(['name' => 'carrera.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'carrera.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'carrera.ver_sedes'])->syncRoles([$rol1]);
        Permission::create(['name' => 'carrera.desactivar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'carrera.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'carrera.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'carrera.ver_malla'])->syncRoles([$rol1]);


        // PUBLICACIONES NOTICIAS Y CONVOCATORIAS
        Permission::create(['name' => 'publicaciones'])->syncRoles([$rol1,$rol2]);


        //permisos para noticias
        Permission::create(['name' => 'noticia.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'noticia.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'noticia.noticia_destacada'])->syncRoles([$rol1]);
        Permission::create(['name' => 'noticia.publicar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'noticia.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'noticia.editar'])->syncRoles([$rol1]);


        //permisos para convocatorias
        Permission::create(['name' => 'convocatoria.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'convocatoria.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'convocatoria.publicar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'convocatoria.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'convocatoria.editar'])->syncRoles([$rol1]);


        // INFRAESTRUCTURA
        Permission::create(['name' => 'infraestructura.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'infraestructura.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.planos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.ver_documentos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.cambiar_estado'])->syncRoles([]);
        Permission::create(['name' => 'infraestructura.datos_ubicacion'])->syncRoles([$rol1]);
        Permission::create(['name' => 'infraestructura.generar_reporte'])->syncRoles([$rol1]);

        // ACADEMICO
        Permission::create(['name' => 'academico'])->syncRoles([$rol1,$rol2]);

        // permiso para los estudiantes
        Permission::create(['name' => 'estudiantes.inicio'])->syncRoles([]);
        Permission::create(['name' => 'estudiantes.subir_datos'])->syncRoles([]);
        Permission::create(['name' => 'estudiantes.generar_reporte'])->syncRoles([]);
        Permission::create(['name' => 'estudiantes.editar'])->syncRoles([]);
        Permission::create(['name' => 'estudiantes.editar_url'])->syncRoles([]);
        Permission::create(['name' => 'estudiantes.ingresar'])->syncRoles([]);

        // permiso para los titulados
        Permission::create(['name' => 'titulados.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'titulados.subir_datos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'titulados.generar_reporte'])->syncRoles([$rol1]);
        Permission::create(['name' => 'titulados.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'titulados.editar_url'])->syncRoles([$rol1]);
        Permission::create(['name' => 'titulados.ingresar'])->syncRoles([$rol1]);

          // permiso para los docentes
        Permission::create(['name' => 'docentes.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'docentes.subir_datos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'docentes.generar_reporte'])->syncRoles([$rol1]);
        Permission::create(['name' => 'docentes.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'docentes.editar_url'])->syncRoles([$rol1]);
        Permission::create(['name' => 'docentes.ingresar'])->syncRoles([$rol1]);

          // permiso para administrativos
        Permission::create(['name' => 'administrativos.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'administrativos.subir_datos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'administrativos.generar_reporte'])->syncRoles([$rol1]);
        Permission::create(['name' => 'administrativos.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'administrativos.editar_url'])->syncRoles([$rol1]);
        Permission::create(['name' => 'administrativos.ingresar'])->syncRoles([$rol1]);

        // permisos para seguimiento individual de estudiantes
        Permission::create(['name' => 'seguimiento_estudiantes.inicio'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'seguimiento_estudiantes.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.documentos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.subir_datos'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.generar_reporte'])->syncRoles([$rol1]);
        Permission::create(['name' => 'seguimiento_estudiantes.ficha'])->syncRoles([$rol1, $rol2]);


    }
}
