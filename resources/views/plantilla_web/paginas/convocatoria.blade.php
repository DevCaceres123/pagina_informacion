@extends('index')
@section('titulo', 'CONVOCATORIA')
@section('contenido')

    <div class="container mb-5 font-sans mt-7 bg-light p-5">

        {{-- 🔹 Carrusel de imágenes de la convocatoria --}}
        <div id="convocatoriaCarousel" class="carousel slide mb-4 rounded shadow-lg" data-bs-ride="carousel">
            <div class="carousel-inner rounded">
                @forelse ($convocatorias->imgConvocatorias as $imagen)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $imagen->imagen) }}" class="d-block w-100 object-fit-cover"
                            style="height: 550px;" alt="Imagen convocatoria {{ $loop->iteration }}">
                    </div>
                @empty
                    {{-- Imagen por defecto si no hay galería --}}
                    <div class="carousel-item active">
                        <img src="{{ asset('storage/imagenes_convocatorias/default.webp') }}" class="d-block w-100 object-fit-cover"
                            style="height: 550px;" alt="Sin imagen">
                    </div>
                @endforelse
            </div>

            {{-- Controles del carrusel --}}
            @if ($convocatorias->imgConvocatorias->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#convocatoriaCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#convocatoriaCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            @endif
        </div>

        {{-- 🔹 Título, autor, fecha y categoría --}}
        <div class="mb-4">
            <h2 class="fw-bold text-3xl mb-2 text-uppercase mb-2" style="font-family: 'Inter', sans-serif;">
                {{ $convocatorias->titulo }}
            </h2>
            <p class="small text-muted mb-3 text-capitalize">
                <i class="far fa-calendar-alt me-1"></i> {{ $convocatorias->created_at->translatedFormat('d F Y') }}
                |
                <i class="fas fa-user me-1"></i>
                {{ $convocatorias->usuario
                    ? strtoupper(substr($convocatorias->usuario->nombres, 0, 1)) .
                        '. ' .
                        strtok($convocatorias->usuario->apellidos, ' ')
                    : 'Desconocido' }}
                |
                <i class="fas fa-tag me-1"></i> {{ $convocatorias->categoria->nombre }}
            </p>
        </div>

        {{-- 🔹 Botones --}}
        <div class="d-flex flex-wrap gap-3 mb-5">
            <a href="{{ route('convocatorias.show') }}" class="btn btn-outline-secondary shadow-sm"
                style="font-family: 'Inter', sans-serif;">← Volver</a>

            @if ($convocatorias->archivo)
                <a href="{{ asset('storage/' . $convocatorias->archivo) }}" target="_blank" class="btn btn-info shadow-sm"
                    style="font-family: 'Inter', sans-serif;">
                    📄 Descargar archivo
                </a>
            @endif

            <button onclick="navigator.share({title: '{{ $convocatorias->titulo }}', url: window.location.href})"
                class="btn btn-primary shadow-sm" style="font-family: 'Inter', sans-serif;">
                Compartir convocatoria
            </button>
        </div>
        {{-- 🔹 Contenido de la convocatoria --}}
        <div class="mb-5" style="font-family: 'Inter', sans-serif; font-size: 1rem; line-height: 1.9; color:#333;">
            {!! nl2br(e($convocatorias->descripcion)) !!}
        </div>



        {{-- 🔹 Otras convocatorias relacionadas --}}
        <h3 class="mb-4" style="font-family: 'Inter', sans-serif;">Otras convocatorias</h3>
        <div class="row g-4">
            @forelse ($ultimasConvocatorias as $otra)
                @php
                    $imagenRelacionada = $otra->imgConvocatorias->first()->imagen ?? 'assets/img/default.webp';
                @endphp
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 news-card overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $imagenRelacionada) }}" class="card-img-top object-fit-cover"
                                style="height: 230px;" alt="Imagen relacionada">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-uppercase" style="font-family: 'Inter', sans-serif;">
                                {{ $otra->titulo }}
                            </h5>
                            <p class="text-muted small" style="font-family: 'Inter', sans-serif;">
                                📅 {{ $otra->created_at->translatedFormat('d F Y') }}
                            </p>
                            <a href="{{ route('convocatoria.deatelleConvocatoria', encrypt($otra->id)) }}"
                                class="btn btn-outline-primary btn-sm w-100 mt-2" style="font-family: 'Inter', sans-serif;">
                                Ver más
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">No hay convocatorias relacionadas.</p>
            @endforelse
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
