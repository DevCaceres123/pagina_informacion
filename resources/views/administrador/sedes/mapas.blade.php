@extends('principal')
@section('titulo', 'Mapa de Lugares y Puntos de Transbordo')

@section('contenido')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <div class="container mt-4 ">
        <h3 class="mb-3 bg-dark border-start border-5 border-primary py-3  text-light fw-bold text-center">REGISTRAR UBICACIÓN</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <form action="" id="formBuscarUbicacion" class="row">
                            <div class="col-12 col-md-4">
                                <div class="">
                                    <label class="form-label">Ingrese Latitud (X)</label>
                                    <input type="text" name="latitud" id="latitud" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="">
                                    <label class="form-label">Ingrese Longitud (Y)</label>
                                    <input type="text" name="longitud" id="longitud" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-md">
                                        Buscar Ubicación
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>


                </div>
            </div>
        </div>
        <form id="guardarUbicaciones">
            <div class="row">
                <!-- MAPA -->
                <div class="col-md-8 mb-3">
                    <div id="map" style="height: 500px; border: 1px solid #ccc;"></div>
                </div>

                <!-- PANEL DERECHO -->
                <div class="col-md-4 mb-3">
                    <div class="card mb-3">
                        <div class="card-header bg-danger text-light text-uppercase">Agregar ubicación</div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label">Nombre de la ubicación</label>
                                <input type="text" name="nombre" id="nombre" class="form-control">
                                <input type="hidden" name="idSede" id="idSede" value="{{ $sede->id ?? '' }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Tipo de geometría</label>
                                <select id="tipo" class="form-select" required>
                                    <option value="">Selecciona tipo</option>
                                    <option value="punto">🟢 Punto</option>
                                    <option value="poligono">🟩 Polígono</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="button" id="activarDibujo" class="btn btn-primary btn-sm">Agregar ubicación
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-danger text-light text-uppercase">Información Registrada</div>
                        <div class="card-body">
                            <h6>Puntos agregados</h6>
                            <hr>
                            <ul id="info-puntos" class="list-group small mb-1 text-capitalize text-muted"></ul>

                            <h6>Polígonos agregados</h6>
                            <hr>
                            <ul id="info-poligonos" class="list-group small text-capitalize text-muted"></ul>
                        </div>
                        <div class="text-end mb-2 me-3">
                            <button type="submit" class="btn btn-primary btn-sm">Guardar ubicación</button>
                        </div>
                    </div>
                </div>
            </div>



            <input type="hidden" name="geojson" id="geojson">


        </form>
    </div>

@endsection

@section('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        const POLIGONOS = @json($poligonos);
        const PUNTOS = @json($puntos);
    </script>
    <script src="{{ asset('js/modulos/sedes/mapas.js') }}" type="module"></script>

@endsection
