<?php

use App\Http\Controllers\Paginas\Controlador_pagina;
use App\Http\Controllers\Usuario\Controlador_login;
use App\Http\Controllers\Usuario\Controlador_permisos;
use App\Http\Controllers\Usuario\Controlador_rol;
use App\Http\Controllers\Usuario\Controlador_user;
use App\Http\Controllers\Usuario\Controlador_usuario;
use App\Http\Controllers\Sedes\Controlador_sedes;
use App\Http\Controllers\Carreras\Controlador_carrera;
use App\Http\Middleware\Autenticados;
use App\Http\Middleware\No_autenticados;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;

Route::get('/', function () {
    return view('plantilla_web/paginas/inicio');
})->name('login');


Route::controller(Controlador_pagina::class)->group(function () {
    Route::get('/noticias/{id}', 'noticias')->name('noticias.show');
    Route::get('/sedes/{id}', 'sedes')->name('pagina.sedes');
    Route::get('/inicio', 'inicio')->name('pagina.inicio');
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
