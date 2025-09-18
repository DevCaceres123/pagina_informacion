@extends('principal')
@section('titulo', 'INFRAESTRUCTURA SEDES')

@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg rounded-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">📂 Documentos de la Infraestructura</h5>
                </div>
                <div class="card-body">

                    {{-- 📌 Estado Inicial --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold text-primary">1️⃣ Estado: Inicial</h6>
                        <p class="text-muted small mb-2">Aquí debe cargarse la <strong>Solicitud</strong>.</p>

                        @if (!empty($infraestructura->solicitud))
                            <div class="mb-2">
                                <a href="{{ route('infraestructura.verDocumentos', ['tipo' => 'solicitud', 'id' => $infraestructura->id]) }}"
                                    target="_blank" class="btn btn-outline-success btn-sm">
                                    📄 Ver Solicitud
                                </a>
                            </div>
                        @endif

                        <form id="form-solicitud" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            
                            <input type="file" name="solicitud" class="form-control form-control-sm"
                                accept="application/pdf" id="solicitud">
                            <button type="button" class="btn btn-sm btn-primary btn-enviar" data-tipo="solicitud" data-id={{ $infraestructura->id }} >
                                ⬆️ Subir / Actualizar
                            </button>
                        </form>
                    </div>

                    {{-- 📌 Estado En Proceso --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold text-warning">2️⃣ Estado: En Proceso</h6>
                        <p class="text-muted small mb-2">Aquí debe cargarse la <strong>Nota</strong> y su número
                            correspondiente.</p>

                        @if (!empty($infraestructura->nota))
                            <div class="mb-2">
                                  <a href="{{ route('infraestructura.verDocumentos', ['tipo' => 'nota', 'id' => $infraestructura->id]) }}" target="_blank"
                                    class="btn btn-outline-success btn-sm">
                                    📄 Ver Nota
                                </a>
                                <span class="badge bg-secondary ms-2 fs-6">N° {{ $infraestructura->numero_nota ?? '-' }}</span>
                            </div>
                        @endif

                        <form id="form-nota" enctype="multipart/form-data" class="row g-2">
                            
                            <div class="col-md-6">
                                <input type="file" name="nota" class="form-control form-control-sm"
                                    accept="application/pdf" id="nota">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="numero_nota" class="form-control form-control-sm"
                                    placeholder="N° Nota" value="{{ $infraestructura->numero_nota ?? '' }}" id="numero_nota">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-warning w-100 btn-enviar" data-tipo="nota" data-id={{ $infraestructura->id }} >
                                    ⬆️ Guardar
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- 📌 Estado Finalizado --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold text-success">3️⃣ Estado: Finalizado</h6>
                        <p class="text-muted small mb-2">Aquí debe cargarse el <strong>Contrato</strong>.</p>

                        @if (!empty($infraestructura->contrato))
                            <div class="mb-2">
                                  <a href="{{ route('infraestructura.verDocumentos', ['tipo' => 'contrato', 'id' => $infraestructura->id]) }}" target="_blank"
                                    class="btn btn-outline-success btn-sm">
                                    📄 Ver Contrato
                                </a>
                            </div>
                        @endif

                        <form id="form-contrato" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            
                            <input type="file" name="contrato" class="form-control form-control-sm"
                                accept="application/pdf" id="contrato">
                            <button type="button" class="btn btn-sm btn-success btn-enviar" data-tipo="contrato" data-id={{ $infraestructura->id }} >
                                ⬆️ Subir / Actualizar
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src = "{{ asset('js/modulos/infraestructura/documentosInfraestructura.js') }}" type = "module" > </script>
@endsection
