@extends('index')
@section('titulo', 'PERFIL')
@section('contenido')
    <style>
        .img-carrusel {
            width: 100%;
            height: 400px;
            object-fit: cover;
            object-position: center;
            border-radius: 0.5rem;
        }
    </style>
<div class="container-fluid bg-light py-5">
    <div class="container text-center mb-5">
        <h1 class="display-5 fw-bold text-primary mt-5">Noticias</h1>
        <p class="lead text-secondary">Mantente informado sobre las últimas novedades de nuestra institución.</p>
    </div>

    <div class="container bg-white p-5 rounded shadow-sm">
        <a href="{{ asset('inicio') }}" class="btn btn-outline-primary mb-4">&larr; Volver a Noticias</a>

        <!-- Título y metadatos -->
        <h2 class="mb-2 fw-semibold text-dark">Inauguración del nuevo laboratorio en Caranavi</h2>
        <p class="text-muted">Publicado el 10 de junio de 2025 por <strong>Lic. Juan Pérez</strong></p>

        <!-- Carrusel de imágenes -->
        <div id="galeriaNoticia" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner rounded">
                <div class="carousel-item active">
                    <img src="{{ asset('pagina_template/assets/img/portrait-6.jpg') }}" class="d-block w-100 img-carrusel" alt="Imagen 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('pagina_template/assets/img/portrait-7.jpg') }}" class="d-block w-100 img-carrusel" alt="Imagen 2">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#galeriaNoticia" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galeriaNoticia" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Contenido de la noticia -->
        <div class="text-dark fs-2">
            <p>
                La sede académica de Caranavi celebró la apertura de su nuevo laboratorio de biotecnología. Este espacio permitirá a los estudiantes realizar prácticas especializadas en biología molecular, genética y microbiología.
           
                El evento contó con la presencia de autoridades de la UPEA, docentes, estudiantes y representantes del municipio.
            
            
                Este es un paso más hacia la descentralización y el fortalecimiento de la educación superior en las provincias.
            </p>
        </div>
    </div>
</div>

@endsection
