@extends('index')
@section('sedes', 'SEDES')

@section('contenido')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <div class="container my-5 pt-6">

        {{-- Información de la sede --}}
        <div class="card shadow-sm p-4 mb-5">
            <h2 id="nombreSede" class="fw-bold mb-3 text-uppercase">{{ $sedeUnica->nombre }}</h2>
            <p id="descripcionSede" class="text-muted">Contamos con infraestructura moderna, laboratorios equipados y áreas
                recreativas.</p>

            {{-- Mapa --}}
            <section class="pt-0 mt-5">
                <div class="container">
                    <div class="row flex-center text-center pb-6">
                        <div class="col-12">
                            <div id="map" style="width: 100%; height: 500px; border-radius: 10px;"></div>
                        </div>
                    </div>
                </div>
            </section>

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
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
    <script>
        baguetteBox.run('.gallery');
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
