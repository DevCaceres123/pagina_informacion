@extends('principal')
@section('titulo', 'CARRERAS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Lista de carreras</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCarrera">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_carreras">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>CARRERA</th>
                                    <th>MODALIDAD</th>
                                    <th>SEDE</th>
                                    <th>ESTADO</th>
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCarrera" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-white">
                    <h5 class="modal-title">
                        <i class="ri-graduation-cap-line me-2"></i> Agregar Nueva Carrera
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCarrera" enctype="multipart/form-data">
                        @csrf

                        {{-- SELECCIONAR SEDE --}}
                        <div class="mb-3">
                            <label for="sede_id" class="form-label">
                                Sede <strong class="text-danger">*</strong>
                            </label>
                            <select name="sede_id" id="sede_id" class="form-select"  required>
                                <option selected disabled>Seleccione una sede</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="_sede_id" class="text-danger small"></div>
                        </div>

                        {{-- NOMBRE DE LA CARRERA --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre de la Carrera <strong class="text-danger">*</strong>
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control text-uppercase"
                                required>
                            <div id="_nombre" class="text-danger small"></div>
                        </div>

                        {{-- MODALIDAD --}}
                        <div class="mb-3">
                            <label for="modalidad" class="form-label">
                                Modalidad <strong class="text-danger">*</strong>
                            </label>
                            <select name="modalidad" id="modalidad" class="form-select" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Semestral">Semestral</option>
                                <option value="Anual">Anual</option>
                            </select>
                            <div id="_modalidad" class="text-danger small"></div>
                        </div>

                        {{-- MALLA CURRICULAR --}}
                        <div class="mb-3">
                            <label for="malla_curricular" class="form-label">
                                Malla Curricular (PDF)
                            </label>
                            <input type="file" name="malla_curricular" id="malla_curricular" class="form-control"
                                accept="application/pdf">
                            <div id="_malla_curricular" class="text-danger small"></div>
                        </div>

                        {{-- VINCULO WEB --}}
                        <div class="mb-3">
                            <label for="vinculo_web" class="form-label">
                                Vínculo Web
                            </label>
                            <input type="url" name="vinculo_web" id="vinculo_web" class="form-control"
                                placeholder="https://">
                            <div id="_vinculo_web" class="text-danger small"></div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-3-line me-1"></i> Guardar Carrera
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/carreras/carreras.js') }}" type="module"></script>
@endsection
