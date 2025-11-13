@extends('principal')
@section('titulo', 'TITULADOS')
@section('contenido')

    <div class="container mt-4">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user-graduate me-2"></i> Registro de Titulados
                </h5>
                <span class="badge bg-light text-primary fw-bold fs-5 text-uppercase"
                    id='fecha_filtrada'>{{ \Carbon\Carbon::parse($colacionSeleccionada)->translatedFormat('d \d\e F Y') }}</span>
            </div>

            <div class="card-body">
                <form action="" method="POST">

                    <div class="row g-3 align-items-end">
                        <!-- Filtro por año -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Año:</label>
                            <!-- Filtro por año -->
                            <select id="anio" name="anio" class="form-select shadow-sm">
                                @foreach ($anios as $anio)
                                    <option value="{{ $anio }}" {{ $anio == $anioSeleccionado ? 'selected' : '' }}>
                                        {{ $anio }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <!-- Filtro por colación -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Colación:</label>
                            <!-- Filtro por colación -->
                            <select id="fecha_filtro" name="fecha_filtro" class="form-select shadow-sm">
                                @foreach ($colaciones as $fecha)
                                    <option value="{{ $fecha }}"
                                        {{ $fecha == $colacionSeleccionada ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($fecha)->translatedFormat('d \d\e F Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="button" id="btnFiltrar">Filtrar</button>
                        </div>
                    </div>



                    <div class="table-responsive">
                        <table class="table table-bordered align-middle shadow-sm table-striped" id="tabla_titulados">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Carrera</th>
                                    <th>Sedes</th>
                                    <th>Genero</th>
                                    <th>Grado Academico</th>

                                    <th class="text-uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-4">
                        {{-- <button type="button" class="btn btn-success px-4 shadow-sm fw-bold" id="guardar_totales">
                            <i class="fas fa-save me-2"></i> Guardar Totales
                        </button> --}}

                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                            data-bs-target="#modalReporte">
                            <i class="fas fa-file-pdf me-2"></i> Generar Reporte
                        </button>

                        <button type="button" class="btn btn-info" data-bs-toggle="modal"
                            data-bs-target="#modalSubirDatos">
                            <i class="fas fa-file-pdf me-2"></i>Subir datos
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReporte" tabindex="-1" aria-labelledby="modalReporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formReporte">

                        <div class="mb-3">
                            <label for="tipoReporte" class="form-label fw-bold">Filtrar por:</label>
                            <select id="tipoReporte" class="form-select shadow-sm">
                                <option value="">-- Seleccione tipo --</option>
                                <option value="sede">Sede</option>
                                <option value="carrera">Carrera</option>
                            </select>
                        </div>

                        <div id="checkboxesContainer" class="d-none mt-3"></div>



                </div>
                <div class="modal-footer">
                    <button type="button" id="generarReporte" class="btn btn-success">
                        <i class="fas fa-file-pdf me-2"></i> Generar PDF
                    </button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalSubirDatos" tabindex="-1" aria-labelledby="modalReporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <i class=""></i>Importar Estudiantes desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form id="formSubirDatosExcel" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo" class="form-label">Seleccionar archivo Excel</label>
                                <input type="file" name="archivo" id="archivo" class="form-control form-lg"
                                    accept=".csv, text/csv" required>
                            </div>
                            <div class="alert alert-info mt-2 py-2 px-3">
                                <strong>El archivo CSV debe contener las siguientes columnas:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><code>nombre_completo</code></li>
                                    <li><code>documento</code></li>
                                    <li><code>carrera</code></li>
                                    <li><code>sede</code></li>
                                    <li><code>genero</code> → <span class="text-success">masculino</span> / <span
                                            class="text-primary">femenino</span></li>
                                    <li><code>Fecha</code></li>
                                    <li><code>grado_academico</code> → <span class="text-dark">licenciatura</span>, <span
                                            class="text-dark">técnico superior</span>, <span class="text-dark">técnico
                                            medio</span></li>

                                </ul>
                            </div>



                            <div id="alertContainer" class="alert d-none" role="alert"></div>
                            <div id="previewContainer" class=" d-none">

                                <h4 class="text-center fw-bolder mb-2 text-primary-emphasis text-uppercase">
                                    <i class="fas fa-eye me-2"></i> Vista Previa de Registros
                                </h4>


                                <div class="card card-preview-data shadow-lg">
                                    <div class="card-body p-4">

                                        {{-- Indicador de Fila --}}
                                        <div class="alert alert-info py-2 mb-3 text-center fw-semibold" role="alert">
                                            <i class="fas fa-info-circle me-2"></i> Mostrando los primeros 10 registros
                                            para
                                            validación.
                                        </div>

                                        {{-- Contenedor de la Tabla --}}
                                        <div class="table-responsive">
                                            <table class="table table-striped table-sm align-middle text-center mb-0"
                                                id="previewTable">
                                                <thead class="">
                                                    <tr id="previewHeaders" class="text-uppercase fw-bold"></tr>
                                                </thead>
                                                <tbody id="previewBody">
                                                    {{-- Los datos cargados aquí --}}
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Footer de Acción --}}
                                        <div class="text-center pt-4 border-top mt-4">
                                            <button type="button" id="btnConfirmar"
                                                class="btn btn-success btn-lg px-5 fw-bold btn-confirmar-custom shadow-lg">
                                                <i class="fas fa-cloud-upload-alt me-2"></i> Subir Definitivamente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-importar">Importar</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('scripts')

    <script>
        const sedes = @json($sedes);
        const carreras = @json($carreras);

        $('#tipoReporte').on('change', function() {
            const tipo = $(this).val();
            const $container = $('#checkboxesContainer');
            $container.empty();

            if (!tipo) {
                $container.addClass('d-none');
                return;
            }

            // Agregar "Listar todo"
            $container.append(`
            <div class="mb-2">
                <div class="form-check text-capitalize">
                    <input class="form-check-input filtroCheck" type="checkbox" id="checkTodo">
                    <label class="form-check-label fw-bold text-primary" for="checkTodo">Listar todo</label>
                </div>
            </div>
        `);

            // Contenedor de filas en dos columnas
            const $rowContainer = $('<div class="row"></div>');

            const data = tipo === 'sede' ? sedes : carreras;

            data.forEach((item, index) => {
                const col = $(`
                <div class="col-md-6 mb-2">
                    <div class="form-check text-capitalize">
                        <input class="form-check-input filtroCheck check-item" type="checkbox" value="${item.id}" id="${tipo}${item.id}">
                        <label class="form-check-label" for="${tipo}${item.id}">${item.nombre}</label>
                    </div>
                </div>
            `);
                $rowContainer.append(col);
            });

            $container.append($rowContainer).removeClass('d-none');

            // Funcionalidad: "Listar todo"
            $('#checkTodo').on('change', function() {
                $('.check-item').prop('checked', $(this).is(':checked'));
            });
        });
    </script>


    <script src="{{ asset('js/modulos/academico/titulados.js') }}" type="module"></script>

    <script>

        
        $("#anio").on("change", function() {
            const anioSeleccionado = $(this).val();

            $.ajax({
                url: "listarFechasColacion",
                method: "GET",
                data: {
                    anio: anioSeleccionado
                },
                success: function(response) {
                    if (response.tipo !== "exito") {
                        mensajeAlerta(response.mensaje, "errores");
                        return;
                    }

                    const fechas = response.mensaje;
                    $("#fecha_filtro").empty();

                    fechas.forEach((f) => {
                        // f.valor → YYYY-MM-DD (para el filtro)
                        // f.texto → "13 noviembre" (formateado por Carbon)
                        $("#fecha_filtro").append(
                            `<option value="${f.valor}">${f.texto}</option>`
                        );
                    });

                    // Actualizar badge
                    const primera = fechas.length > 0 ? fechas[0].texto : "N/A";
                    $("#fecha_filtrada").text(` ${primera}`);
                },
                error: function() {
                    console.error("Error al obtener las fechas de colación.");
                },
            });
        });
    </script>


@endsection
