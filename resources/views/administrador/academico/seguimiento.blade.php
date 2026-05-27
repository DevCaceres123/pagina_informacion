@extends('principal')
@section('titulo', 'Seguimiento de Estudiantes')

@section('contenido')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-info py-3">
                    <div class="row align-items-center mb-2">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-user-graduate me-2"></i> Seguimiento de Estudiantes
                            </h4>
                        </div>
                        <div class="col-auto d-flex gap-2">
                            @can('seguimiento_estudiantes.subir_datos')
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalCSV">
                                    <i class="fas fa-file-csv me-1"></i> Subir CSV
                                </button>
                            @endcan
                            @can('seguimiento_estudiantes.inicio')
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalReporteGeneral">
                                    <i class="fas fa-file-pdf me-1"></i> Reporte General
                                </button>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalReportePendientes">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Pendientes
                                </button>
                            @endcan
                            @can('seguimiento_estudiantes.crear')
                                <button class="btn btn-primary btn-sm" id="btnNuevo">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            @endcan
                        </div>
                    </div>
                    <ul class="nav nav-tabs" id="mainTabs" role="tablist" style="border-bottom:none">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-light fw-semibold small"
                                id="tab-listado-btn"
                                data-bs-toggle="tab"
                                data-bs-target="#tabListado"
                                type="button" role="tab">
                                <i class="fas fa-list me-1"></i> Listado de Estudiantes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-light fw-semibold small"
                                id="tab-aprobados-btn"
                                data-bs-toggle="tab"
                                data-bs-target="#tabChartAprobados"
                                type="button" role="tab">
                                <i class="fas fa-check-circle me-1 text-success"></i> Aprobados para Titularse
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-light fw-semibold small"
                                id="tab-tendencia-btn"
                                data-bs-toggle="tab"
                                data-bs-target="#tabChartTendencia"
                                type="button" role="tab">
                                <i class="fas fa-chart-line me-1 text-info"></i> Tendencia por Gestión
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="mainTabContent">

                        {{-- Tab Listado --}}
                        <div class="tab-pane fade show active" id="tabListado" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped table-md" id="tablaEstudiantes">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px">#</th>
                                            <th>NOMBRE COMPLETO</th>
                                            <th>MATRÍCULA</th>
                                            <th style="width:90px">TIPO DOC.</th>
                                            <th>N° DOCUMENTO</th>
                                            <th>SEDE</th>
                                            <th>CARRERA</th>
                                            <th style="width:80px">GESTIÓN</th>
                                            <th style="width:130px" class="text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab Aprobados --}}
                        <div class="tab-pane fade" id="tabChartAprobados" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-center" style="min-height:320px">
                                <div id="spinnerAprobados" class="text-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-1"></div> Cargando...
                                </div>
                                <canvas id="chartAprobados" style="display:none; max-height:320px"></canvas>
                            </div>
                        </div>

                        {{-- Tab Tendencia --}}
                        <div class="tab-pane fade" id="tabChartTendencia" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-center" style="min-height:320px">
                                <div id="spinnerTendencia" class="text-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-1"></div> Cargando...
                                </div>
                                <canvas id="chartTendencia" style="display:none; max-height:320px; width:100%"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL NUEVO / EDITAR ===================== --}}
    <div class="modal fade" id="modalEstudiante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title" id="tituloModalEstudiante">Nuevo Estudiante</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEstudiante">
                        <input type="hidden" id="est_id">

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" id="nombre_completo" class="form-control text-capitalize" placeholder="Apellido Paterno Apellido Materno Nombres">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Matrícula <span class="text-danger">*</span></label>
                                <input type="text" id="matricula" class="form-control" placeholder="Ej: 20231234">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Género <span class="text-danger">*</span></label>
                                <select id="genero" class="form-select">
                                    <option value="">-- Seleccione --</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                <select id="tipo_documento" class="form-select">
                                    <option value="">-- Seleccione --</option>
                                    <option value="CI">Carnet de Identidad</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">N° Documento <span class="text-danger">*</span></label>
                                <input type="text" id="numero_documento" class="form-control" placeholder="Ej: 1234567">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Gestión <span class="text-danger">*</span></label>
                                <input type="number" id="gestion" class="form-control" placeholder="{{ date('Y') }}" min="2000" max="2100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sede <span class="text-danger">*</span></label>
                                <select id="sede_id" class="form-select">
                                    <option value="">-- Seleccione Sede --</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ strtoupper($sede->nombre) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Carrera <span class="text-danger">*</span></label>
                                <select id="carrera_id" class="form-select">
                                    <option value="">-- Seleccione Carrera --</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id }}">{{ strtoupper($carrera->nombre) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Observación</label>
                                <textarea id="observacion" class="form-control" rows="3" placeholder="Notas u observaciones adicionales..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarEstudiante">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL DOCUMENTOS ===================== --}}
    <div class="modal fade" id="modalDocumentos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <div>
                        <h5 class="modal-title mb-0"><i class="fas fa-folder-open me-2"></i>Documentos del Estudiante</h5>
                        <small id="docEstudianteInfo" class="text-info"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="doc_estudiante_id">

                    <ul class="nav nav-tabs mb-3" id="docTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabDocPrincipales">
                                <i class="fas fa-file-alt me-1"></i> Documentos
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFormularios">
                                <i class="fas fa-file-signature me-1"></i> Formularios de Inscripción
                            </button>       
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabRequisitos">
                                <i class="fas fa-clipboard-check me-1"></i> Expediente de Titulación
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Tab Documentos principales --}}
                        <div class="tab-pane fade show active" id="tabDocPrincipales">
                            <div class="border rounded overflow-hidden">

                                @php
                                $grupos = [
                                    [
                                        'titulo' => null,
                                        'color'  => 'success',
                                        'docs'   => [
                                            ['campo' => 'certificado_habilitacion', 'label' => 'Certificado de Habilitación'],
                                        ],
                                    ],
                                    [
                                        'titulo' => 'Título Técnico Medio',
                                        'color'  => 'primary',
                                        'docs'   => [
                                            ['campo' => 'copia_titulo_tec_medio', 'label' => 'Copia del Título'],
                                        ],
                                    ],
                                    [
                                        'titulo' => 'Título Técnico Superior',
                                        'color'  => 'info',
                                        'docs'   => [
                                            ['campo' => 'copia_titulo_tec_superior', 'label' => 'Copia del Título'],
                                        ],
                                    ],
                                    [
                                        'titulo' => 'Título Licenciatura',
                                        'color'  => 'warning',
                                        'docs'   => [
                                            ['campo' => 'copia_titulo_licenciatura', 'label' => 'Copia del Título'],
                                        ],
                                    ],
                                ];
                                @endphp

                                @foreach($grupos as $grupo)
                                    @if($grupo['titulo'])
                                    <div class="px-3 py-1 bg-light border-bottom">
                                        <small class="fw-semibold text-uppercase text-muted">
                                            <i class="fas fa-graduation-cap me-1 text-{{ $grupo['color'] }}"></i>
                                            {{ $grupo['titulo'] }}
                                        </small>
                                    </div>
                                    @endif

                                    @foreach($grupo['docs'] as $doc)
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                                        <div id="estado_{{ $doc['campo'] }}" class="flex-shrink-0" style="min-width:90px">
                                            <span class="badge bg-secondary">Sin archivo</span>
                                        </div>
                                        <div class="flex-grow-1 small fw-semibold text-dark">
                                            {{ $doc['label'] }}
                                        </div>
                                        <a id="link_{{ $doc['campo'] }}" href="#" target="_blank"
                                           class="btn btn-sm btn-outline-{{ $grupo['color'] }} flex-shrink-0 d-none">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <input type="file" class="form-control form-control-sm doc-input flex-shrink-0"
                                            data-tipo="{{ $doc['campo'] }}" accept=".pdf"
                                            style="max-width:190px" title="Solo PDF · máx 5 MB">
                                        <button class="btn btn-sm btn-{{ $grupo['color'] }} flex-shrink-0 btn-subir-doc"
                                            data-tipo="{{ $doc['campo'] }}">
                                            <i class="fas fa-upload me-1"></i> Subir
                                        </button>
                                    </div>
                                    @endforeach
                                @endforeach

                            </div>
                        </div>

                        {{-- Tab Formularios de inscripción --}}
                        <div class="tab-pane fade" id="tabFormularios">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card border">
                                        <div class="card-header fw-semibold bg-light">
                                            <i class="fas fa-plus-circle me-1 text-primary"></i> Agregar Formulario
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <label class="form-label">Fecha de Recepción <span class="text-danger">*</span></label>
                                                <input type="date" id="form_fecha" class="form-control form-control-sm">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Archivo <span class="text-danger">*</span></label>
                                                <small class="text-muted">Solo PDF · máx 5 MB</small>
                                                <input type="file" id="form_archivo" class="form-control form-control-sm"
                                                    accept=".pdf">
                                            </div>
                                            <button class="btn btn-primary btn-sm w-100" id="btnAgregarFormulario">
                                                <i class="fas fa-upload me-1"></i> Subir Formulario
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="tablaFormularios">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha Recepción</th>
                                                    <th class="text-center">Ver</th>
                                                    <th class="text-center">Eliminar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyFormularios">
                                                <tr><td colspan="4" class="text-center text-muted">Sin formularios.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab Expediente de Titulación --}}
                        <div class="tab-pane fade" id="tabRequisitos">
                            @php
                            $tiposTitulo = [
                                'tec_medio'    => ['label' => 'Téc. Medio',    'labelLargo' => 'Título Técnico Medio',    'color' => 'primary'],
                                'tec_superior' => ['label' => 'Téc. Superior', 'labelLargo' => 'Título Técnico Superior', 'color' => 'info'],
                                'licenciatura' => ['label' => 'Licenciatura',  'labelLargo' => 'Título Licenciatura',     'color' => 'warning'],
                            ];
                            @endphp

                            {{-- Sub-pestañas --}}
                            <ul class="nav nav-tabs nav-fill mb-0" id="expTabs" role="tablist">
                                @foreach($tiposTitulo as $tipo => $cfg)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }} fw-semibold"
                                        data-bs-toggle="tab"
                                        data-bs-target="#expTab_{{ $tipo }}"
                                        type="button">
                                        <i class="fas fa-graduation-cap me-1 text-{{ $cfg['color'] }}"></i>
                                        {{ $cfg['label'] }}
                                    </button>
                                </li>
                                @endforeach
                            </ul>

                            <div class="tab-content border border-top-0 rounded-bottom">
                                @foreach($tiposTitulo as $tipo => $cfg)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="expTab_{{ $tipo }}">

                                    {{-- Estado de aprobación --}}
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light border-bottom">
                                        <span class="small text-muted fw-semibold">{{ $cfg['labelLargo'] }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span id="badge_aprobado_{{ $tipo }}" class="badge bg-secondary">Pendiente</span>
                                            <button class="btn btn-sm btn-outline-success btn-aprobar-expediente"
                                                data-tipo="{{ $tipo }}" id="btn_aprobar_{{ $tipo }}">
                                                <i class="fas fa-check-circle me-1"></i> Marcar Aprobado
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Formulario de carga --}}
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom bg-white flex-wrap">
                                        <input type="text" class="form-control form-control-sm req-nombre"
                                            data-tipo="{{ $tipo }}" placeholder="Nombre del requisito"
                                            style="min-width:160px; max-width:220px">
                                        <input type="file" class="form-control form-control-sm req-archivo"
                                            data-tipo="{{ $tipo }}" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                            title="Imagen o PDF · máx 5 MB" style="max-width:220px">
                                        <button class="btn btn-sm btn-{{ $cfg['color'] }} btn-agregar-requisito flex-shrink-0"
                                            data-tipo="{{ $tipo }}">
                                            <i class="fas fa-upload me-1"></i> Subir
                                        </button>
                                    </div>

                                    {{-- Lista de requisitos --}}
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <tbody id="bodyRequisitos_{{ $tipo }}">
                                                <tr><td colspan="4" class="text-center text-muted py-3">Sin requisitos registrados.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>{{-- /tab-content --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL REPORTE GENERAL ===================== --}}
    <div class="modal fade" id="modalReporteGeneral" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title"><i class="fas fa-file-pdf me-2"></i>Reporte General de Estudiantes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Todos los filtros son opcionales. Al seleccionar sedes, las carreras disponibles se actualizan automáticamente.
                    </p>
                    <div class="row g-3">
                        {{-- SEDES --}}
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Sede(s)</label>
                            <div class="d-flex gap-2 align-items-center mb-1">
                                <input type="text" id="buscarSede" class="form-control form-control-sm"
                                    placeholder="Buscar sede...">
                                <div class="form-check mb-0 text-nowrap">
                                    <input class="form-check-input" type="checkbox" id="chkTodasSedes">
                                    <label class="form-check-label small fw-semibold" for="chkTodasSedes">Todas</label>
                                </div>
                            </div>
                            <div class="border rounded p-2" style="max-height:160px; overflow-y:auto;">
                                @foreach($sedes as $sede)
                                <div class="form-check chk-sede-wrap">
                                    <input class="form-check-input chk-sede" type="checkbox"
                                           id="chk_sede_{{ $sede->id }}" value="{{ $sede->id }}">
                                    <label class="form-check-label small" for="chk_sede_{{ $sede->id }}">
                                        {{ strtoupper($sede->nombre) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- CARRERAS --}}
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Carrera(s)</label>
                            <div class="d-flex gap-2 align-items-center mb-1">
                                <input type="text" id="buscarCarrera" class="form-control form-control-sm"
                                    placeholder="Buscar carrera...">
                                <div class="form-check mb-0 text-nowrap">
                                    <input class="form-check-input" type="checkbox" id="chkTodasCarreras">
                                    <label class="form-check-label small fw-semibold" for="chkTodasCarreras">Todas</label>
                                </div>
                            </div>
                            <div class="border rounded p-2" style="max-height:160px; overflow-y:auto;">
                                @foreach($carreras as $carrera)
                                <div class="form-check chk-carrera-wrap">
                                    <input class="form-check-input chk-carrera" type="checkbox"
                                           id="chk_carrera_{{ $carrera->id }}" value="{{ $carrera->id }}">
                                    <label class="form-check-label small" for="chk_carrera_{{ $carrera->id }}">
                                        {{ strtoupper($carrera->nombre) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- GÉNERO Y RANGO DE AÑOS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Género</label>
                            <select id="rep_genero" class="form-select form-select-sm">
                                <option value="">— Todos —</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gestión desde</label>
                            <input type="number" id="rep_gestion_desde" class="form-control form-control-sm"
                                placeholder="Ej: 2020" min="2000" max="2100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gestión hasta</label>
                            <input type="number" id="rep_gestion_hasta" class="form-control form-control-sm"
                                placeholder="Ej: 2024" min="2000" max="2100">
                        </div>

                        {{-- TIPO DE VISTA --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <label class="form-label fw-semibold">Tipo de Vista</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rep_vista"
                                        id="rep_vista_listado" value="listado" checked>
                                    <label class="form-check-label small" for="rep_vista_listado">
                                        <i class="fas fa-list me-1"></i> Listado detallado
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rep_vista"
                                        id="rep_vista_totales" value="totales">
                                    <label class="form-check-label small" for="rep_vista_totales">
                                        <i class="fas fa-chart-bar me-1"></i> Solo totales
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5" id="seccionAgruparPor" style="display:none">
                            <label class="form-label fw-semibold">Agrupar por</label>
                            <select id="rep_agrupar_por" class="form-select form-select-sm">
                                <option value="sede">Por Sede</option>
                                <option value="carrera">Por Carrera</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnGenerarReporteGeneral">
                        <i class="fas fa-file-pdf me-1"></i> Generar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL REPORTE PENDIENTES ===================== --}}
    <div class="modal fade" id="modalReportePendientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title" id="titleReportePendientes">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Reporte de Documentos Pendientes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="descReportePendientes">
                        Muestra estudiantes que tienen al menos un documento sin subir. Todos los filtros son opcionales.
                    </p>

                    {{-- TIPO DE REPORTE --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">¿Qué desea ver?</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="pend_tipo_reporte" id="pend_tipo_pendientes" value="pendientes" checked>
                            <label class="btn btn-outline-warning btn-sm" for="pend_tipo_pendientes">
                                <i class="fas fa-exclamation-triangle me-1"></i> Documentos Pendientes
                            </label>
                            <input type="radio" class="btn-check" name="pend_tipo_reporte" id="pend_tipo_aprobados" value="aprobados">
                            <label class="btn btn-outline-success btn-sm" for="pend_tipo_aprobados">
                                <i class="fas fa-check-circle me-1"></i> Aprobados para Titularse
                            </label>
                        </div>
                    </div>
                    <hr class="mt-0 mb-3">

                    <div class="row g-3">
                        {{-- SEDES --}}
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Sede(s)</label>
                            <div class="d-flex gap-2 align-items-center mb-1">
                                <input type="text" id="buscarSedePend" class="form-control form-control-sm"
                                    placeholder="Buscar sede...">
                                <div class="form-check mb-0 text-nowrap">
                                    <input class="form-check-input" type="checkbox" id="chkTodasSedesPend">
                                    <label class="form-check-label small fw-semibold" for="chkTodasSedesPend">Todas</label>
                                </div>
                            </div>
                            <div class="border rounded p-2" style="max-height:160px; overflow-y:auto;">
                                @foreach($sedes as $sede)
                                <div class="form-check chk-sede-wrap-pend">
                                    <input class="form-check-input chk-sede-pend" type="checkbox"
                                           id="pchk_sede_{{ $sede->id }}" value="{{ $sede->id }}">
                                    <label class="form-check-label small" for="pchk_sede_{{ $sede->id }}">
                                        {{ strtoupper($sede->nombre) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- CARRERAS --}}
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Carrera(s)</label>
                            <div class="d-flex gap-2 align-items-center mb-1">
                                <input type="text" id="buscarCarreraPend" class="form-control form-control-sm"
                                    placeholder="Buscar carrera...">
                                <div class="form-check mb-0 text-nowrap">
                                    <input class="form-check-input" type="checkbox" id="chkTodasCarrerasPend">
                                    <label class="form-check-label small fw-semibold" for="chkTodasCarrerasPend">Todas</label>
                                </div>
                            </div>
                            <div class="border rounded p-2" style="max-height:160px; overflow-y:auto;">
                                @foreach($carreras as $carrera)
                                <div class="form-check chk-carrera-wrap-pend">
                                    <input class="form-check-input chk-carrera-pend" type="checkbox"
                                           id="pchk_carrera_{{ $carrera->id }}" value="{{ $carrera->id }}">
                                    <label class="form-check-label small" for="pchk_carrera_{{ $carrera->id }}">
                                        {{ strtoupper($carrera->nombre) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- MODALIDAD --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <label class="form-label fw-semibold">Modalidad(es) de Titulación</label>
                            <div class="d-flex gap-3 flex-wrap align-items-center border rounded p-2 bg-light">
                                <div class="form-check mb-0">
                                    <input class="form-check-input chk-modalidad-pend" type="checkbox"
                                           id="pend_mod_tm" value="tec_medio" checked>
                                    <label class="form-check-label small" for="pend_mod_tm">Técnico Medio</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input chk-modalidad-pend" type="checkbox"
                                           id="pend_mod_ts" value="tec_superior" checked>
                                    <label class="form-check-label small" for="pend_mod_ts">Técnico Superior</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input chk-modalidad-pend" type="checkbox"
                                           id="pend_mod_lic" value="licenciatura" checked>
                                    <label class="form-check-label small" for="pend_mod_lic">Licenciatura</label>
                                </div>
                                <div class="form-check mb-0 ms-auto border-start ps-3">
                                    <input class="form-check-input" type="checkbox" id="chkTodasModPend" checked>
                                    <label class="form-check-label small fw-semibold" for="chkTodasModPend">Todas</label>
                                </div>
                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mostrar</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="pend_estado"
                                       id="pend_estado_no_cumplen" value="no_cumplen" checked>
                                <label class="btn btn-outline-danger btn-sm" for="pend_estado_no_cumplen">
                                    <i class="fas fa-times-circle me-1"></i><span id="lbl_no_cumplen">Con pendientes</span>
                                </label>
                                <input type="radio" class="btn-check" name="pend_estado"
                                       id="pend_estado_cumplen" value="cumplen">
                                <label class="btn btn-outline-success btn-sm" for="pend_estado_cumplen">
                                    <i class="fas fa-check-circle me-1"></i><span id="lbl_cumplen">Completos</span>
                                </label>
                                <input type="radio" class="btn-check" name="pend_estado"
                                       id="pend_estado_ambos" value="ambos">
                                <label class="btn btn-outline-secondary btn-sm" for="pend_estado_ambos">
                                    <i class="fas fa-users me-1"></i> Todos
                                </label>
                            </div>
                        </div>

                        {{-- GÉNERO Y RANGO DE AÑOS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Género</label>
                            <select id="pend_genero" class="form-select form-select-sm">
                                <option value="">— Todos —</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gestión desde</label>
                            <input type="number" id="pend_gestion_desde" class="form-control form-control-sm"
                                placeholder="Ej: 2020" min="2000" max="2100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gestión hasta</label>
                            <input type="number" id="pend_gestion_hasta" class="form-control form-control-sm"
                                placeholder="Ej: 2024" min="2000" max="2100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnGenerarReportePendientes">
                        <i class="fas fa-file-pdf me-1"></i> <span id="btnPendientesLabel">Generar PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL CSV ===================== --}}
    <div class="modal fade" id="modalCSV" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title"><i class="fas fa-file-csv me-2"></i>Importar Estudiantes CSV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCSV" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar archivo CSV</label>
                            <input type="file" name="archivo" id="csv_archivo" class="form-control" accept=".csv">
                        </div>
                        <div class="alert alert-info py-2">
                            <strong>Columnas requeridas:</strong>
                            <code>nombre_completo</code> | <code>matricula</code> | <code>tipo_documento</code> |
                            <code>numero_documento</code> | <code>sede</code> | <code>carrera</code> | <code>gestion</code> | <code>genero</code>
                        </div>
                        <div id="csv_alert" class="alert d-none"></div>

                        <div id="csv_preview" class="d-none">
                            <h6 class="text-center fw-bold text-primary mb-2">
                                <i class="fas fa-eye me-1"></i> Vista Previa (primeros 10 registros)
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped" id="csv_tabla">
                                    <thead><tr id="csv_headers" class="table-dark"></tr></thead>
                                    <tbody id="csv_body"></tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <button type="button" id="btnConfirmarCSV" class="btn btn-success px-5">
                                    <i class="fas fa-cloud-upload-alt me-1"></i> Subir Definitivamente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="formCSV" class="btn btn-primary" id="btnPrevisualizarCSV">
                        <i class="fas fa-search me-1"></i> Previsualizar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        const SEGUIMIENTO_URL    = "{{ url('admin/seguimiento') }}";
        const CARRERA_SEDE       = @json($relacionesSedeCarre);
    </script>
    <script src="{{ asset('js/modulos/academico/seguimientoEstudiantes.js') }}" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const STATS_URL = "{{ route('seguimiento.estadisticas') }}";

        let chartAprobados = null;
        let chartTendencia = null;

        function showSpinner(spinnerId, canvasId) {
            document.getElementById(spinnerId).style.display = 'block';
            document.getElementById(canvasId).style.display  = 'none';
        }
        function hideSpinner(spinnerId, canvasId) {
            document.getElementById(spinnerId).style.display = 'none';
            document.getElementById(canvasId).style.display  = 'block';
        }

        function loadEstadisticas() {
            showSpinner('spinnerAprobados', 'chartAprobados');
            showSpinner('spinnerTendencia', 'chartTendencia');

            fetch(STATS_URL)
                .then(r => r.json())
                .then(data => {
                    renderAprobados(data.aprobaciones);
                    renderTendencia(data.tendencia);
                })
                .catch(() => {
                    document.getElementById('spinnerAprobados').innerHTML = '<span class="text-danger small">Error al cargar</span>';
                    document.getElementById('spinnerTendencia').innerHTML = '<span class="text-danger small">Error al cargar</span>';
                });
        }

        function renderAprobados(d) {
            hideSpinner('spinnerAprobados', 'chartAprobados');
            const ctx = document.getElementById('chartAprobados').getContext('2d');
            if (chartAprobados) chartAprobados.destroy();

            chartAprobados = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Téc. Medio', 'Téc. Superior', 'Licenciatura'],
                    datasets: [
                        {
                            label: 'Aprobados',
                            data: [d.aprobado_tm, d.aprobado_ts, d.aprobado_lic],
                            backgroundColor: 'rgba(21, 87, 36, 0.82)',
                            borderColor:     'rgba(21, 87, 36, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'No Aprobados',
                            data: [d.no_aprobado_tm, d.no_aprobado_ts, d.no_aprobado_lic],
                            backgroundColor: 'rgba(133, 26, 26, 0.72)',
                            borderColor:     'rgba(133, 26, 26, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                        x: { ticks: { font: { size: 11 } } },
                    },
                },
            });
        }

        function renderTendencia(rows) {
            hideSpinner('spinnerTendencia', 'chartTendencia');
            const ctx = document.getElementById('chartTendencia').getContext('2d');
            if (chartTendencia) chartTendencia.destroy();

            const labels    = rows.map(r => r.gestion);
            const total     = rows.map(r => r.total);
            const masculino = rows.map(r => r.masculino);
            const femenino  = rows.map(r => r.femenino);

            chartTendencia = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Total',
                            data: total,
                            borderColor:     'rgba(44, 62, 80, 0.9)',
                            backgroundColor: 'rgba(44, 62, 80, 0.08)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Masculino',
                            data: masculino,
                            borderColor:     'rgba(13, 110, 253, 0.85)',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            borderDash: [4, 3],
                        },
                        {
                            label: 'Femenino',
                            data: femenino,
                            borderColor:     'rgba(214, 51, 132, 0.85)',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            borderDash: [4, 3],
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 } } },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                        x: { ticks: { font: { size: 11 } } },
                    },
                },
            });
        }

        // Resize chart when its tab becomes visible (Chart.js can't measure hidden containers)
        document.getElementById('tab-aprobados-btn').addEventListener('shown.bs.tab', () => {
            if (chartAprobados) chartAprobados.resize();
            else loadEstadisticas();
        });
        document.getElementById('tab-tendencia-btn').addEventListener('shown.bs.tab', () => {
            if (chartTendencia) chartTendencia.resize();
            else loadEstadisticas();
        });

        document.addEventListener('DOMContentLoaded', loadEstadisticas);
    })();
    </script>
@endsection
