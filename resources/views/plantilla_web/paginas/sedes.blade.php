@extends('index')
@section('sedes', 'SEDES')

@section('contenido')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css" />

    <div class="container my-5">

        {{-- Título principal --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold">Nuestras Sedes</h1>
            <p class="text-muted">Explora nuestras sedes, carreras disponibles y conoce nuestra ubicación.</p>
        </div>

        {{-- Selector de sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <label for="sedeSelect" class="form-label fw-semibold">Selecciona una sede:</label>
            <select id="sedeSelect" class="form-select text-capitalize">
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                @endforeach

            </select>
        </div>
        {{-- Información de la sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <h2 id="nombreSede" class="fw-bold mb-3 text-uppercase">{{ $sedeUnica->nombre }}</h2>
            <p id="descripcionSede" class="text-muted">Contamos con infraestructura moderna, laboratorios equipados y áreas
                recreativas.</p>

            {{-- Mapa --}}
            <div class="ratio ratio-16x9 rounded overflow-hidden mb-4">
                <iframe id="mapaSede" src="https://www.google.com/maps/embed?pb=!1m18..." allowfullscreen loading="lazy"
                    class="border rounded"></iframe>
            </div>

            {{-- Botón galería --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#galeriaModal">
                📸 Galería de Imágenes
            </button>


        </div>

        {{-- Resolución de la sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <h4 class="fw-bold mb-2">Resolución de Funcionamiento</h4>
            <p class="mb-3">Número de resolución: <span class="fw-semibold text-primary">RES-2025-00123</span></p>
            <a href="{{ asset('storage/resoluciones/' . $sedeUnica->resolucion_pdf) }}" class="btn btn-success" download>
                📄 Descargar Resolución
            </a>
        </div>

        {{-- Carreras --}}
        <div class="card shadow-sm p-4">
            <h3 class="fw-bold mb-4">Carreras disponibles en esta sede</h3>
            <div class="d-flex gap-2 align-items-center mb-3">
                <input type="text" id="buscadorCarreras" class="form-control form-sm" placeholder="🔍 Buscar carrera...">
                <button class="btn btn-danger flex-shrink-0" id="listarTodo"> Listar Todo</button>
            </div>


            <div class="row g-3" id="contenedorCarreras">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title"><i class="fas fa-laptop-code me-2 text-primary"></i> Ingeniería de
                                Sistemas</h5>

                            <div class="mt-3">
                                <a href="{{ asset('mallas/ingenieria_sistemas.pdf') }}"
                                    class="btn btn-danger btn-sm mb-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                    download>
                                    <i class="fas fa-download"></i> Descargar Malla
                                </a>
                                <a href="https://example.com/ingenieria-sistemas" target="_blank"
                                    class="btn btn-outline-dark btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-globe"></i> Ver Página
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
            <a href="https://api.whatsapp.com/send?phone={{ $sedeUnica->whatsapp }}&text=Hola%20me%20gustaria%20que%20me%20ayuden%20%20en%20una%20duda"
                target="_blank" class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="WhatsApp">
                <i class="fab fa-whatsapp fa-lg"></i>
            </a>

            {{-- Facebook --}}
            <a href="{{ $sedeUnica->facebook }}" target="_blank"
                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="Facebook">
                <i class="fab fa-facebook-f fa-lg"></i>
            </a>

            {{-- YouTube --}}
            <a href="{{ $sedeUnica->youtobe }}" target="_blank"
                class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                style="width: 70px; height: 70px;" title="YouTube">
                <i class="fab fa-youtube fa-lg"></i>
            </a>
        </div>
    </div>




    {{-- Modal Galería --}}
    <div class="modal fade" id="galeriaModal" tabindex="-1" aria-labelledby="galeriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">Galería de Imágenes de la Sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="container gallery mt-3 p-5">
                        <div class="row">
                            @foreach ($sedeUnica->imagenesSede as $img)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-4 mb-3">
                                    <a href="{{ asset('storage/galeria_sedes/' . $img->imagen) }}" class="lightbox">
                                        <img src="{{ asset('storage/galeria_sedes/' . $img->imagen) }}"
                                            class="img-fluid rounded shadow-sm border border-1 rounded" alt="Imagen Sede"
                                            style="width: 350px; height: 200px; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Antes de cerrar </body> -->

@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
    <script>
        baguetteBox.run('.gallery');
    </script>

    <script src="{{ asset('js/modulos/pagina/sedes.js') }}" type="module"></script>
@endsection
