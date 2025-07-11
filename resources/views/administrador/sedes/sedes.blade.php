@extends('principal')
@section('titulo', 'USUARIOS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Lista de sedes</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSede">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_sedes">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>SEDE</th>
                                    <th>RESOLUCION</th>
                                    <th>CARREAS</th>
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

    <!-- MODAL -->
    <div class="modal fade" id="modalSede" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalSedeLabel" aria-hidden="true">
        <div class="modal-dialog modal-center modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-light">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded">
                            <i class="fas fa-university me-1"></i> CREAR NUEVA SEDE
                        </span>
                    </h4>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaSede" enctype="multipart/form-data">

                        <div class="container">

                            {{-- SECCIÓN: DATOS DE LA SEDE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:35%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-building me-1"></i> DATOS DE LA SEDE
                                    </div>
                                </div>

                                {{-- Nombre --}}
                                <div class="form-group py-2 col-12 col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-tag me-1"></i> NOMBRE DE LA SEDE <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="text" class="form-control rounded text-uppercase" name="nombre"
                                        id="nombre" required>
                                    <div id="_nombre">

                                    </div>
                                </div>

                                {{-- Descripción --}}
                                <div class="form-group py-2 col-12 col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-align-left me-1"></i> DESCRIPCIÓN <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <textarea name="descripcion" id="descripcion" rows="2" class="form-control rounded text-uppercase" required></textarea>
                                    <div id="_descripcion">

                                    </div>
                                </div>
                            </div>

                            {{-- SECCIÓN: DATOS DE RESOLUCIÓN --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:31%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-file-alt me-1"></i> DATOS DE LA RESOLUCIÓN
                                    </div>
                                </div>

                                {{-- Número de resolución --}}
                                <div class="form-group py-2 col-12 col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-hashtag me-1"></i> NÚMERO DE RESOLUCIÓN <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="text" class="form-control rounded text-uppercase"
                                        name="resolucion_numero" id="resolucion_numero" required>
                                    <div id="_resolucion_numero">
                                    </div>
                                </div>

                                {{-- Archivo de resolución --}}
                                <div class="form-group py-2 col-12 col-md-6 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-file-pdf me-1"></i> ARCHIVO DE RESOLUCIÓN (PDF) <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" class="form-control rounded" name="resolucion_archivo"
                                        id="resolucion_archivo" accept="application/pdf" required>
                                    <div id="_resolucion_archivo">
                                    </div>
                                </div>
                            </div>


                            {{-- SECCIÓN: IMÁGENES DE LA SEDE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-images me-1"></i> IMÁGENES DE LA SEDE
                                    </div>
                                </div>

                                {{-- Drag and Drop Galería --}}
                                <div class="form-group py-2 col-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-upload me-1"></i> SUBIR IMÁGENES
                                    </label>
                                    <div class="border border-2 border-dashed rounded p-3 text-center bg-light"
                                        id="dropzoneGaleria">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Arrastra las imágenes aquí o haz clic para seleccionar
                                        </p>
                                        <input type="file" class="form-control mt-2" name="galeria[]" id="galeria"
                                            accept="image/*" multiple>
                                        <div id="_galeria[]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- SECCIÓN: UBICACIÓN --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:38%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-map-marker-alt me-1"></i> UBICACIÓN
                                    </div>
                                </div>

                                {{-- URL de Google Maps --}}
                                <div class="form-group py-2 col-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-link me-1"></i> URL DE GOOGLE MAPS
                                    </label>
                                    <input type="url" class="form-control rounded" name="mapa_url" id="mapa_url"
                                        placeholder="https://www.google.com/maps/embed?...">
                                    <div id="_mapa_url">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- SECCIÓN: REDES SOCIALES --}}
                        <div class="row border border-3 rounded m-auto position-relative mt-4 p-2">
                            <div class="position-absolute" style="top:0px; left:40%; margin-top: -15px;">
                                <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                    <i class="fas fa-map-marker-alt me-1"></i> SOCIAL
                                </div>
                            </div>
                            {{-- Facebook --}}
                            <div class="form-group py-2 col-12 col-md-6 mt-2">
                                <label class="form-label">
                                    <i class="fab fa-facebook me-1"></i> URL DE FACEBOOK
                                </label>
                                <input type="url" class="form-control rounded" name="facebook" id="facebook"
                                    placeholder="https://facebook.com/tu_pagina">
                                <div id="_facebook">

                                </div>
                            </div>

                            {{-- YouTube --}}
                            <div class="form-group py-2 col-12 col-md-6 mt-2">
                                <label class="form-label">
                                    <i class="fab fa-youtube me-1"></i> URL DE YOUTUBE
                                </label>
                                <input type="url" class="form-control rounded" name="youtube" id="youtube"
                                    placeholder="https://youtube.com/tu_canal">
                                <div id="_youtube">

                                </div>

                            </div>
                            {{-- WhatsApp --}}
                            <div class="form-group py-2 col-12 col-md-4 mt-2">
                                <label class="form-label">
                                    <i class="fab fa-whatsapp me-1"></i> NÚMERO DE WHATSAPP
                                </label>
                                <input type="text" class="form-control rounded" name="whatsapp" id="whatsapp"
                                    placeholder="Ej:1234567">
                                <div id="_whatsapp">
                                </div>
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger rounded btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-1"></i> Cerrar
                            </button>
                            <button type="submit" class="btn btn-success rounded btn-sm" id="btn_guardar_sede">
                                <i class="ri-save-3-line me-1 align-middle"></i> Guardar
                            </button>
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

    {{-- modal para ver las imagenes de la sede --}}
    <div class="modal fade" id="modalGaleria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-images me-2"></i> Galería de Imágenes de la Sede
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_sede_actual">
                    <div class="mb-3">
                        <label for="nuevasImagenes" class="form-label">Agregar nuevas imágenes</label>
                        <input type="file" id="nuevasImagenes" name="nuevasImagenes[]" class="form-control" accept="image/*" multiple>
                        <button id="btnAgregarImagenes" class="btn btn-success btn-sm mt-2">
                            <i class="ri-upload-cloud-line me-1"></i> Subir Imágenes
                        </button>
                    </div>
                    <div id="galeriaContenedor" class="row g-2"></div>

                    <div class="mt-3">
                        <label class="form-label">
                            <i class="ri-image-line me-1"></i> Vista previa de imágenes seleccionadas
                        </label>
                        <div id="vistaPreviaGaleria" class="row g-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/sedes/sedes.js') }}" type="module"></script>
@endsection
