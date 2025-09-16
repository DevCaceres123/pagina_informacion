@extends('index')
@section('titulo', 'PERFIL')
@section('contenido')
    <section class="py-0">
        <div class="swiper theme-slider min-vh-100"
            data-swiper='{"loop":true,"allowTouchMove":false,"autoplay":{"delay":5000},"effect":"fade","speed":800}'>

            <div class="swiper-wrapper">

                <!-- Slide 1: MISIÓN -->
                <div class="swiper-slide position-relative" data-zanim-timeline="{}">
                    <div class="bg-holder" style="background-image:url(pagina_template/assets/img/perfil1.png);">
                    </div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: 1;">
                    </div>
                    <div class="container position-relative" style="z-index: 2;">
                        <div class="row min-vh-100 py-8 align-items-center" data-inertia='{"weight":1.5}'>
                            <div class="col-sm-8 col-lg-7 px-5 px-sm-3">
                                <div class="overflow-hidden">
                                    <h1 class="fs-4 fs-md-5 lh-1 text-light" data-zanim-xs='{"delay":0}'>MISIÓN
                                    </h1>
                                </div>
                                <div class="overflow-hidden mt-3">
                                    <p class="text-white fs-1 fs-md-2 lh-xs text-justify" data-zanim-xs='{"delay":0.1}'>
                                        La DISBEDC tiene como misión “promover el bienestar de la comunidad
                                        universitaria y sociedad civil, mediante la aplicación de programas y
                                        políticas institucionales, que generen un proceso de desarrollo humano
                                        sostenible, generando espacios que propicien la comunicación a través de
                                        actividades académicas y atendiendo sus necesidades socioeconómicas, de
                                        salud, cultura y deporte, con calidad humana, contribuyendo al mejoramiento
                                        de las condiciones de vida.
                                    </p>
                                </div>
                                <div class="overflow-hidden">
                                    <div data-zanim-xs='{"delay":0.2}'>
                                        <a class="btn me-3 mt-3 text-light" style="background-color: #003366;"
                                            href="index.html#!">Localización
                                            <span class="fas fa-chevron-right ms-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: VISIÓN -->
                <div class="swiper-slide position-relative" data-zanim-timeline="{}">
                    <div class="bg-holder" style="background-image:url(pagina_template/assets/img/perfil2.png);">
                    </div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: 1;">
                    </div>
                    <div class="container position-relative" style="z-index: 2;">
                        <div class="row min-vh-100 py-8 align-items-center" data-inertia='{"weight":1.5}'>
                            <div class="col-sm-8 col-lg-7 px-5 px-sm-3">
                                <div class="overflow-hidden">
                                    <h1 class="fs-4 fs-md-5 lh-1 text-light" data-zanim-xs='{"delay":0}'>VISIÓN
                                    </h1>
                                </div>
                                <div class="overflow-hidden mt-3">
                                    <p class="text-white fs-1 fs-md-2 lh-xs text-justify" data-zanim-xs='{"delay":0.1}'>
                                        Generar la interacción de los miembros de la comunidad universitaria con el
                                        entorno socio-cultural, propiciando una estructura organizativa,
                                        administrativa, que permita operativizar las políticas de Bienestar
                                        Estudiantil y Extensión Universitaria en concordancia con las políticas
                                        institucionales de la Universidad Pública de El Alto.
                                    </p>
                                </div>
                                <div class="overflow-hidden">
                                    <div data-zanim-xs='{"delay":0.2}'>
                                        <a class="btn btn-primary me-3 mt-3" href="index.html#!">Localización
                                            <span class="fas fa-chevron-right ms-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: OBJETIVO -->
                <div class="swiper-slide position-relative" data-zanim-timeline="{}">
                    <div class="bg-holder" style="background-image:url(pagina_template/assets/img/perfil3.jpeg);">
                    </div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: 1;">
                    </div>
                    <div class="container position-relative" style="z-index: 2;">
                        <div class="row min-vh-100 py-8 align-items-center" data-inertia='{"weight":1.5}'>
                            <div class="col-sm-8 col-lg-7 px-5 px-sm-3">
                                <div class="overflow-hidden">
                                    <h1 class="fs-4 fs-md-5 lh-1 text-light" data-zanim-xs='{"delay":0}'>OBJETIVO
                                    </h1>
                                </div>
                                <div class="overflow-hidden mt-3">
                                    <p class="text-white fs-1 fs-md-2 lh-xs text-justify" data-zanim-xs='{"delay":0.1}'>
                                        Promover el desarrollo humano sostenible de la comunidad estudiantil,
                                        afirmando las potencialidades de las/os estudiantes, respondiendo a las
                                        necesidades bio-psicosociales, que garanticen el bienestar de la comunidad
                                        universitaria, bajo los siguientes pilares de acción: Área de Bienestar
                                        Estudiantil, Área de Extensión e Interacción Universitaria (cultura y
                                        deporte) y el Área de Psicología.
                                    </p>
                                </div>
                                <div class="overflow-hidden">
                                    <div data-zanim-xs='{"delay":0.2}'>
                                        <a class="btn btn-primary me-3 mt-3" href="index.html#!">Localización
                                            <span class="fas fa-chevron-right ms-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Navegación -->
            <div class="swiper-nav">
                <div class="swiper-button-prev"><span class="fas fa-chevron-left"></span></div>
                <div class="swiper-button-next"><span class="fas fa-chevron-right"></span></div>
            </div>
        </div>
    </section>



    <!-- <section> info sedes ============================-->
    <section class="bg-wheat text-center" style="margin-top: -150px; position: relative; z-index: 100;">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="ring-icon mx-auto" data-zanim-xs='{"delay":0}' style="background-color: #880000;">
                        <p class="fs-1 text-light">45</p>
                        <span class="fas fa-users text-light" style="margin-top:-28px"></span>
                    </div>
                    <h5 data-zanim-xs='{"delay":0.1}' class="mt-1">Estudiantes</h5>
                    <p class="mb-0 mt-3 px-3 mb-2" data-zanim-xs='{"delay":0.2}'>
                        Jóvenes comprometidos con su formación y el desarrollo académico, listos para transformar el futuro.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="ring-icon mx-auto" data-zanim-xs='{"delay":0}' style="background-color: #880000;">
                        <p class="fs-1 text-light">45</p>
                        <span class="fas fa-building text-light" style="margin-top:-28px"></span>
                    </div>
                    <h5 data-zanim-xs='{"delay":0.1}' class="mt-1">Sedes</h5>
                    <p class="mb-0 mt-3 px-3 mb-2" data-zanim-xs='{"delay":0.2}'>
                        Infraestructura moderna y accesible, diseñada para ofrecer un entorno de aprendizaje óptimo.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="ring-icon mx-auto" data-zanim-xs='{"delay":0}' style="background-color: #880000;">
                        <p class="fs-1 text-light">45</p>
                        <span class="fas fa-house-user text-light" style="margin-top:-28px"></span>
                    </div>
                    <h5 data-zanim-xs='{"delay":0.1}' class="mt-1">Carreras</h5>
                    <p class="mb-0 mt-3 px-3 mb-2" data-zanim-xs='{"delay":0.2}'>
                        Programas académicos variados y actualizados que preparan a profesionales competentes y
                        responsables.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="ring-icon mx-auto" data-zanim-xs='{"delay":0}' style="background-color: #880000;">
                        <p class="fs-1 text-light">45</p>
                        <span class="fas fa-user-graduate text-light" style="margin-top:-28px"></span>
                    </div>
                    <h5 data-zanim-xs='{"delay":0.1}' class="mt-1">Titulados</h5>
                    <p class="mb-0 mt-3 px-3 mb-2" data-zanim-xs='{"delay":0.2}'>
                        Profesionales exitosos que contribuyen al desarrollo de la sociedad con ética y conocimiento.
                    </p>
                </div>
            </div>

        </div>
    </section>
    <!-- Final info sedes ============================-->

    <!-- seccion de noticias -->
    <section class="py-6 text-center text-md-start" style="background-color: #003366;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md">
                    <h4 class="text-white mb-0 text-center">ÚLTIMAS NOTICIAS<br class="d-md-none" />
                    </h4>
                </div>
                <!--       <div class="col-md-auto mt-md-0 mt-4"><a class="btn btn-light rounded-pill" href="contact.html">Contact Us</a></div> -->
            </div>
        </div><!-- end of .container-->
    </section><!-- <section> close ============================-->


    <section class="bg-100">
        <div class="container">
            <div class="text-center mb-6">
                <h3 class="fs-2 fs-md-3">Ultimas Publicaciones de Sedes Academicas</h3>
                <hr class="short"
                    data-zanim-xs='{"from":{"opacity":0,"width":0},"to":{"opacity":1,"width":"4.20873rem"},"duration":0.8}'
                    data-zanim-trigger="scroll" />
            </div>

                @foreach ($noticias as $index => $noticia)
                <div class="row g-0 position-relative mb-4 mb-lg-0">
                    {{-- Imagen de portada --}}
                    <div class="col-lg-6 py-3 py-lg-0 mb-0 position-relative {{ $index % 2 == 0 ? '' : 'order-lg-2' }}"
                        style="min-height:400px">
                        @php
                            // Buscamos la imagen que tenga 'portada_' en el nombre
                            $imagenPortada = $noticia->imagenesNoticia->first(function ($img) {
                                return str_contains($img->imagen, 'portada_');
                            });
                        @endphp

                        <div class="bg-holder rounded-ts-lg rounded-te-lg rounded-lg-te-0"
                            style="background-image: url({{ $imagenPortada ? asset('storage/imagenes_noticias/' . $imagenPortada->imagen) : asset('assets/noticias/default.jpg') }}); background-size: cover;background-position: center;">
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div
                        class="col-lg-6 px-lg-5 py-lg-6 p-4 my-lg-0 bg-white rounded-bs-lg rounded-lg-bs-0 rounded-be-lg rounded-lg-be-0 {{ $index % 2 == 0 ? '' : 'order-lg-1' }}">
                        <div class="elixir-caret d-none d-lg-block"></div>
                        <div class="d-flex align-items-center h-100">
                            <div data-zanim-timeline="{}" data-zanim-trigger="scroll">
                                {{-- Título --}}
                                <div class="overflow-hidden text-uppercase">
                                    <h5 data-zanim-xs='{"delay":0}'>{{ $noticia->titulo }}</h5>
                                </div>

                                {{-- Metadatos --}}
                                <div class="overflow-hidden mb-2">
                                    <small class="text-muted">
                                        Publicado: {{ $noticia->created_at_formateado }} |
                                        Autor: {{ $noticia->autor->nombre ?? 'Desconocido' }} |
                                        Tipo: {{ $noticia->categoria->nombre }}
                                    </small>
                                </div>

                                {{-- Contenido --}}
                                <div class="overflow-hidden">
                                    <p class="mt-3" data-zanim-xs='{"delay":0.1}'>
                                        {{ Str::limit($noticia->contenido, 200, '...') }}</p>
                                </div>

                                {{-- Botón Ver Detalle --}}
                                <div class="overflow-hidden">
                                    <div data-zanim-xs='{"delay":0.2}'>
                                        <a class="d-flex align-items-center"
                                            href="{{ route('noticia.detalleNoticia', $noticia->id) }}" target="_blank">
                                            Ver Detalle
                                            <div class="overflow-hidden ms-2">
                                                <span class="d-inline-block"
                                                    data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {{-- <div class="row g-0 position-relative mb-4 mb-lg-0">
                <div class="col-lg-6 py-3 py-lg-0 mb-0 position-relative" style="min-height:400px;">
                    <div class="bg-holder rounded-ts-lg rounded-te-lg rounded-lg-te-0  "
                        style="background-image:url(assets/noticias/feria_cientifica.jpg);"></div>
                    <!--/.bg-holder-->
                </div>
                <div
                    class="col-lg-6 px-lg-5 py-lg-6 p-4 my-lg-0 bg-white rounded-bs-lg rounded-lg-bs-0 rounded-be-lg rounded-lg-be-0 rounded-lg-te-lg ">
                    <div class="elixir-caret d-none d-lg-block"></div>
                    <div class="d-flex align-items-center h-100">
                        <div data-zanim-timeline="{}" data-zanim-trigger="scroll">
                            <div class="overflow-hidden">
                                <h5 data-zanim-xs='{"delay":0}'>IV VERSIÓN DE LA EXPO FERIA CIENTÍFICA DE SEDES
                                    ACADÉMICAS DESCONCENTRADAS "EXPOSAD 2024"</h5>
                            </div>
                            <div class="overflow-hidden">
                                <p class="mt-3" data-zanim-xs='{"delay":0.1}'>La Universidad Pública de El Alto
                                    y la Dirección de Interacción Social Bienestar Estudiantil Deportes y Cultura,
                                    fueron parte del desarrollo de la IV VERSIÓN DE LA EXPO FERIA CIENTÍFICA DE
                                    SEDES ACADÉMICAS DESCONCENTRADAS "EXPOSAD 2024", donde los estudiantes
                                    expusieron sus proyectos de investigación con mucho dinamismo y entusiasmo.</p>
                            </div>
                            <div class="overflow-hidden">
                                <div data-zanim-xs='{"delay":0.2}'>
                                    <a class="d-flex align-items-center" href="{{ route('noticia.show', 1) }}"
                                        target="_blank">
                                        Ver Detalle
                                        <div class="overflow-hidden ms-2">
                                            <span class="d-inline-block"
                                                data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span>
                                        </div>
                                    </a>
                                </div>
                                <!--                  <div data-zanim-xs='{"delay":0.2}'><a class="d-flex align-items-center" href="index.html#!">Ver Detalle<div class="overflow-hidden ms-2"><span class="d-inline-block" data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span></div></a></div>     -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-0 position-relative mb-4 mb-lg-0">
                <div class="col-lg-6 py-3 py-lg-0 mb-0 position-relative order-lg-2" style="min-height:400px;">
                    <div class="bg-holder rounded-ts-lg rounded-te-lg rounded-lg-te-0  rounded-lg-ts-0"
                        style="background-image:url(assets/noticias/arte_upea.jpg);"></div>
                    <!--/.bg-holder-->
                </div>
                <div
                    class="col-lg-6 px-lg-5 py-lg-6 p-4 my-lg-0 bg-white rounded-bs-lg rounded-lg-bs-0 rounded-be-lg  rounded-lg-be-0">
                    <div class="elixir-caret d-none d-lg-block"></div>
                    <div class="d-flex align-items-center h-100">
                        <div data-zanim-timeline="{}" data-zanim-trigger="scroll">
                            <div class="overflow-hidden">
                                <h5 data-zanim-xs='{"delay":0}'>Gira de Arte "CAMINOS DE UNIÓN</h5>
                            </div>
                            <div class="overflow-hidden">
                                <p class="mt-3" data-zanim-xs='{"delay":0.1}'>De esta manera fue como se
                                    desarrollo la Gira de Arte denominada "CAMINOS DE UNIÓN", en esta ocasión desde
                                    el Municipio de Achacachi donde estuvieron presentes LanzArte Bolivia y el
                                    Ballet Folklórico UPEA. </p>
                            </div>
                            <div class="overflow-hidden">
                                <div data-zanim-xs='{"delay":0.2}'>
                                    <a class="d-flex align-items-center"
                                        href={{ route('noticia.show', 1) }} target="_blank">
                                        Ver Detalle
                                        <div class="overflow-hidden ms-2">
                                            <span class="d-inline-block"
                                                data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span>
                                        </div>
                                    </a>
                                </div>
                                <!--                 <div data-zanim-xs='{"delay":0.2}'><a class="d-flex align-items-center" href="index.html#!">Learn More<div class="overflow-hidden ms-2"><span class="d-inline-block" data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span></div></a></div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-0 position-relative mb-4 mb-lg-0">
                <div class="col-lg-6 py-3 py-lg-0 mb-0 position-relative" style="min-height:400px;">
                    <div class="bg-holder rounded-ts-lg rounded-te-lg rounded-lg-te-0 rounded-lg-ts-0 rounded-bs-0 rounded-lg-bs-lg "
                        style="background-image:url(assets/noticias/campeonato.jpg);"></div>
                    <!--/.bg-holder-->
                </div>
                <div class="col-lg-6 px-lg-5 py-lg-6 p-4 my-lg-0 bg-white rounded-bs-lg rounded-lg-bs-0 rounded-be-lg  ">
                    <div class="elixir-caret d-none d-lg-block"></div>
                    <div class="d-flex align-items-center h-100">
                        <div data-zanim-timeline="{}" data-zanim-trigger="scroll">
                            <div class="overflow-hidden">
                                <h5 data-zanim-xs='{"delay":0}'>Encuentro deportivo INTER-SEDES 'VIACHA 2024'.</h5>
                            </div>
                            <div class="overflow-hidden">
                                <p class="mt-3" data-zanim-xs='{"delay":0.1}'>TFotografía de la apertura e
                                    inicio del día 5 de Septiembre del Encuentro deportivo INTER-SEDES 'VIACHA
                                    2024'.</p>
                            </div>
                            <div class="overflow-hidden">
                                <div data-zanim-xs='{"delay":0.2}'>
                                    <a class="d-flex align-items-center"
                                        href={{ route('noticia.show', 1) }} target="_blank">
                                        Ver Detalle
                                        <div class="overflow-hidden ms-2">
                                            <span class="d-inline-block"
                                                data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span>
                                        </div>
                                    </a>
                                </div>
                                <!--                  <div data-zanim-xs='{"delay":0.2}'><a class="d-flex align-items-center" href="index.html#!">Learn More<div class="overflow-hidden ms-2"><span class="d-inline-block" data-zanim-xs='{"from":{"opacity":0,"x":-30},"to":{"opacity":1,"x":0},"delay":0.8}'>&xrarr;</span></div></a></div>        -->
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="text-center mt-4">
                <a href="/noticias" target="_blank"
                    class="btn btn-outline-danger px-4 py-2 rounded-pill fw-semibold position-relative overflow-hidden">
                    Ver más noticias
                    <span class="ms-2">&xrarr;</span>
                </a>
            </div>

        </div>
    </section>

    <!-- fin de seccion de noticias -->

    <!-- INICIO AUTORIDADES -->

    <section class="bg-white position-relative"
        style="background-image: url('pagina_template/assets/img/perfil2.png'); background-attachment: fixed; background-size: cover; background-position: center;">
        <div class="container position-relative p-5" style="z-index: 1;">
            <h4 class="text-center text-light rounded p-2" style="margin-top:-95px; background-color: #880000;">NUESTRAS
                AUTORIDADES
            </h4>
            <div class="container py-1" style="margin-top:60px">
                <div class="row d-flex flex-wrap justify-content-center">
                    <!-- AUTORIDAD 1 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/rector_upea.webp') }}"
                            alt="Rector">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'> DR. CARLOS
                            CONDORI
                            TITIRICO</span>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1">RECTOR UNIVERSIDAD PÚBLICA DE EL ALTO</p>
                    </div>

                    <!-- AUTORIDAD 2 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/vice_rector.webp') }}"
                            alt="Vicerrector">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>DR. EFRAIN
                            CHAMBI VARGAS
                            PH.D.</span>
                        <p class="text-light mb-0 mt-1">VICERRECTOR UNIVERSIDAD PÚBLICA DE EL ALTO</p>
                    </div>

                    <!-- AUTORIDAD 3 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/directora_disbet.jpg') }}"
                            alt="Directora">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>LIC. HERMINIA SILLO
                            CORINA</span>
                        <p class="text-light mb-0 mt-1">DIRECTORA DISBEDC UNIVERSIDAD PÚBLICA DE EL ALTO</p>



                    </div>

                    <!-- AUTORIDAD 4 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/cordinador_sedes.jpg') }}"
                            alt="Directora">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'> LIC. PRIMITIVO HUAYHUA CAYO
                            CORINA</span>
                        <p class="text-light mb-0 mt-1">CORDINADOR DE SEDES UNIVERSIDAD PÚBLICA DE EL ALTO</p>



                    </div>
                </div>
            </div>

        </div><!-- end of .container-->
    </section>
    <!-- FINAL DE AUTORIDADES -->



    <!-- INICIO UBICACION DE SEDES ACADEMICAS DESCONCENTRADAS -->

    <section class="py-6 text-center mt-4" style="background-color: #003366;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md">
                    <h4 class="text-white mb-0">UBICACIÓN DE SEDES ACADEMICAS DESCONCENTRADAS DE LA UPEA<br
                            class="d-md-none" /></h4>
                </div>
            </div>
        </div><!-- end of .container-->
    </section><!-- <section> close ============================-->


    <section class="pt-0 mt-5">
        <div class="container">
            <div class="row flex-center text-center pb-6">
                <div class="col-12">

                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15299.515842022018!2d-68.2027616!3d-16.532207200000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sbo!4v1749878906763!5m2!1ses-419!2sbo"
                        width="1220" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div>
    </section>

    <!-- FINAL DE SEDES ACADEMICAS DESCONCENTRADAS -->




    <!-- Redes sociales -->

    <section class="py-6 text-center" style="background-color: #003366;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md">
                    <h4 class="text-white mb-0">CONTACTANOS <br class="d-md-none" /></h4>
                </div>
                <!--       <div class="col-md-auto mt-md-0 mt-4"><a class="btn btn-light rounded-pill" href="contact.html">Contact Us</a></div> -->
            </div>
        </div>
    </section>


    <section class="text-center">
        <div class="container">
            <div class="text-center">
                <h3 class="fs-2 fs-md-3">REDES SOCIALES</h3>
                <p class="px-lg-4 mt-3">"Las redes sociales son herramientas poderosas; utilízalas para aprender,
                    conectar y crecer, pero nunca olvides que el verdadero conocimiento se construye más allá de la
                    pantalla."</p>
                <hr class="short"
                    data-zanim-xs='{"from":{"opacity":0,"width":0},"to":{"opacity":1,"width":"4.20873rem"},"duration":0.8}'
                    data-zanim-trigger="scroll" />
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4 mt-4" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="px-3 py-4 px-lg-4">
                        <div class="overflow-hidden"><img src="{{ asset('assets/facebook.png') }}" alt="icon"
                                height="90" data-zanim-xs='{"delay":0}' /></div>
                        <div class="overflow-hidden">
                            <h5 class="mt-3" data-zanim-xs='{"delay":0.1}'>Facebook</h5>
                        </div>
                        <div class="overflow-hidden">
                            <a href="https://www.facebook.com/profile.php?id=61572634599360" target="_blank">
                                <p class="mb-0" data-zanim-xs='{"delay":0.2}'>Ver Publicaciones</p>
                            </a>
                            <!--              <p class="mb-0" data-zanim-xs='{"delay":0.2}'>Ir </p>    -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mt-4" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="px-3 py-4 px-lg-4">
                        <div class="overflow-hidden">
                            <img src="{{ asset('assets/tiktok.webp') }}" alt="icon" height="95"
                                data-zanim-xs='{"delay":0}' />
                        </div>
                        <div class="overflow-hidden">
                            <h5 class="mt-3" data-zanim-xs='{"delay":0.1}'>TikTok</h5>
                        </div>
                        <div class="overflow-hidden">
                            <a href="https://www.tiktok.com/@upea_disbedc_2025?_t=ZM-8vJiE1He7KJ&_r=1" target="_blank">
                                <p class="mb-0" data-zanim-xs='{"delay":0.2}'>Ver Publicaciones</p>
                            </a>
                            <!--                 <p class="mb-0" data-zanim-xs='{"delay":0.2}'>We cover a large range of creative platforms and digital projects with one purpose: to create experiences.</p>   -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mt-4" data-zanim-timeline="{}" data-zanim-trigger="scroll">
                    <div class="px-3 py-4 px-lg-4">
                        <div class="overflow-hidden"><img src="{{ asset('assets/youtube.webp') }}" alt="icon"
                                height="90" data-zanim-xs='{"delay":0}' /></div>
                        <div class="overflow-hidden">
                            <h5 class="mt-3" data-zanim-xs='{"delay":0.1}'>YouTube</h5>
                        </div>
                        <div class="overflow-hidden">
                            <a href="https://www.facebook.com/profile.php?id=61572634599360" target="_blank">
                                <p class="mb-0" data-zanim-xs='{"delay":0.2}'>Ver Publicaciones</p>
                            </a>
                            <!--                 <p class="mb-0" data-zanim-xs='{"delay":0.2}'>We guide you through the pipelines that generate new products with higher potential and lower risk.</p>    -->
                        </div>
                    </div>
                </div>

            </div>
    </section>

@endsection
