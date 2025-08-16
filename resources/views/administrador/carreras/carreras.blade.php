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
                                    <th>SEDES</th>
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

    {{-- modal nueva carrera --}}
    <div class="modal fade" id="modalCarrera" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-white">
                    <span class="badge badge-outline-light rounded">
                        <i class="fas fa-university me-1"></i> CREAR NUEVA CARRERA
                    </span>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formCarrera" enctype="multipart/form-data">
                        @csrf

                        {{-- SELECCIONAR SEDE --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-school me-1"></i> SELECCIONAR SEDES <strong
                                    class="text-danger">(*)</strong>
                            </label>
                            <select name="sede_id[]" id="sede_id" class=" text-capitalize" multiple required>

                                @foreach ($sedes as $sede)
                                    <option class="text-capitalize" value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="_sede_id" class="text-danger small"></div>
                        </div>

                        {{-- NOMBRE DE LA CARRERA --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i> NOMBRE DE LA CARRERA <strong
                                    class="text-danger">(*)</strong>
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control text-uppercase"
                                required>
                            <div id="_nombre" class="text-danger small"></div>
                        </div>

                        {{-- MODALIDAD --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-1"></i>MODALIDAD <strong class="text-danger">(*)</strong>
                            </label>
                            <select name="modalidad" id="modalidad" class="form-select" required>
                                <option value="">Seleccione una opción</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                            </select>
                            <div id="_modalidad" class="text-danger small"></div>
                        </div>

                        {{-- MALLA CURRICULAR --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf me-1"></i>MALLA CURRICULAR (PDF) <strong
                                    class="text-danger"></strong>
                            </label>
                            <input type="file" name="malla_curricular" id="malla_curricular" class="form-control"
                                accept="application/pdf">
                            <div id="_malla_curricular" class="text-danger small"></div>
                        </div>

                        {{-- VINCULO WEB --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-globe me-1"></i>SITIO WEB<strong class="text-danger"></strong>
                            </label>
                            <input type="url" name="vinculo_web" id="vinculo_web" class="form-control"
                                placeholder="https://">
                            <div id="_vinculo_web" class="text-danger small"></div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarCarrera">
                                <i class="fas fa-save me-1"></i> Guardar Carrera
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-1"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal editar carrera --}}
    <div class="modal fade" id="modalCarreraEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-white">
                    <span class="badge badge-outline-light rounded">
                        <i class="fas fa-university me-1"></i> CREAR NUEVA CARRERA
                    </span>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formCarreraEditar">

                        {{-- SELECCIONAR SEDE --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-school me-1"></i> SELECCIONAR SEDE <strong
                                    class="text-danger">(*)</strong>
                            </label>
                            <select name="sede_id_edit[]" id="sede_id_edit" class="form-select text-capitalize" required
                                multiple>

                                @foreach ($sedes as $sede)
                                    <option value="{{ (string)$sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="_sede_id_edit" class="text-danger small"></div>
                        </div> --}}

                        {{-- NOMBRE DE LA CARRERA --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i> NOMBRE DE LA CARRERA <strong
                                    class="text-danger">(*)</strong>
                            </label>
                            <input type="hidden" name="id_carrera" id="id_carrera">
                            <input type="text" name="nombre_edit" id="nombre_edit"
                                class="form-control text-uppercase" required>
                            <div id="_nombre_edit" class="text-danger small"></div>
                        </div>

                        {{-- MODALIDAD --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-1"></i>MODALIDAD <strong class="text-danger">(*)</strong>
                            </label>
                            <select name="modalidad_edit" id="modalidad_edit" class="form-select" required>
                                <option value="">Seleccione una opción</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                            </select>
                            <div id="_modalidad_edit" class="text-danger small"></div>
                        </div>

                        {{-- VINCULO WEB --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-globe me-1"></i>SITIO WEB<strong class="text-danger"></strong>
                            </label>
                            <input type="url" name="vinculo_web_edit" id="vinculo_web_edit" class="form-control"
                                placeholder="https://">
                            <div id="_vinculo_web_edit" class="text-danger small"></div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarCarreraedit">
                                <i class="fas fa-save me-1"></i> Guardar Carrera
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-1"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal para ver la malla curicular --}}
    <div class="modal fade" id="modalVerMalla" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf me-2"></i> Visualización de Malla Curricular
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="border rounded shadow-sm overflow-hidden mb-3" style="height:600px;">
                        <iframe id="iframeMalla" src="" width="100%" height="100%"
                            style="border: none;"></iframe>
                    </div>

                    <div class="mb-3">
                        <label for="nuevoPdf" class="form-label fw-bold">
                            <i class="ri-upload-cloud-line me-1"></i> Seleccionar nueva Malla curiicular (PDF)
                        </label>
                        <input type="file" id="nuevoPdf" name="nuevoPdf" class="form-control"
                            accept="application/pdf">
                        <div id="nombreArchivoSeleccionado" class="text-muted mt-1"></div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                    <button id="btnActualizarMalla" class="btn btn-success">
                        <i class="ri-upload-cloud-line me-1"></i> Subir nuevo PDF
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal Ver Sedes -->
    <div class="modal fade" id="modalSedes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-graduation-cap me-2"></i>Sedes Asignadas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_carreraEdit" id="id_carreraEdit">
                    <ul id="listarSedes" class="list-group list-group-flush text-capitalize p-2">
                        <!-- Carreras se llenan dinámicamente -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>
                        Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Inicialización de Selectr
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('sede_id');
            const selectElement2 = document.getElementById('sede_id_edit');

            let selectrInstanceSede = new Selectr(selectElement, {
                searchable: true,
                placeholder: 'Busca o selecciona una opción...'
            });

    

        });
    </script>

    <script src="{{ asset('js/modulos/carreras/carreras.js') }}" type="module"></script>


@endsection
