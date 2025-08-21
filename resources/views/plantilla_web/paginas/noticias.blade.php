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
    </style>

    <div class="container py-4 font-sans">

        <!-- 🔥 Noticia destacada -->
        <div class="row mb-5 mt-6 align-items-center bg-light rounded p-4 shadow-sm">
            <div class="col-md-6 position-relative">
                <img src="{{ asset('assets/noticias/feria_cientifica.jpg') }}" alt="Noticia destacada"
                    class="img-fluid rounded shadow-lg">

            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center p-3">
                <h2 class="fw-bold text-3xl mb-2 text-dark" style="font-family: 'Inter', sans-serif;">Título de la Noticia
                    Destacada</h2>
                <p class="text-muted mb-2" style="font-family: 'Inter', sans-serif;">📅 20 Agosto 2025 | ✍️ Autor: Juan Pérez
                </p>
                <p class="text-gray-800 mb-3" style="font-family: 'Inter', sans-serif;">
                    Este es un resumen o contenido más largo de la noticia destacada. Aquí puedes poner la introducción o
                    parte del contenido para captar la atención del lector...
                </p>
                <a href="#" class="btn btn-secondary btn-sm shadow rounded"
                    style="font-family: 'Inter', sans-serif;"><----- Ver más -----></a>
            </div>
        </div>

        <div class="coniner-fluid bg-light rounded p-4 shadow-sm">
            <!-- 📰 Sección Más Noticias con buscador y filtro -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <h3 class="mb-0" style="font-family: 'Inter', sans-serif;">Más noticias</h3>

                <div class="d-flex gap-2 flex-wrap">
                    <input type="text" class="form-control" id="searchNoticias" placeholder="Buscar noticias..."
                        style="font-family: 'Inter', sans-serif;">
                    <select class="form-select" id="filtroTipo" style="font-family: 'Inter', sans-serif;">
                        <option value="">Todos los tipos</option>
                        <option value="deporte">Deporte</option>
                        <option value="cultura">Cultura</option>
                        <option value="educacion">Educación</option>
                    </select>
                </div>
            </div>

            <!-- Grid de noticias -->
            <div class="row g-4" id="noticiasGrid">
                @foreach (range(2, 7) as $i)
                    <div class="col-md-4 noticia-item" data-tipo="{{ ['deporte', 'cultura', 'educacion'][$i % 3] }}">
                        <div class="card h-100 shadow-sm border-0 news-card overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ asset('assets/noticias/campeonato.jpg') }}" class="card-img-top transition-img"
                                    alt="Noticia {{ $i }}">
                                <span class="badge bg-danger position-absolute top-2 start-2 p-2"
                                    style="font-family: 'Inter', sans-serif;">{{ ['Deporte', 'Cultura', 'Educación'][$i % 3] }}</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: 'Inter', sans-serif;">Título noticia
                                    {{ $i }}</h5>
                                <p class="text-muted small" style="font-family: 'Inter', sans-serif;">📅 18 Agosto 2025</p>
                                <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-2"
                                    style="font-family: 'Inter', sans-serif;">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cargar más -->
            <div class="text-center mt-4">
                <button id="cargarMas" class="btn btn-secondary btn-md shadow-sm">Cargar más</button>
            </div>
        </div>

    </div>


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
    </script>
@endsection
