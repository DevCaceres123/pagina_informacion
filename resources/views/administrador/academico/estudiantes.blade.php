@extends('principal')
@section('titulo', 'ESTUDIANTES')
@section('contenido')

    <div class="container mt-4">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user-graduate me-2"></i> Registro de Estudiantes por Carrera
                </h5>
                <span class="badge bg-light text-primary fw-bold fs-6">Gestión {{ date('Y') }}</span>
            </div>

            <div class="card-body">
                <form action="" method="POST">

                    <div class="row mb-4">

                        <div class="col-md-3">
                            <label for="gestion_filtro" class="form-label fw-bold">Ver estadísticas de:</label>
                            <select name="gestion_filtro" id="gestion_filtro" class="form-select shadow-sm">
                                @php
                                    // Agregamos la gestión actual como primera opción
                                    $todasGestiones = collect([$gestionActual])->merge($gestiones);
                                @endphp

                                @foreach ($todasGestiones as $g)
                                    <option value="{{ $g }}" {{ $g == $gestionActual ? 'selected' : '' }}>
                                        {{ $g }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center shadow-sm table-striped"
                            id="tabla_estudiantes">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Carrera</th>
                                    <th>Sedes</th>
                                    <th>Hombres</th>
                                    <th>Mujeres</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
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

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalReporte">
                            <i class="fas fa-file-pdf me-2"></i> Generar Reporte
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
                <div class="form-check">
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
                    <div class="form-check">
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


    <script src="{{ asset('js/modulos/academico/estudiantes.js') }}" type="module"></script>

@endsection
