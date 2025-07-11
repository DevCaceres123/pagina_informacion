@extends('index')
@section('sedes', 'SEDES')

@section('contenido')
    <div class="container my-5">

        {{-- Título principal --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold">Nuestras Sedes</h1>
            <p class="text-muted">Explora nuestras sedes, carreras disponibles y conoce nuestra ubicación.</p>
        </div>

        {{-- Selector de sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <label for="sedeSelect" class="form-label fw-semibold">Selecciona una sede:</label>
            <select id="sedeSelect" class="form-select">
                <option value="sede1">Sede Central</option>
                <option value="sede2">Sede Norte</option>
                <option value="sede3">Sede Sur</option>
            </select>
        </div>

        {{-- Información de la sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <h2 id="nombreSede" class="fw-bold mb-3">Sede Villa Esperanza</h2>
            <p id="descripcionSede" class="text-muted">Contamos con infraestructura moderna, laboratorios equipados y áreas
                recreativas.</p>

            {{-- Mapa --}}
            <div class="ratio ratio-16x9 rounded overflow-hidden mb-4">
                <iframe id="mapaSede" src="https://www.google.com/maps/embed?pb=!1m18..." allowfullscreen loading="lazy"
                    class="border rounded"></iframe>
            </div>

            {{-- Botón galería --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#galeriaModal">
                📸 Ver Galería de Imágenes
            </button>
        </div>

        {{-- Resolución de la sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <h4 class="fw-bold mb-2">Resolución de Funcionamiento</h4>
            <p class="mb-3">Número de resolución: <span class="fw-semibold text-primary">RES-2025-00123</span></p>
            <a href="{{ asset('resoluciones/sede_central_resolucion.pdf') }}" class="btn btn-success" download>
                📄 Descargar Resolución
            </a>
        </div>

        {{-- Carreras --}}
        <div class="card shadow-sm p-4">
            <h3 class="fw-bold mb-4">Carreras disponibles en esta sede</h3>
            <input type="text" id="buscadorCarreras" class="form-control mb-4" placeholder="🔍 Buscar carrera...">

            <div class="row g-3" id="listaCarreras">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">💻 Ingeniería de Sistemas</h5>
                            <div class="mt-3">
                                <a href="{{ asset('mallas/ingenieria_sistemas.pdf') }}"
                                    class="btn btn-outline-primary btn-sm mb-1 w-100" download>
                                    📥 Descargar Malla
                                </a>
                                <a href="https://example.com/ingenieria-sistemas" target="_blank"
                                    class="btn btn-outline-secondary btn-sm w-100">
                                    🌐 Ver Página
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">📊 Administración de Empresas</h5>
                            <div class="mt-3">
                                <a href="{{ asset('mallas/administracion_empresas.pdf') }}"
                                    class="btn btn-outline-primary btn-sm mb-1 w-100" download>
                                    📥 Descargar Malla
                                </a>
                                <a href="https://example.com/administracion-empresas" target="_blank"
                                    class="btn btn-outline-secondary btn-sm w-100">
                                    🌐 Ver Página
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">🧮 Contaduría Pública</h5>
                            <div class="mt-3">
                                <a href="{{ asset('mallas/contaduria_publica.pdf') }}"
                                    class="btn btn-outline-primary btn-sm mb-1 w-100" download>
                                    📥 Descargar Malla
                                </a>
                                <a href="https://example.com/contaduria-publica" target="_blank"
                                    class="btn btn-outline-secondary btn-sm w-100">
                                    🌐 Ver Página
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Agrega más carreras aquí --}}
            </div>
        </div>
    </div>

    {{-- Redes sociales --}}
    <div class="mt-3 mb-5">
        <h5 class="text-center">
            <i class="fas fa-share-alt me-1"></i> Nuestras Redes Sociales
        </h5>
        <div class="d-flex justify-content-center gap-3 mt-2">
            {{-- WhatsApp --}}
            <a href="https://wa.me/" target="_blank"
                class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="WhatsApp">
                <i class="fab fa-whatsapp fa-lg"></i>
            </a>

            {{-- Facebook --}}
            <a href="#" target="_blank"
                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="Facebook">
                <i class="fab fa-facebook-f fa-lg"></i>
            </a>

            {{-- YouTube --}}
            <a href="#" target="_blank"
                class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="YouTube">
                <i class="fab fa-youtube fa-lg"></i>
            </a>
        </div>
    </div>

    {{-- Modal Galería --}}
    <div class="modal fade" id="galeriaModal" tabindex="-1" aria-labelledby="galeriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">Galería de Imágenes de la Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="carouselGaleria" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('img/sede1_foto1.jpg') }}" class="d-block w-100 rounded"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('img/sede1_foto2.jpg') }}" class="d-block w-100 rounded"
                                    alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('img/sede1_foto3.jpg') }}" class="d-block w-100 rounded"
                                    alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleria"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleria"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('buscadorCarreras').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const cards = document.querySelectorAll('#listaCarreras .card');
            cards.forEach(function(card) {
                const titulo = card.querySelector('.card-title').textContent.toLowerCase();
                card.parentElement.style.display = titulo.includes(filtro) ? '' : 'none';
            });
        });
    </script>
@endsection
