@extends('principal')
@section('titulo', 'INFRAESTRUCTURA SEDES')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-university  me-2"></i>  Modulo de Infraestructura
                            </h4>
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
                        <table class="table table-bordered table-md" id="tabla_listar_infraestructura">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>SEDE</th>
                                    <th>CREACION</th>
                                    <th>TIEMPO TRANSCURRIDO</th>
                                    <th>ESTADO INMUEBLE</th>
                                    <th>ESTADO TRAMITE</th>
                                    <th>PLANOS</th>
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
                                    <div id="_sede_id">

                                    </div>
                                </div>

                            </div>

                            {{-- SECCIÓN 2: DATOS DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-home me-1"></i> DATOS DEL INMUEBLE
                                    </div>
                                </div>


                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> PROPIEDAD <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <input type="text" class="form-control" id="propiedad" name="propiedad"
                                        placeholder="Propiedad" required>
                                    <div id="_propiedad">

                                    </div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> USO ASIGNADO <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <input type="text" class="form-control" id="uso_asignado" name="uso_asignado"
                                        placeholder="uso asignado" required>

                                    <div id="_uso_asignado">

                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-upload me-1"></i> PLANOS E UBICACION (IMAGEN) <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" class="form-control" name="planos[]" id="planos"
                                        accept="image/*" required multiple>

                                    <div id="_planos">

                                    </div>
                                </div>


                            </div>



                            {{-- SECCIÓN 2: ESTADO DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-home me-1"></i> ESTADO DEL INMUEBLE
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
                                        <option value="regular">Regular</option>
                                        <option value="malo">Malo</option>
                                    </select>
                                    <div id="_estado_inmueble"></div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> OBSERVACIÓN <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a comment here" id="observacion_estado" style="height: 100px"
                                            name="observacion_estado"></textarea>
                                        <label for="observacion_estado">Ingrese alguna observacion del inmueble</label>
                                    </div>

                                    <div id="_observacion_estado"></div>
                                </div>
                            </div>

                            {{-- SECCIÓN 3: SOLICITUD --}}

                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-file-contract me-1"></i> ESTADO TRAMITE <b> (INICIAL)</b>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">
                                        <i class="fas fa-upload me-1"></i> SOLICITUD (PDF) <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" class="form-control" name="solicitud" id="solicitud"
                                        accept=".pdf" required>
                                    <div id="_solicitud"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Cerrar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm" id="btnGuardarInfraestructura">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de cambio de estado -->
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-2 border-primary">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-retweet me-2"></i> Cambiar Estado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formCambioEstado">
                        <input type="hidden" id="idElementoEstado" name="id">

                        <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                            <div class="position-absolute" style="top:0px; left:25%; margin-top: -15px;">
                                <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                    <i class="fas fa-cogs me-1"></i>ESTADO DE TRAMITE
                                </div>
                            </div>
                            <div class="form-group py-2 col-md-12 mt-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i> Estado <strong class="text-danger">(*)</strong>
                                </label>
                                <select class="form-select" name="estado" id="estado_select" required>
                                    <option disabled>-- Seleccione --</option>
                                    <option value="inicial">Inicial</option>
                                    <option value="proceso">En Proceso</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i
                            class="fas fa-eraser me-1"></i>Cancelar</button>
                    <button class="btn btn-primary btn-sm" id="guardarEstadoBtn"><i
                            class="fas fa-save me-1"></i>Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para agregar datos de ubicación -->

    <div class="modal fade" id="datosUbicacionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalSedeLabel" aria-hidden="true">
        <div class="modal-dialog modal-center modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-light">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded">
                            <i class="fas fa-university me-1"></i> UBICACION DE LA INFRAESTRUCTURA
                        </span>
                    </h4>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formInfraestructuraUbicacion" enctype="multipart/form-data">
                        <div class="container">

                            {{-- 📍 SECCIÓN 1: DATOS DE LA UBICACIÓN --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-map-marker-alt me-1"></i> DATOS DE LA UBICACIÓN
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Escala</label>                                    
                                    <input type="text" class="form-control" id="escala" name="escala"
                                        placeholder="Ejemplo: 1:500, 1:1000">
                                    <div id="_escala">

                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label class="form-label">Distrito</label>
                                    <input type="hidden" id="infraestructura_id" name="infraestructura_id">
                                    <input type="text" class="form-control" id="distrito" name="distrito"
                                        placeholder="Ejemplo: 2,3,4">
                                    <div id="_distrito">

                                    </div>
                                </div>

                                <div class="col-md-8 mt-2">
                                    <label class="form-label">Direccion</label>
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                                        placeholder="Zona Juan pablo II, Av. Los Alamos, etc.">
                                    <div id="_ubicacion">

                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label class="form-label">Urbanización</label>
                                    <input type="text" class="form-control" id="urb" name="urb"
                                        placeholder="Ejem: Villa Club, etc.">
                                    <div id="_urb"></div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label class="form-label">Manzano</label>
                                    <input type="text" class="form-control" id="manzano" name="manzano"
                                        placeholder="Ejem: 1,2,3">
                                    <div id="_manzano"></div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <label class="form-label">Lote Nº</label>
                                    <input type="text" class="form-control" id="lote" name="lote"
                                        placeholder="Ejem: 1,2,3">
                                    <div id="_lote"></div>
                                </div>
                            </div>


                            {{-- 📐 SECCIÓN 2: MEDIDAS DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-ruler-combined me-1"></i> MEDIDAS DEL INMUEBLE
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="form-label">Sup. S/Test (m<sup>2</sup>)</label>
                                    <input type="text" class="form-control" id="sup_test" name="sup_test"
                                        placeholder="Ejemplo: 6245.10 m2">
                                    <div id="_sup_test"></div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="form-label">Sup. S/Lev (m<sup>2</sup>)</label>
                                    <input type="text" class="form-control" id="sup_lev" name="sup_lev"
                                       placeholder="Ejemplo: 6245.10 m2">
                                    <div id="_sup_lev"></div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="form-label">Sup. Adjunta (m<sup>2</sup>)</label>
                                    <input type="text" class="form-control" id="sup_adju" name="sup_adju"
                                       placeholder="Ejemplo: 6245.10 m2">
                                    <div id="_sup_adju"></div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="form-label">Sup. Útil (m<sup>2</sup>)</label>
                                    <input type="text" class="form-control" id="sup_util" name="sup_util"
                                       placeholder="Ejemplo: 6245.10 m2">
                                    <div id="_sup_util"></div>
                                </div>
                            </div>

                        </div>


                        <div class="modal-footer">
                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Cerrar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm"
                                    id="btnGuardarInfraestructuraubicacion">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="modalPlanos" tabindex="-1" aria-labelledby="imagenesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg rounded-4">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded">
                            <i class="fas fa-file-image  me-1"></i> ADMINISTRAR IMÁGENES DE PLANOS
                        </span>
                    </h4>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="modal-body">
                        {{-- Formulario de subida --}}
                        <form id="formSubirImagenesPlanos" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nuevasImagenes" class="form-label">Agregar nuevas imágenes</label>
                                <input type="hidden" id="id_infrastructura" name="id_infrastructura">
                                <input type="file" id="nuevasImagenes" name="nuevasImagenes[]" class="form-control"
                                    accept="image/*" multiple>

                                <div id="_nuevasImagenes"></div>
                                <button type="submit" id="btnAgregarImagenes" class="btn btn-success btn-sm mt-2">
                                    <i class="fas fa-upload me-1"></i> Subir Imágenes
                                </button>
                            </div>
                        </form>
                        <hr>
                        <label class="form-label">
                            <i class="fas fa-file-image  me-1"></i>Imágenes cargadas:::
                        </label>
                        {{-- Contenedor de imágenes existentes --}}
                        <div id="galeriaContenedor" class="row g-2">

                        </div>
                        <hr>
                        {{-- Vista previa de nuevas imágenes seleccionadas --}}
                        {{-- <div class="mt-3">
                            <label class="form-label">
                                <i class="fas fa-file-image  me-1"></i> Vista previa de imágenes seleccionadas::
                            </label>
                            <div id="vistaPreviaGaleria" class="row container d-flex"></div>
                        </div> --}}
                    </div>
                </div>

                
            </div>
        </div>
    </div>



      <!-- MODAL PARA EDITAR INFRAESTRUCTURA-->
    <div class="modal fade" id="modalInfraestructuraEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalSedeLabel" aria-hidden="true">
        <div class="modal-dialog modal-center modal-lg" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-black text-light">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded">
                            <i class="fas fa-university me-1"></i> EDITAR INFRAESTRUCTURA
                        </span>
                    </h4>
                    <span class="ms-3">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formInfraestructuraEdit" enctype="multipart/form-data">
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
                                    <input type="hidden" id="id_infraestructuraEdit" name="id">
                                    <select name="sede_idEdit" id="sede_idEdit" class="form-select text-capitalize" required>
                                        <option value="" disabled selected>Seleccione una sede</option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div id="_sede_idEdit">

                                    </div>
                                </div>

                            </div>

                            {{-- SECCIÓN 2: DATOS DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-home me-1"></i> DATOS DEL INMUEBLE
                                    </div>
                                </div>


                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> PROPIEDAD <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <input type="text" class="form-control" id="propiedadEdit" name="propiedadEdit"
                                        placeholder="Propiedad" required>
                                    <div id="_propiedadEdit">

                                    </div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> USO ASIGNADO <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <input type="text" class="form-control" id="uso_asignadoEdit" name="uso_asignadoEdit"
                                        placeholder="Uso asignado" required>

                                    <div id="_uso_asignadoEdit">

                                    </div>
                                </div>
                            </div>



                            {{-- SECCIÓN 2: ESTADO DEL INMUEBLE --}}
                            <div class="row border border-3 rounded m-auto position-relative mt-3 p-2">
                                <div class="position-absolute" style="top:0px; left:33%; margin-top: -15px;">
                                    <div class="d-inline p-1 border rounded border-danger bg-danger text-light">
                                        <i class="fas fa-home me-1"></i> ESTADO DEL INMUEBLE
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-warehouse me-1"></i> ESTADO DEL INMUEBLE <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <select name="estado_inmuebleEdit"  id="estado_inmuebleEdit" class="form-select" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="bueno">Bueno</option>
                                        <option value="regular">Regular</option>
                                        <option value="malo">Malo</option>
                                    </select>
                                    <div id="_estado_inmuebleEdit"></div>
                                </div>

                                <div class="col-md-12 mt-2 mb-2">
                                    <label class="form-label">
                                        <i class="fas fa-comment-alt me-1"></i> OBSERVACIÓN <strong
                                            class="text-danger">(*)</strong>
                                    </label>

                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a comment here" id="observacion_estadoEdit" style="height: 100px"
                                            name="observacion_estadoEdit"></textarea>
                                        <label for="observacion_estadoEdit">Ingrese alguna observacion del inmueble</label>
                                    </div>

                                    <div id="_observacion_estadoEdit"></div>
                                </div>
                            </div>                          
                        </div>

                        <div class="modal-footer">
                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Cerrar
                                </button>
                                <button type="submit" class="btn btn-success btn-sm" id="btnGuardarInfraestructuraEdit">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/infraestructura/infraestructura.js') }}" type="module"></script>
@endsection
