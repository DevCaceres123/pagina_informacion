@extends('index')
@section('titulo', 'PERFIL')
@section('contenido')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <style>
        /* SECCIÓN */
        .stats-section {

            padding: 90px 0;
            margin-top: -120px;
            position: relative;
            z-index: 10;
        }

        /* CARD */
        .stat-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px 25px;
            text-align: center;
            height: 100%;
            border-bottom: 4px solid #880000;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.35s ease;
        }

        .stat-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.14);
        }

        /* ICONO */
        .stat-icon {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            background: rgba(136, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .icon-inner {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: #880000;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(136, 0, 0, 0.4);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .icon-inner {
            transform: scale(1.60);
        }

        .icon-inner i {
            font-size: 28px;
            color: #ffffff;
        }

        /* TEXTO */
        .stat-number {
            font-size: 46px;
            font-weight: 800;
            color: #880000;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .stat-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1f2a44;
        }

        .stat-divider {
            display: block;
            width: 42px;
            height: 3px;
            background: #880000;
            margin: 12px auto 18px;
            border-radius: 3px;
        }

        .stat-text {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }
    </style>
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
                                        Promover el bienestar de la comunidad estudiantil, mediante la aplicación de
                                        políticas y programas institucionales que generen un proceso de desarrollo humano
                                        sostenible, generando espacios que propicien la comunicación a través de actividades
                                        académicas, culturales y deportivas, atendiendo sus necesidades, con calidad humana;
                                        contribuyendo al mejoramiento de las condiciones de la sociedad
                                    </p>
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
                                        Generar la interacción y extensión universitaria con su entorno socio – cultural,
                                        propiciando una estructura organizativa administrativa que permita la
                                        operativización de las políticas institucionales de bienestar estudiantil de la
                                        Universidad Pública de El Alto
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: OBJETIVO -->
                {{-- <div class="swiper-slide position-relative" data-zanim-timeline="{}">
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

                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>

            <!-- Navegación -->
            <div class="swiper-nav">
                <div class="swiper-button-prev"><span class="fas fa-chevron-left"></span></div>
                <div class="swiper-button-next"><span class="fas fa-chevron-right"></span></div>
            </div>
        </div>
    </section>



    <!-- <section> info sedes ============================-->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">

                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card counter">
                        <div class="stat-icon">
                            <div class="icon-inner">
                                <i class="fas fa-users text-light"></i>
                            </div>
                        </div>

                        <h2 class="stat-number" data-target="{{$cantidadEstudiantes > 0 ? $cantidadEstudiantes : 5000}}">0</h2>
                        <h5 class="stat-title">Estudiantes</h5>
                        <span class="stat-divider"></span>

                        <p class="stat-text">
                            Jóvenes comprometidos con su formación y el desarrollo académico.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card counter">
                        <div class="stat-icon">
                            <div class="icon-inner">
                                <i class="fas fa-building text-light"></i>
                            </div>
                        </div>

                        <h2 class="stat-number" data-target="{{$cantidadSedes > 0 ? $cantidadSedes : 13}}">0</h2>
                        <h5 class="stat-title">Sedes</h5>
                        <span class="stat-divider"></span>

                        <p class="stat-text">
                            Infraestructura moderna y accesible para un aprendizaje óptimo.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card counter">
                        <div class="stat-icon">
                            <div class="icon-inner">
                                <i class="fas fa-book-open text-light"></i>
                            </div>
                        </div>

                        <h2 class="stat-number" data-target="{{$cantidadCarreras > 0 ? $cantidadCarreras : 20}}">0</h2>
                        <h5 class="stat-title">Carreras</h5>
                        <span class="stat-divider"></span>

                        <p class="stat-text">
                            Programas académicos actualizados y orientados al mercado laboral.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card counter">
                        <div class="stat-icon">
                            <div class="icon-inner">
                                <i class="fas fa-user-graduate text-light"></i>
                            </div>
                        </div>

                        <h2 class="stat-number" data-target="{{$cantidadTitulados > 0 ? $cantidadTitulados : 4000}}">0</h2>
                        <h5 class="stat-title">Titulados</h5>
                        <span class="stat-divider"></span>

                        <p class="stat-text">
                            Profesionales que aportan con ética y conocimiento a la sociedad.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Final info sedes ============================-->

    <!-- seccion de noticias -->
    <section class="py-4" style="background: linear-gradient(135deg, #003366 0%, #004080 100%); border-bottom: 4px solid #880000;">
        <div class="container">
            <div class="d-flex flex-column align-items-center text-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span style="width:38px;height:38px;background:rgba(255,255,255,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-newspaper text-white" style="font-size:16px"></i>
                    </span>
                    <h4 class="text-white mb-0 fw-bold">ÚLTIMAS NOTICIAS</h4>
                </div>
                <div style="width:50px;height:3px;background:#880000;border-radius:2px;margin-top:2px"></div>
            </div>
        </div>
    </section>


    <section class="bg-100">
        <div class="container">
            <div class="text-center mb-6">
                <h3 class="fs-2 fs-md-2 text-capitalize">Últimas publicaciones de sedes académicas desconcentradas</h3>
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

                        <div class="bg-holder rounded-ts-lg rounded-te-lg rounded-lg-te-0 position-relative"
                            style="background-image: url({{ $imagenPortada ? asset('storage/imagenes_noticias/' . $imagenPortada->imagen) : asset('storage/imagenes_noticias/default.webp') }}); background-size: cover;background-position: center;">
                            <span class="position-absolute top-0 end-0 p-2 badge mt-2 me-2 text-capitalize bg-gradient"
                                style="background-color: #003366;border-radius: 5px;">{{ $noticia->sede->nombre }}</span>
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
                                    {{-- Metadatos --}}
                                    <p class="small text-muted mb-3 text-capitalize">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $noticia->created_at_formateado }} |
                                        <i class="fas fa-user me-1"></i>
                                        {{ $noticia->usuario
                                            ? strtoupper(substr($noticia->usuario->nombres, 0, 1)) . '. ' . strtok($noticia->usuario->apellidos, ' ')
                                            : 'Desconocido' }}
                                        |
                                        <i class="fas fa-tag me-1"></i> {{ $noticia->categoria->nombre }}
                                    </p>
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
                                            href="{{ route('noticia.detalleNoticia', encrypt($noticia->id)) }}"
                                            target="_blank">
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

    <section class="position-relative py-6"
        style="background-image: url('pagina_template/assets/img/perfil2.png'); background-attachment: fixed; background-size: cover; background-position: center;">
        {{-- Overlay oscuro para legibilidad --}}
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background:rgba(0,0,0,.6);z-index:0"></div>
        <div class="container position-relative" style="z-index: 1;">
            <div class="d-flex flex-column align-items-center text-center mb-5 gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span style="width:38px;height:38px;background:rgba(136,0,0,.7);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-user-tie text-white" style="font-size:15px"></i>
                    </span>
                    <h4 class="text-white mb-0 fw-bold">NUESTRAS AUTORIDADES</h4>
                </div>
                <div style="width:50px;height:3px;background:#880000;border-radius:2px;margin-top:2px"></div>
            </div>
            <div class="container py-1">
                <div class="row d-flex flex-wrap justify-content-center">
                    <!-- AUTORIDAD 1 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/rector_upea.webp') }}"
                            alt="Rector">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>DR. CARLOS
                            CONDORI TITIRICO </span>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1"><b>RECTOR</b></p>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1">UNIVERSIDAD PÚBLICA DE EL ALTO</p>
                    </div>

                    <!-- AUTORIDAD 2 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2" src="{{ asset('assets/autoridades/vice_rector.webp') }}"
                            alt="Vicerrector">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>DR. EFRAIN
                            CHAMBI VARGAS
                        </span>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1"><b>VICERRECTOR</b></p>
                        <p class="text-light mb-0 mt-1"> UNIVERSIDAD PÚBLICA DE EL ALTO</p>
                    </div>

                    <!-- AUTORIDAD 3 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2"
                            src="{{ asset('assets/autoridades/directora_disbet.jpg') }}" alt="Directora">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>LIC.
                            HERMINIA SILLO CORINA
                            </span>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1"><b>DIRECTORA - DISBEDC</b></p>
                        <p class="text-light mb-0 mt-1">UNIVERSIDAD PÚBLICA DE EL ALTO</p>



                    </div>

                    <!-- AUTORIDAD 4 -->
                    <div class="col-12 col-md-3 d-flex flex-column align-items-center text-center">
                        <img class="rounded-3 img-fluid mb-2"
                            src="{{ asset('assets/autoridades/cordinador_sedes.jpg') }}" alt="Directora">
                        <span class=" fw-bold text-light p-2 rounded mt-1" style='background-color: #880000;'>LIC.
                            PRIMITIVO HUAYHUA CAYO</span>
                        <p class="text-light mb-0 mt-1  bg-lightp-2 rounded mt-1"><b>COORDINADOR DE SEDES</b></p>
                        <p class="text-light mb-0 mt-1">UNIVERSIDAD PÚBLICA DE EL ALTO</p>



                    </div>
                </div>
            </div>

        </div>
        </div><!-- end of .container-->
    </section>
    <!-- FINAL DE AUTORIDADES -->



    <!-- INICIO UBICACION DE SEDES ACADEMICAS DESCONCENTRADAS -->

    <section class="py-4" style="background: linear-gradient(135deg, #003366 0%, #004080 100%); border-bottom: 4px solid #880000;">
        <div class="container">
            <div class="d-flex flex-column align-items-center text-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span style="width:38px;height:38px;background:rgba(255,255,255,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-map-marked-alt text-white" style="font-size:16px"></i>
                    </span>
                    <h4 class="text-white mb-0 fw-bold">UBICACIÓN DE SEDES ACADÉMICAS DESCONCENTRADAS (UPEA)</h4>
                </div>
                <div style="width:50px;height:3px;background:#880000;border-radius:2px;margin-top:2px"></div>
            </div>
        </div>
    </section>


    <section class="pt-0 mt-5">
        <div class="container">
            <div class="row flex-center text-center pb-6">
                <div class="col-12">
                    <div id="map" style="width: 100%; height: 500px; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- FINAL DE SEDES ACADEMICAS DESCONCENTRADAS -->




    <!-- Redes sociales -->

    <section class="py-4" style="background: linear-gradient(135deg, #003366 0%, #004080 100%); border-bottom: 4px solid #880000;">
        <div class="container">
            <div class="d-flex flex-column align-items-center text-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span style="width:38px;height:38px;background:rgba(255,255,255,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-share-alt text-white" style="font-size:16px"></i>
                    </span>
                    <h4 class="text-white mb-0 fw-bold">REDES SOCIALES</h4>
                </div>
                <div style="width:50px;height:3px;background:#880000;border-radius:2px;margin-top:2px"></div>
            </div>
        </div>
    </section>


    <section class="text-center">
        <div class="container">
            <div class="text-center">

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
@section('script')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const poligonos = @json($poligonos);

            const map = L.map('map').setView([-16.5322, -68.2027], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const colors = ["#880000", "#007bff", "#28a745", "#ffc107"];
            var allBounds = null;

            poligonos.forEach((pol, index) => {
                const color = colors[index % colors.length];
                const layer = L.geoJSON(pol.geometry, {
                    style: { color: color, weight: 2, fillColor: color, fillOpacity: 0.4 }
                }).addTo(map);

                layer.bindTooltip(pol.ubicacion, { permanent: true, direction: "top" });

                try {
                    const b = layer.getBounds();
                    if (b.isValid()) {
                        allBounds = allBounds ? allBounds.extend(b) : b;
                        const center = b.getCenter();
                        layer.bindPopup(`
                            <b>${pol.ubicacion}</b><br>
                            <a class="btn btn-sm btn-success mt-2"
                               href="https://www.google.com/maps/dir/?api=1&destination=${center.lat},${center.lng}"
                               target="_blank">🚗 Cómo llegar</a>
                        `);
                    }
                } catch (e) {}
            });

            if (allBounds && allBounds.isValid()) {
                map.fitBounds(allBounds, { padding: [40, 40] });
            }


            // 👉 Mensaje inicial para guiar al usuario
            L.control
                .attribution({
                    prefix: ""
                })
                .addAttribution("🖱️ Haz click en la ubicacion para ver cómo llegar")
                .addTo(map);
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".stat-number");
            const speed = 120; // menor = más rápido

            const animateCounter = (counter) => {
                const target = +counter.getAttribute("data-target");
                let count = 0;

                const updateCount = () => {
                    const increment = Math.ceil(target / speed);
                    count += increment;

                    if (count < target) {
                        counter.innerText = count;
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCount();
            };

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.6
            });

            counters.forEach(counter => observer.observe(counter));
        });
    </script>
@endsection
