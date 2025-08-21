@extends('index')
@section('titulo', 'NOTICIA')
@section('contenido')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Merriweather:wght@700&display=swap" rel="stylesheet">

<div class="container mb-5 font-sans mt-7 bg-light p-5">

    <!-- Slider de imágenes -->
    <div id="noticiaCarousel" class="carousel slide mb-4 rounded shadow-lg" data-bs-ride="carousel">
        <div class="carousel-inner rounded">
            @foreach(range(1,3) as $i) {{-- Reemplaza con tus imágenes de la noticia --}}
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <img src="{{ asset('assets/noticias/feria_cientifica.jpg') }}" 
                     class="d-block w-100 object-fit-cover" 
                     style="height: 550px;" 
                     alt="Imagen {{ $i }}">
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
        <h1 class="fw-bold text-3xl mb-2" style="font-family: 'Inter', sans-serif;">Título completo de la noticia</h1>
        <p class="text-muted mb-1" style="font-family: 'Inter', sans-serif;">📅 20 Agosto 2025 | ✍️ Autor: Juan Pérez</p>
        <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif;">🏷️ Categoría: Deporte</p>
    </div>

    <!-- Contenido de la noticia -->
    <div class="mb-5" style="font-family: 'Inter', sans-serif; font-size: 1rem; line-height: 1.9; color:#333;">
       <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quia, qui, vel, harum dicta temporibus error voluptates aperiam beatae sunt fugiat necessitatibus doloribus dolorum deleniti cum. Consequatur, beatae. Dolorem, ipsa quis? 
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo rerum, corrupti perferendis itaque ab quidem non quos nemo, rem, necessitatibus aliquam nulla. Quisquam temporibus molestias alias, laudantium odio eligendi inventore! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatibus amet, nulla eveniet obcaecati dolorem ipsa perferendis, quam deserunt, vel odit hic molestiae pariatur quo cumque! Ullam rem facere non! Sapiente. Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda eveniet quod consequuntur natus ex accusantium ipsa a quidem officia impedit! Error excepturi officiis dolorum pariatur quae animi voluptatem eveniet repellendus!
        Laudantium aliquid accusamus et est quo. Ea quos dolorum perferendis ducimus ut numquam, at, officiis voluptates architecto facilis atque rerum. Voluptatem iure iusto similique ipsam nobis reprehenderit eaque quis soluta.
        Magni incidunt, distinctio obcaecati eveniet deleniti unde quas molestiae ea deserunt accusamus maxime doloremque vel autem hic placeat velit exercitationem cum veniam architecto ex similique nam labore. Aspernatur, accusantium aliquam?
       </p>
    </div>

    <!-- Botones -->
    <div class="d-flex flex-wrap gap-3 mb-5">
        <a href="{{ route('pagina.inicio') }}" class="btn btn-outline-secondary shadow-sm" style="font-family: 'Inter', sans-serif;">← Volver a noticias</a>
        <a href="#" class="btn btn-primary shadow-sm" style="font-family: 'Inter', sans-serif;">Compartir noticia</a>
    </div>

    <!-- Noticias relacionadas -->
    <h3 class="mb-4" style="font-family: 'Inter', sans-serif;">Noticias relacionadas</h3>
    <div class="row g-4">
        @foreach(range(1,3) as $i)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 news-card overflow-hidden">
                <div class="position-relative">
                    <div class="bg-light w-100 h-48 d-flex align-items-center justify-content-center fw-bold text-dark fs-2">{{ $i }}</div>
                </div>
                <div class="card-body">
                    <h5 class="card-title" style="font-family: 'Inter', sans-serif;">Noticia relacionada {{ $i }}</h5>
                    <p class="text-muted small" style="font-family: 'Inter', sans-serif;">📅 18 Agosto 2025</p>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-2" style="font-family: 'Inter', sans-serif;">Ver más</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

<style>
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        transition: all 0.3s ease-in-out;
    }
</style>

@endsection
