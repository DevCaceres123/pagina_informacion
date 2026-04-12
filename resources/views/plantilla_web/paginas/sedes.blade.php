@extends('index')
@section('sedes', 'SEDES')

@section('contenido')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <style>
        /* ---------------------------------------------------- */
        /* GENERALES UI/UX */
        /* ---------------------------------------------------- */
        body {
            background-color: #f8f9fa;
            /* Fondo muy claro para destacar las tarjetas */
        }

        /* Títulos de Secciones */
        .section-title-icon {
            color: #343a40;
            border-bottom: 2px solid #007bff;
            /* Subrayado temático */
            padding-bottom: 5px;
            display: inline-block;
            font-size: 1.5rem;
        }

        /* ---------------------------------------------------- */
        /* HEADER PRINCIPAL */
        /* ---------------------------------------------------- */
        .header-sede {
            background: #ffffff;
            /* Fondo blanco */
            border: 1px solid #e0e0e0;
        }

        /* Botones de Redes Sociales */
        .social-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .social-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* ---------------------------------------------------- */
        /* MAPA Y GALERÍA */
        /* ---------------------------------------------------- */
        .map-container-final {
            width: 100%;
            height: 450px;
            /* Altura cómoda para el mapa */
            border-radius: 12px;
            overflow: hidden;
            /* Asegura que el mapa se integre al contenedor */
        }

        .map-container-final #map {
            width: 100%;
            height: 100%;
            border-radius: 12px;
        }

        .gallery-preview-card .btn-primary {
            /* Botón de galería destacado */
            background: linear-gradient(45deg, #007bff, #00b4d8);
            border: none;
        }

        .gallery-preview-card .btn-primary:hover {
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        /* ---------------------------------------------------- */
        /* CARRERAS */
        /* ---------------------------------------------------- */
        .card-carreras-final {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
        }

        /* Estilo de búsqueda */
        .input-search-custom {
            border-radius: 50px;
        }

        .btn-listado {
            border-radius: 50px;
            background-color: #6c757d;
            /* Gris para listar */
            border-color: #6c757d;
        }

        /* Tarjeta de carrera individual */
        .card-carrera-item {
            border: 1px solid #e0e0e0 !important;
            transition: transform 0.2s ease;
        }

        .card-carrera-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #007bff !important;
        }

        /* ---------------------------------------------------- */
        /* RESOLUCIÓN */
        /* ---------------------------------------------------- */
        .card-resolution-final {
            background-color: #fffde7;
            /* Fondo amarillo muy claro para énfasis */
            border: none;
            padding: 2rem !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .card-resolution-final .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #343a40;
        }

        .card-resolution-final .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }

        /* ---------------------------------------------------- */
        /* MODAL GALERÍA */
        /* ---------------------------------------------------- */
        .gallery-thumbnail {
            height: 150px;
            /* Altura uniforme para todas las miniaturas */
            object-fit: cover;
            transition: transform 0.2s ease;
        }

        .gallery-thumbnail:hover {
            transform: scale(1.05);
        }
    </style>
    <div class="container my-5 pt-6 page-sede-final">

        {{-- BLOQUE PRINCIPAL: Título, Descripción y Redes (Cabecera) --}}
        <div class="header-sede mb-5 p-4 rounded-4 shadow-lg text-center">
            <h1 id="nombreSede" class="fw-bolder display-4 mb-2 text-uppercase text-primary-emphasis">{{ $sedeUnica->nombre }}
            </h1>
            <p id="descripcionSede" class="lead text-muted mb-4 px-lg-5">
                Contamos con infraestructura moderna, laboratorios equipados y áreas recreativas.
            </p>

            {{-- Redes sociales (Integradas en el encabezado) --}}
            <h5 class="fw-bold mb-3 mt-4 text-secondary-emphasis">¡Síguenos y Contáctanos!</h5>
            <div class="d-flex justify-content-center gap-3 social-links">
                {{-- WhatsApp --}}
                <a href="https://api.whatsapp.com/send?phone={{ $sedeUnica->whatsapp }}&text=Hola%20me%20gustaria%20que%20me%20ayuden%20%20en%20una%20duda"
                    target="_blank" class="btn btn-success social-btn whatsapp-btn" title="WhatsApp">
                    <i class="fab fa-whatsapp fa-2x"></i>
                </a>

                {{-- Facebook --}}
                <a href="{{ $sedeUnica->facebook }}" target="_blank" class="btn btn-primary social-btn facebook-btn"
                    title="Facebook">
                    <i class="fab fa-facebook-f fa-2x"></i>
                </a>

                {{-- YouTube --}}
                <a href="{{ $sedeUnica->youtobe }}" target="_blank" class="btn btn-danger social-btn youtube-btn"
                    title="YouTube">
                    <i class="fab fa-youtube fa-2x"></i>
                </a>
            </div>

        </div>

        <div class="row g-5">

            {{-- COLUMNA IZQUIERDA: MAPA Y GALERÍA (Visual) --}}
            <div class="col-lg-7">

                {{-- SECCIÓN MAPA --}}
                <div class="mb-5">
                    <h3 class="fw-bold mb-3 section-title-icon"><i class="fas fa-map-marked-alt me-2 text-primary"></i>
                        Ubicación</h3>
                    <div id="map" class="map-container-final shadow-lg"></div>
                </div>

                {{-- SECCIÓN GALERÍA --}}
                <div class="mb-5">
                    <h3 class="fw-bold mb-3 section-title-icon"><i class="fas fa-camera-retro me-2 text-primary"></i>
                        Instalaciones</h3>
                    <div class="card p-4 rounded-4 shadow-sm gallery-preview-card">
                        <p class="text-muted mb-3">Haz clic en <b>Ver Galería</b> y podrás visualizar imágenes de la sede</p>
                        <button class="btn btn-primary btn-lg fw-bold w-100" data-bs-toggle="modal"
                            data-bs-target="#galeriaModal">
                            <i class="fas fa-images me-2"></i> Ver Galería
                        </button>
                    </div>
                </div>
                {{-- SECCIÓN RESOLUCIÓN (CORREGIDA: Ahora es su propia tarjeta fuera de Carreras) --}}
                @if ($sedeUnica->publicar_resolucion == 'activo')
                    <div class="card card-resolution-final shadow-sm p-4 rounded-4 border-start border-warning border-5">
                        <h4 class="fw-bold mb-3 section-title-icon"><i class="fas fa-file-contract me-2 text-warning"></i>
                            Documento Legal</h4>
                        <p class="">Resolución:</p>
                        <p class="fw-bolder text-primary fs-3">{{ $sedeUnica->resolucion ?? 'Ninguno::' }}</p>
                        <a href="{{ asset('storage/resoluciones/' . $sedeUnica->resolucion_pdf) }}"
                            id="btnDescargarResolucion" class="btn btn-warning fw-bold w-100 btn-lg shadow-sm" download>
                            <i class="fas fa-download me-2"></i> Descargar Resolución (PDF)
                        </a>
                    </div>
                @endif


            </div>

            <div class="col-lg-5">

                {{-- SECCIÓN CARRERAS --}}
                <div class="card card-carreras-final shadow-lg p-4 rounded-4 mb-5 h-100">
                    <h3 class="fw-bold mb-4 section-title-icon">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i> Carreras Disponibles
                    </h3>

                    {{-- Buscador y total --}}
                    <div class="d-flex flex-column gap-2 mb-4">
                        <div class="d-flex gap-2 align-items-center">

                            <input type="text" id="buscadorCarreras" class="form-control form-sm input-search-custom"
                                placeholder="🔍 Buscar carrera...">
                            <input type="hidden" name="sede_id" id="sede_id" value="{{ encrypt($sedeUnica->id) }}">
                            {{-- <button class="btn btn-danger flex-shrink-0 btn-listado" id="listarTodo">
                                <i class="fas fa-list-ul"></i> Todo
                            </button> --}}
                        </div>
                        <small class="text-muted ms-3">
                            Total de carreras existentes en esta sede: <strong>{{ $totalCarreras }}</strong>
                        </small>
                    </div>

                    {{-- Carreras --}}
                    <div class="row g-3 carreras-grid-final" id="contenedorCarreras">
                        @forelse ($carreras as $carrera)
                            <div class="col-6">
                                <div class="card card-carrera-item h-100 border-0 shadow-sm">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <h6 class="card-title fw-bold mb-2 text-uppercase">{{ $carrera->nombre }}</h6>
                                        <div class="mt-3 btn-group-vertical w-100" role="group">
                                            @if ($carrera->malla_curricular_pdf)
                                                <a href="{{ asset('storage/mallas_curriculares/' . $carrera->malla_curricular_pdf) }}"
                                                    class="btn btn-danger btn-sm w-100 btn-action-malla" download>
                                                    <i class="fas fa-download me-1"></i> Malla Curricular
                                                </a>
                                            @endif
                                            <a href="{{ $carrera->vinculo_web ?? '#' }}"
                                                class="btn btn-outline-dark btn-sm w-100 btn-action-web"
                                                @empty($carrera->vinculo_web) onclick="return false;" @endempty>
                                                <i class="fas fa-external-link-alt me-1"></i> Ver Página
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No hay carreras registradas en esta sede.</p>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL GALERÍA (Mantenido y Mejorado Ligeramente) --}}
    <div class="modal fade" id="galeriaModal" tabindex="-1" aria-labelledby="galeriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow rounded-4">
                <div class="modal-header modal-header-custom py-3">
                    <h5 class="modal-title fw-bold text-uppercase">Galería de Imágenes de {{ $sedeUnica->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="gallery-sede-modal">
                        <div class="row g-3">
                            @foreach ($sedeUnica->imagenesSede as $img)
                                <div class="col-6 col-sm-4 col-md-3">
                                    <a href="{{ asset('storage/galeria_sedes/' . $img->imagen) }}"
                                        class="lightbox d-block">
                                        <img src="{{ asset('storage/galeria_sedes/' . $img->imagen) }}"
                                            class="img-fluid rounded shadow-sm gallery-thumbnail" alt="Imagen Sede">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
    <script>
        baguetteBox.run('.gallery-sede-modal');
    </script>

    <script src="{{ asset('js/modulos/pagina/sedes.js') }}" type="module"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const poligonos = @json($poligonos);
            const puntos = @json($puntos);

            // Inicializar mapa centrado
            const map = L.map('map').setView([-16.5322, -68.2027], 13);

            // Capa base
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // 🎨 Colores para los polígonos
            const colors = ["#880000", "#007bff", "#28a745", "#ffc107"];

            // ======================
            // 📍 DIBUJAR POLÍGONOS
            // ======================
            poligonos.forEach((pol, index) => {
                const color = colors[index % colors.length]; // alterna colores
                const layer = L.geoJSON(pol.geometry, {
                    style: {
                        color: color,
                        weight: 2,
                        fillColor: color,
                        fillOpacity: 0.4
                    }
                }).addTo(map);

                // Tooltip permanente
                layer.bindTooltip(pol.ubicacion, {
                    permanent: true,
                    direction: "top"
                });

                // Popup con enlace a Google Maps
                const center = layer.getBounds().getCenter();
                layer.bindPopup(`
            <b>${pol.ubicacion}</b><br>
            <a class="btn btn-sm btn-success mt-2" 
               href="https://www.google.com/maps/dir/?api=1&destination=${center.lat},${center.lng}" 
               target="_blank">
               🚗 Cómo llegar
            </a>
        `);
            });

            // ======================
            // 🟢 DIBUJAR PUNTOS
            // ======================
            const puntoIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854878.png', // puedes cambiarlo
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -28]
            });


            // Mostrar PUNTOS existentes en el mapa y en el panel
            puntos.forEach(function(punto) {
                if (punto.geometry && punto.geometry.coordinates) {

                    // 🧠 Construimos un objeto Feature válido para Leaflet
                    const feature = {
                        type: "Feature",
                        geometry: punto.geometry,
                        properties: {
                            id: punto.id,
                            ubicacion: punto.ubicacion
                        }
                    };

                    // 🗺️ Crear la capa GeoJSON del punto
                    const layer = L.geoJSON(feature, {
                        pointToLayer: function(feature, latlng) {
                            return L.marker(latlng);
                        },
                        onEachFeature: function(feature, layer) {
                            layer._idBD = feature.properties.id;

                            const [lng, lat] = feature.geometry.coordinates;

                            // Popup con enlace a Google Maps
                            layer.bindPopup(`
                    <b>${feature.properties.ubicacion}</b><br>
                    <a class="btn btn-sm btn-success mt-2" 
                       href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" 
                       target="_blank">
                       🚗 Cómo llegar
                    </a>
                `);
                        }
                    }).bindTooltip(punto.ubicacion, {
                        permanent: true,
                        direction: "top",
                    });

                    layer.addTo(map);

                    // Mostrar en el panel de información
                    const li = document.createElement("p");
                    li.textContent = `${punto.ubicacion}`;

                }
            });


            // ======================
            // 💬 MENSAJE GUIA
            // ======================
            L.control
                .attribution({
                    prefix: ""
                })
                .addAttribution("🖱️ Haz click en la ubicación o punto para ver cómo llegar")
                .addTo(map);
        });
    </script>
@endsection
