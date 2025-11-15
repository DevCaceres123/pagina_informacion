<?php

use App\Http\Controllers\Paginas\Controlador_pagina;
use App\Http\Controllers\Usuario\Controlador_login;
use App\Http\Controllers\Usuario\Controlador_permisos;
use App\Http\Controllers\Usuario\Controlador_rol;
use App\Http\Controllers\Usuario\Controlador_user;
use App\Http\Controllers\Usuario\Controlador_usuario;
use App\Http\Controllers\Sedes\Controlador_sedes;
use App\Http\Controllers\Carreras\Controlador_carrera;
use App\Http\Controllers\Infraestructuras\Controlador_infraestructura;
use App\Http\Controllers\Publicaciones\Controlador_noticias;
use App\Http\Controllers\Publicaciones\Controlador_convocatorias;
use App\Http\Controllers\Academico\Controlador_estadisticasEstudiantes;
use App\Http\Controllers\Academico\Controlador_estadisticasTitulados;
use App\Http\Controllers\Academico\Controlador_estadisticasDocente;
use App\Http\Controllers\Academico\Controlador_estadisticasAdministrativo;
use App\Http\Middleware\Autenticados;
use App\Http\Middleware\No_autenticados;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use App\Models\Noticia;
use App\Models\ImgNoticia;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {

    $noticias = Noticia::with([
         'sede' => function ($q) {
             $q->select('id', 'nombre'); // solo traigo id y nombre de la sede
         },
         'categoria' => function ($q) {
             $q->select('id', 'nombre'); // solo id y nombre de la categoría
         },
          'usuario' => function ($q) {
              $q->select('id', 'nombres', 'apellidos'); // solo traigo id y nombre de la sede
          },
         'imagenesNoticia' => function ($q) {
             $q->select(['imagen', 'noticia_id']);
         }

         ])->select('id', 'titulo', 'contenido', 'url_video', 'sede_id', 'categoria_id', 'user_id', 'created_at')
           ->where('estado_noticia', 'activo')
           ->orderBy('id', 'desc')
           ->limit(3)
           ->get();

        $poligonos = DB::table('ubicacion_sedes')
            ->selectRaw("id, ubicacion, ST_AsGeoJSON(poligono)::json as geometry")            
            ->whereNotNull('poligono')
            ->whereNull('deleted_at')  // listamos todos aquellos que no se hayan eliminado
            ->get()
            ->map(function ($p) {
                $p->geometry = json_decode($p->geometry);
                return $p;
        });
    return view('plantilla_web/paginas/inicio', compact('noticias','poligonos'));
})->name('login');


Route::controller(Controlador_pagina::class)->group(function () {
    Route::get('/noticias', 'noticias')->name('noticias.show');
    Route::get('/detalleNoticia/{id}', 'noticia')->name('noticia.detalleNoticia');
    Route::get('/convocatorias', 'convocatorias')->name('convocatorias.show');
    Route::get('/deatelleConvocatoria/{id}', 'convocatoria')->name('convocatoria.deatelleConvocatoria');
    Route::get('/sedes/{id}', 'sedes')->name('pagina.sedes');
    Route::get('/inicio', 'inicio')->name('pagina.inicio');
    Route::post('/buscarCarrera', 'buscarCarrera')->name('pagina.buscarCarrera');
});


Route::prefix('/')->middleware([No_autenticados::class])->group(function () {


    Route::get('/login', function () {
        return view('login', ['fromHome' => true]);
    })->name('login_home');

    Route::controller(Controlador_login::class)->group(function () {
        Route::post('ingresar', 'ingresar')->name('log_ingresar');
    });
});


