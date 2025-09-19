@extends('index')
@section('titulo', 'NOTICIA')
@section('contenido')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Merriweather:wght@700&display=swap"
        rel="stylesheet">

    <div class="container mb-5 font-sans mt-7 bg-light p-5">

        <!-- Slider de imágenes -->
        <div id="noticiaCarousel" class="carousel slide mb-4 rounded shadow-lg" data-bs-ride="carousel" data-bs-interval="3000">

            <div class="carousel-inner">

                @foreach ($noticia->imagenesNoticia as $imagenes)
                    {{-- Reemplaza con tus imágenes de la noticia --}}
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('storage/imagenes_noticias/' . $imagenes->imagen) }}"
                            class="d-block w-100 object-fit-cover" style="height: 550px;">
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#noticiaCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#noticiaCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Título, autor y fecha -->
        <div class="mb-4">
            <h2 class="fw-bold fs-4 mb-2" style="font-family: 'Inter', sans-serif;">{{ $noticia->titulo }}</h2>
            <p class="text-muted mb-1" style="font-family: 'Inter', sans-serif;">📅 {{ $noticia->created_at_formateado }}|
                ✍️ Autor: Juan Pérez</p>
            <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif;">🏷️ Categoría:
                {{ $noticia->categoria->nombre }}</p>
        </div>


        <!-- Botones -->
        <div class="d-flex flex-wrap gap-3 mb-5 mt-2">
            <a href="/" class="btn btn-outline-secondary shadow-sm" style="font-family: 'Inter', sans-serif;">← Volver
                a noticias</a>
            <a href="#" class="btn btn-primary shadow-sm" style="font-family: 'Inter', sans-serif;">Compartir
                noticia</a>
        </div>


        <!-- Contenido de la noticia -->
        <div class="mb-5" style="font-family: 'Inter', sans-serif; font-size: 1rem; line-height: 1.9; color:#333;">
            <p>
                {{ $noticia->contenido }}
            </p>
        </div>


        @if ($noticia->url_video)
            @php
                $videoId = '';
                $url = $noticia->url_video;

                // Expresión regular para encontrar el ID del video en cualquier tipo de URL de YouTube
                preg_match(
                    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                    $url,
                    $matches,
                );

                if (isset($matches[1])) {
                    $videoId = $matches[1];
                }
            @endphp

            @if ($videoId)
                <div class="container py-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                        title="Video de YouTube de la Noticia"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Noticias relacionadas -->
        <h3 class="mb-4" style="font-family: 'Inter', sans-serif;">Noticias relacionadas</h3>
        <div class="row g-4">
            @foreach ($ultimasNoticias as $noticia)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 news-card overflow-hidden">
                        <div class="position-relative">
                            <div class="bg-light d-flex align-items-center justify-content-center fw-bold text-dark fs-2 border"
                                style="height: 200px; width: 100%;">
                                <img src="{{ asset('storage/imagenes_noticias/' . $noticia->imagenesNoticia[0]->imagen) }}"
                                    alt="{{ $noticia->titulo }}" class="card-img-top"
                                    style="object-fit: cover; height: 100%; width: 100%;">
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" style="font-family: 'Inter', sans-serif;">{{ $noticia->titulo }}</h5>
                            <p class="text-muted small" style="font-family: 'Inter', sans-serif;">📅
                                {{ $noticia->created_at_formateado }}</p>
                            <a href="{{ route('noticia.detalleNoticia', encrypt($noticia->id)) }}"
                                class="btn btn-outline-primary btn-sm w-100 mt-2"
                                style="font-family: 'Inter', sans-serif;">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <style>
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease-in-out;
        }
    </style>

@endsection
