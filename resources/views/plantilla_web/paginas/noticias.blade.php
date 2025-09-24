@extends('index')
@section('titulo', 'NOTICIA')
@section('contenido')

    <style>
        /* Hover animado en noticias */
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease-in-out;
        }

        .transition-img {
            transition: transform 0.3s ease;
        }

        .news-card:hover .transition-img {
            transform: scale(1.05);
        }

        .badge {
            font-size: 0.8rem;
            text-transform: uppercase;
        }


        .page-link {
            background-color: #880000;
            color: rgb(241, 241, 241);
        }

        .page-link:hover {
            background-color: #880000;
            color: rgb(241, 241, 241);
        }
    </style>
    </style>

    <div class="container py-4 font-sans">

        @if (isset($noticiaDestacada))
            <!-- 🔥 Noticia destacada -->
            <div class="row mb-5 mt-6 align-items-center bg-light rounded p-4 shadow-sm">
                <div class="col-md-6 position-relative">
                    @php

                        // Buscamos la imagen que tenga 'portada_' en el nombre
                        $imagenPortada = $noticiaDestacada->imagenesNoticia->first(function ($img) {
                            return str_contains($img->imagen, 'portada_');
                        });

                    @endphp
                    <img src="{{ asset('storage/imagenes_noticias/' . ($imagenPortada->imagen ?? 'default.webp')) }}"
                        alt="Noticia destacada" class="img-fluid shadow-lg">
                    <span class="position-absolute top-0 end-0 p-2 badge mt-2 me-4 text-capitalize bg-gradient"
                        style="background-color: #003366;border-radius: 5px;">{{ $noticiaDestacada->sede->nombre }}</span>

                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center p-3">
                    <h2 class="fw-bold mb-2 text-dark text-uppercase fs-3" style="font-family: 'Inter', sans-serif;">
                        {{ $noticiaDestacada->titulo }}
                    </h2>
                    <p class="small text-muted mb-3 text-capitalize">
                        <i class="far fa-calendar-alt me-1"></i> {{ $noticiaDestacada->created_at_formateado }} |
                        <i class="fas fa-user me-1"></i>
                        {{ $noticiaDestacada->usuario
                            ? strtoupper(substr($noticiaDestacada->usuario->nombres, 0, 1)) .
                                '. ' .
                                strtok($noticiaDestacada->usuario->apellidos, ' ')
                            : 'Desconocido' }}
                        |
                        <i class="fas fa-tag me-1"></i> {{ $noticiaDestacada->categoria->nombre }}
                    </p>
                    <p class="text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                        {{ Str::limit($noticiaDestacada->contenido, 200, '...') }}</p>
                    </p>
                    <a href="{{ route('noticia.detalleNoticia', encrypt($noticiaDestacada->id)) }}"
                        class="btn btn-outline-primary btn-sm w-100 mt-2" style="font-family: 'Inter', sans-serif;">Ver
                        más</a>
                </div>
            </div>
        @endif


        <div class="coniner-fluid bg-light rounded p-4 shadow-sm mt-3">
            <form method="GET" action="{{ route('noticias.show') }}" class="py-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-light">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Buscar noticias...">
                        </div>

                        <div class="col-12 col-md-3">
                            <select class="form-select" name="categoria" style="font-family: 'Inter', sans-serif;">
                                <option value="">Todos los tipos</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ encrypt($categoria->id) }}"
                                        {{ request('categoria') && decrypt(request('categoria')) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <select class="form-select" name="sede" style="font-family: 'Inter', sans-serif;">
                                <option value="">Seleccionar Sede</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ encrypt($sede->id) }}"
                                        {{ request('sede') && decrypt(request('sede')) == $sede->id ? 'selected' : '' }}>
                                        {{ $sede->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control"
                                title="Buscar por fecha">
                        </div>

                        <button type="submit" class="btn btn-danger rounded shadow-sm px-4 col-12 col-md-2">
                            <i class="fas fa-search  me-1"></i> Buscar
                        </button>

                        <span class="text-muted py-2 px-3 fw-normal ms-auto">
                            {{ $noticias->total() }} Resultados Encontrados
                        </span>
                    </div>
                </div>
            </form>

            <!-- 📰 Sección Más Noticias con buscador y filtro -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <h3 class="mb-0" style="font-family: 'Inter', sans-serif;">Más noticias</h3>
            </div>

            <!-- Listar noticias -->
            <div class="row g-4" id="noticiasGrid">
                @foreach ($noticias as $noticia)
                    <div class="col-md-4 noticia-item">
                        <div class="card h-100 shadow-sm border-0 news-card overflow-hidden">
                            <div class="position-relative">
                                <div class="bg-light d-flex align-items-center justify-content-center fw-bold text-dark fs-2 border position-relative"
                                    style="height: 200px; width: 100%;">
                                    @php
                                        // Buscamos la imagen que tenga 'portada_' en el nombre
                                        $imagenPortada = $noticia->imagenesNoticia->first(function ($img) {
                                            return str_contains($img->imagen, 'portada_');
                                        });
                                    @endphp

                                    <img src="{{ asset('storage/imagenes_noticias/' . ($imagenPortada->imagen ?? 'default.webp')) }}"
                                        alt="{{ $noticia->titulo }}" class="card-img-top transition-img"
                                        style="object-fit: cover; height: 100%; width: 100%;">
                                    <span
                                        class="position-absolute top-0 end-0 p-2 badge mt-2 me-1 text-capitalize bg-gradient"
                                        style="background-color: #003366;border-radius: 5px;">{{ $noticia->sede->nombre }}</span>
                                </div>
                                <span class="badge bg-danger position-absolute top-2 start-2 p-2"
                                    style="font-family: 'Inter', sans-serif;"></span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-uppercase" style="font-family: 'Inter', sans-serif;">
                                    {{ $noticia->titulo }}
                                </h6>
                                <p class="small text-muted mb-3 text-capitalize">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $noticia->created_at_formateado }}
                                    |
                                    <i class="fas fa-user me-1"></i>
                                    {{ $noticia->usuario
                                        ? strtoupper(substr($noticia->usuario->nombres, 0, 1)) .
                                            '. ' .
                                            strtok($noticia->usuario->apellidos, ' ')
                                        : 'Desconocido' }}
                                    
                                    <i class="fas fa-tag me-1"></i> {{ $noticia->categoria->nombre }}
                                </p>
                                <a href="{{ route('noticia.detalleNoticia', encrypt($noticia->id)) }}"
                                    class="btn btn-outline-primary btn-sm w-100 mt-2"
                                    style="font-family: 'Inter', sans-serif;">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mb-2 mt-4">
                <p class="text-muted text-center">
                    Mostrando {{ $noticias->firstItem() }} al {{ $noticias->lastItem() }} de {{ $noticias->total() }}
                    resultados
                </p>
            </div>

            <div class="d-flex justify-content-center">
                {{ $noticias->links('pagination::simple-bootstrap-5') }}
            </div>


        </div>

    </div>

    {{-- 
    <script>
        const searchInput = document.getElementById('searchNoticias');
        const filtroSelect = document.getElementById('filtroTipo');
        const noticias = document.querySelectorAll('.noticia-item');

        searchInput.addEventListener('input', filtrarNoticias);
        filtroSelect.addEventListener('change', filtrarNoticias);

        function filtrarNoticias() {
            const search = searchInput.value.toLowerCase();
            const tipo = filtroSelect.value;

            noticias.forEach(noticia => {
                const titulo = noticia.querySelector('.card-title').textContent.toLowerCase();
                const noticiaTipo = noticia.dataset.tipo;
                noticia.style.display = (titulo.includes(search) && (tipo === '' || noticiaTipo === tipo)) ?
                    'block' : 'none';
            });
        }

        document.getElementById('cargarMas').addEventListener('click', () => {
            alert('Aquí se cargarían más noticias desde el backend.');
        });
    </script> --}}
@endsection



@section('script')
    <script src="{{ asset('js/modulos/pagina/noticias.js') }}" type="module"></script>
@endsection