Route::prefix('/admin')->middleware([Autenticados::class])->group(function () {
    Route::controller(Controlador_login::class)->group(function () {
        Route::get('inicio', 'inicio')->name('inicio');
        Route::post('cerrar_session', 'cerrar_session')->name('salir');
    });

    // CONTROLADOR PARA LOS USAURIOS
    Route::controller(Controlador_usuario::class)->group(function () {
        Route::get('perfil', 'perfil')->name('perfil');
        Route::post('pwd_guardar', 'password_guardar')->name('pwd_guardar');
    });


    // CONTROLADOR PARA LAS SEDES
    Route::controller(Controlador_sedes::class)->group(function () {
        Route::resource('sedes', Controlador_sedes::class);
        Route::get('listarSedes', 'listarSedes')->name('sede.listar');
        Route::post('resolucion/{id}/actualizar_pdf', 'actualizar_pdf')->name('sede.pdf');
        Route::get('listarImagenes/{id_sede}', 'listarImagenes')->name('sede.listarImagenes');
        Route::post('agregarImagenes/{id_sede}', 'agregarImagenes')->name('sede.agregarImagenes');
        Route::delete('eliminarImagen/{id_sede}', 'eliminarImagen')->name('sede.eliminarImagen');
        Route::post('actualizarDatos', 'actualizarDatos')->name('sede.actualizarDatos');
        Route::get('ubicacionSede/{id_sede}', 'ubicacionSede')->name('sede.ubicacionSede');
        Route::post('guardarUbicaciones', 'guardarUbicaciones')->name('sede.guardarUbicaciones');
        Route::put('eliminarUbicacion/{id_ubicacion}', 'eliminarUbicacion')->name('sede.eliminarUbicacion');
        Route::put('actualizarUbicacion/{id_ubicacion}', 'actualizarUbicacion')->name('sede.eliminarUbicacion');
    });


    // CONTROLADOR PARA LAS CARRERAS
    Route::controller(Controlador_carrera::class)->group(function () {
        Route::resource('carrera', Controlador_carrera::class);
        Route::get('listarCarreras', 'listarCarreras')->name('carrera.listarCarreras');
        Route::put('cambiarEstado/{id_carrera}', 'cambiarEstado')->name('carrera.cambiarEstado');
        Route::post('malla/{id_carrera}/actualizar_malla', 'actualizar_malla')->name('carrera.actualizar_malla');
        Route::get('listarSedesCarrera/{id_carrera}', 'listarSedesCarrera')->name('carrera.listarSedesCarrera');
        Route::put('asignar_sede/{id_carrera}', 'asignarSede')->name('carrera.asignarSede');
        Route::put('quitar_sede/{id_carrera}', 'quitarSede')->name('carrera.quitarSede');

    });

    // CONTROLADOR PARA LAS INFRAESTRUCTURAS
    Route::controller(Controlador_infraestructura::class)->group(function () {
        Route::resource('infraestructura', Controlador_infraestructura::class);
        Route::get('listarInfraestructuras', 'listarInfraestructuras')->name('infraestructura.listarInfraestructuras');
        Route::get('estadoTramite/{id_infraestructura}', 'estadoTramite')->name('infraestructura.estadoTramite');
        Route::post('cambiarEstadoTramite', 'cambiarEstadoTramite')->name('infraestructura.cambiarEstadoTramite');
        route::get('docuementosInfraestructura/{id_infraestructura}', 'docuementosInfraestructura')->name('infraestructura.docuementosInfraestructura');
        Route::get('verDocumentos/{tipo}{id}', 'verDocumentos')->name('infraestructura.verDocumentos');
        Route::post('guardarDocumentos', 'guardarDocumentos')->name('infraestructura.guardarDocumentos');
        Route::post('datosUbicacion', 'datosUbicacion')->name('infraestructura.datosUbicacion');
        Route::post('guardarDatosUbicacion', 'guardarDatosUbicacion')->name('infraestructura.guardarDatosUbicacion');
        Route::get('listarImagenesPlanos/{id_infraestructura}', 'listarImagenesPlanos')->name('infraestructura.listarImagenesPlanos');
        Route::get('planos/{id_infraestructura}', 'verPlano')->name('infraestructura.planosVer');
        Route::post('agregarImagenesPlanos/{id_infraestructura}', 'agregarImagenesPlanos')->name('infraestructura.agregarImagenesPlanos');
        Route::delete('eliminarImagenPlano/{id_imagen}', 'eliminarImagenPlano')->name('infraestructura.eliminarImagenPlano');
        Route::post('actualizarInfraestructura', 'actualizarInfraestructura')->name('infraestructura.actualizarInfraestructura');
        Route::get('reporteInfraestructura/{id_infraestructura}', 'reporteInfraestructura')->name('infraestructura.reporteInfraestructura');
    });


    // CONTROLADOR PARA LAS NOTICIAS
    Route::controller(Controlador_noticias::class)->group(function () {
        Route::resource('noticia', Controlador_noticias::class);
        Route::get('nuevaNoticia', 'nuevaNoticia')->name('noticia.nuevaNoticia');
        Route::get('listarNoticias', 'listarNoticias')->name('noticia.listarNoticias');
        Route::put('cambiar_estado_destacado/{id_noticia}', 'cambiar_estado_destacado')->name('noticia.cambiar_estado_destacado');
        Route::put('cambiar_estado_publicacion/{id_noticia}', 'cambiar_estado_publicacion')->name('noticia.cambiar_estado_publicacion');
        Route::get('editarNoticia/{id_noticia}', 'editarNoticia')->name('noticia.editarNoticia');
        Route::delete('eliminarImagenNoticia/{id_imagen}', 'eliminarImagenNoticia')->name('noticia.eliminarImagenNoticia');
        Route::post('actualizarNoticia/{id_noticia}', 'actualizarNoticia')->name('noticia.actualizarNoticia');
    });

    // CONTROLADOR PARA LAS CONVOCATORIAS
    Route::controller(Controlador_convocatorias::class)->group(function () {
        Route::resource('convocatoria', Controlador_convocatorias::class);
        Route::get('listarConvocatorias', 'listarConvocatorias')->name('convocatoria.listarNoticias');
        Route::get('nuevaConvocatoria', 'nuevaConvocatoria')->name('convocatoria.nuevaConvocatoria');
        Route::get('editarConvocatoria/{id_convocatoria}', 'editarConvocatoria')->name('convocatoria.editarConvocatoria');
        Route::delete('eliminarImagenConvocatoria/{id_convocatoria}', 'eliminarImagenConvocatoria')->name('convocatoria.eliminarImagenConvocatoria');
        Route::post('actualizarConvocatoria/{id_convocatoria}', 'actualizarConvocatoria')->name('convocatoria.actualizarConvocatoria');
        Route::put('cambiar_estado_convocatoria/{id_noticia}', 'cambiar_estado_convocatoria')->name('convocatoria.cambiar_estado_convocatoria');
      
    });


    // CONTROLADOR PARA LA ESTADISTICA DE LOS ESTUDIANTES
    Route::controller(Controlador_estadisticasEstudiantes::class)->group(function () {
        Route::resource('estudiantes', Controlador_estadisticasEstudiantes::class);
        Route::get('listarEstudiantes', 'listarEstudiantes')->name('estudiantes.listarEstudiantes');
        Route::put('actualizar_registro_estudiante/{id_carrera}', 'actualizar_registro_estudiante')->name('estudiantes.actualizar_registro_estudiante');
        Route::post('generar_reporte_estudiante', 'generar_reporte_estudiante')->name('estudiantes.generar_reporte_estudiante');
        Route::post('previsualizarExcel', 'previsualizarExcel')->name('estudiantes.previsualizarExcel');
        Route::post('subirDatosEstudiantecsv', 'subirDatosEstudiantecsv')->name('estudiantes.subirDatosEstudiantecsv');
        
      
    });



    // CONTROLADOR PARA LA ESTADISTICA DE LOS TITULADOS
    Route::controller(Controlador_estadisticasTitulados::class)->group(function () {
        Route::resource('titulados', Controlador_estadisticasTitulados::class);
        Route::get('listarTitulados', 'listarTitulados')->name('titulados.listarTitulados');
        Route::put('actualizar_registro_titulado/{id_carrera}', 'actualizar_registro_titulado')->name('titulados.actualizar_registro_titulado');
        Route::post('previsualizarTitulados', 'previsualizarTitulados')->name('titulados.previsualizarTitulados');
        Route::post('subirDatosTituladoscsv', 'subirDatosTituladoscsv')->name('titulados.subirDatosTituladoscsv');      
        Route::get('listarFechasColacion', 'listarFechasColacion')->name('titulados.listarFechasColacion');
        Route::post('generar_reporte_titulados', 'generar_reporte_titulados')->name('titulados.generar_reporte_titulados');     
    });


    // CONTROLADOR PARA LA ESTADISTICA DE LOS DOCENTES
    Route::controller(Controlador_estadisticasDocente::class)->group(function () {
        Route::resource('docentes', Controlador_estadisticasDocente::class);
        Route::get('listarDocentes', 'listarDocentes')->name('docentes.listarDocentes');
        Route::put('actualizar_registro_docente/{id_titulado}', 'actualizar_registro_docente')->name('docentes.actualizar_registro_titulado');
        Route::post('previsualizarDocentes', 'previsualizarDocentes')->name('docentes.previsualizarDocentes');
        Route::post('subirDatosDocentescsv', 'subirDatosDocentescsv')->name('docentes.subirDatosDocentescsv');
        Route::post('generar_reporte_docente', 'generar_reporte_docente')->name('docentes.generar_reporte_docente');  
        
    });


    // CONTROLADOR PARA LA ESTADISTICA DE LOS ADMINISTRATIVOS
    Route::controller(Controlador_estadisticasAdministrativo::class)->group(function () {
      Route::resource('administrativos', Controlador_estadisticasAdministrativo::class);
    
      
    });



    //PARA LOS PERMISOS
    Route::resource('permisos', Controlador_permisos::class);
    Route::post('/permisos/listar', [Controlador_permisos::class, 'listar'])->name('permisos.listar');

    //PARA EL ROL
    Route::resource('roles', Controlador_rol::class);

    //para la administracion de usuarios
    Route::resource('user', Controlador_user::class);
    Route::post('/user/listar', [Controlador_user::class, 'listar'])->name('user.listar');
});


Route::get('/test-image', function () {
    // Ruta a una imagen de prueba que tengas en public/img
    $ruta = public_path('test.jpg');

    // Si no tienes una imagen de prueba, crea una en blanco:
    if (!file_exists($ruta)) {
        $img = Image::canvas(800, 600, '#cccccc');
        $img->text('TEST IMAGE', 400, 300, function ($font) {
            $font->file(public_path('fonts/arial.ttf')); // opcional, si tienes
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        $img->save($ruta);
    }

    // Prueba Intervention Image
    $img = Image::make($ruta)
        ->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('jpg', 80);

    return $img->response('jpg');
});
