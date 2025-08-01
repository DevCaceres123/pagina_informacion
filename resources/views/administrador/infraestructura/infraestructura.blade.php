@extends('principal')
@section('titulo', 'INFRAESTRUCTURA SEDES')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Lista de Infraestructuras</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInfraestructura">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_infraestructura">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>SEDE</th>
                                    <th>ESTADO INMUEBLE</th>
                                    <th>TRAMITE</th>
                                    <th>UBICACION</th>
                                    <th>DISTRITO</th>
                                    <th>ACCION</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="modalInfraestructura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalSedeLabel" aria-hidden="true">
        <div class="modal-dialog modal-center modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-light">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded">
                            <i class="fas fa-university me-1"></i> CREAR NUEVA INFRAESTRUCTURA
                        </span>
                    </h4>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formInfraestructura" enctype="multipart/form-data">
                        <div class="container">

                            {{-- SECCIÓN 1: SEDE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:40%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-building me-1"></i> SEDE
                                    </div>
                                </div>

                                <div class="form-group py-2 col-12 col-md-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-location-dot me-1"></i> SELECCIONE UNA SEDE <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <select name="sede_id" class="form-select text-capitalize" required>
                                        <option value="" disabled selected>Seleccione una sede</option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>

                            {{-- SECCIÓN 2: ESTADO DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-home me-1"></i> DATOS DEL INMUEBLE
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-warehouse me-1"></i> ESTADO DEL INMUEBLE <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <select name="estado_inmueble" class="form-select" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="bueno">Bueno</option>
                                        <option value="mediano">Mediano</option>
                                        <option value="malo">Malo</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> OBSERVACIÓN <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a comment here" id="observacion_estado"  style="height: 100px"></textarea>
                                        <label for="observacion_estado">Ingrese alguna observacion del inmueble</label>
                                    </div>
                                </div>
                            </div>

                            {{-- SECCIÓN 3: CONTRATO --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-file-contract me-1"></i> DATOS DEL CONTRATO
                                    </div>
                                </div>



                                <div class="col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i> FECHA DE INICIO <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="date" class="form-control" name="fecha_inicio" required>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i> FECHA FINAL <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="date" class="form-control" name="fecha_final" required>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-upload me-1"></i> CONTRATO (PDF) <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" class="form-control" name="contrato" accept=".pdf" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Cerrar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal para ver la resolución --}}
    <div class="modal fade" id="modalVerResolucion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf me-2"></i> Visualización de Resolución
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="border rounded shadow-sm overflow-hidden mb-3" style="height:600px;">
                        <iframe id="iframeResolucion" src="" width="100%" height="100%"
                            style="border: none;"></iframe>
                    </div>

                    <div class="mb-3">
                        <label for="nuevoPdf" class="form-label fw-bold">
                            <i class="ri-upload-cloud-line me-1"></i> Seleccionar nuevo archivo de Resolución (PDF)
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
                    <button id="btnActualizarPdf" class="btn btn-success">
                        <i class="ri-upload-cloud-line me-1"></i> Subir nuevo PDF
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de cambio de estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-2 border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-retweet me-2"></i> Cambiar Estado
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formCambioEstado">
                    <input type="hidden" id="idElementoEstado" name="id">

                    <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                        <div class="position-absolute" style="top:0px; left:35%; margin-top: -15px;">
                            <div class="d-inline p-1 border rounded border-primary bg-primary text-light">
                                <i class="fas fa-cogs me-1"></i> CAMBIO DE ESTADO
                            </div>
                        </div>

                        <div class="form-group py-2 col-md-12 mt-3">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-1"></i> Estado <strong class="text-danger">(*)</strong>
                            </label>
                            <select class="form-select" name="estado" id="estado_select" required>
                                <option value="">-- Seleccione --</option>
                                <option value="inicial">Inicial</option>
                                <option value="proceso">En Proceso</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>

                        <div class="form-group py-2 col-md-6 mt-3 d-none" id="nota_input_group">
                            <label class="form-label">
                                <i class="fas fa-sticky-note me-1"></i> Número de Nota <strong class="text-danger">(*)</strong>
                            </label>
                            <input type="text" class="form-control" name="numero_nota" id="numero_nota_input">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="guardarEstadoBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>





@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/infraestructura/infraestructura.js') }}" type="module"></script>
@endsection
