@extends('principal')
@section('titulo', 'Todas las Ubicaciones')

@section('contenido')

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-info py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-map-marked-alt me-2"></i> Todas las Ubicaciones
                            </h4>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <select id="filtroSede" class="form-select form-select-sm" style="min-width: 220px;">
                                <option value="">Todas las sedes</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ strtoupper($sede->nombre) }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('sedes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="position: relative;">
                    <div id="map" style="height: 630px;"></div>
                    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1000;">
                        <button id="btnTogglePuntos" class="btn btn-sm btn-outline-light"
                            style="background:rgba(30,30,30,0.75); border-color:#aaa; color:#fff; backdrop-filter:blur(4px);">
                            <i class="fas fa-bus me-1"></i> Mostrar puntos de abordaje
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        const UBICACIONES = @json($ubicaciones);
        const SEDE_EDIT_URL = "{{ url('admin/ubicacionSede') }}";
    </script>
    <script src="{{ asset('js/modulos/sedes/ubicaciones.js') }}"></script>
@endsection
