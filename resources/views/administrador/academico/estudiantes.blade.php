@extends('principal')
@section('titulo', 'ESTUDIANTES')
@section('contenido')

    <div class="container mt-4">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user-graduate me-2"></i> Registro de Estudiantes por Carrera
                </h5>
                <span class="badge bg-light text-primary fw-bold">Gestión {{ date('Y') }}</span>
            </div>

            <div class="card-body">
                <form action="" method="POST">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="gestion" class="form-label fw-bold">Gestión:</label>
                            <input type="number" name="gestion" id="gestion" class="form-control shadow-sm"
                                value="{{ date('Y') }}" min="2000" max="2100">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formReporte">

                        <div class="mb-3">
                            <label for="tipoReporte" class="form-label">Listar por:</label>
                            <select id="tipoReporte" class="form-select">
                                <option value="">-- Seleccione --</option>
                                <option value="sede">Sede</option>
                                <option value="carrera">Carrera</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="checkboxesContainer">
                            <!-- Aquí se listarán los checkboxes dinámicamente -->
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="generarReporte" class="btn btn-success">
                        <i class="fas fa-file-pdf me-2"></i> Generar PDF
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/academico/estudiantes.js') }}" type="module"></script>
    <script>
        const sedes = @json($sedes); // array de sedes desde Laravel
        const carreras = @json($carreras); // array de carreras desde Laravel

        $('#tipoReporte').on('change', function() {
            const tipo = $(this).val();
            const $container = $('#checkboxesContainer');
            $container.empty(); // limpiar

            if (tipo === 'sede') {
                sedes.forEach(sede => {
                    $container.append(`
                <div class="form-check">
                    <input class="form-check-input filtroCheck" type="checkbox" value="${sede.id}" id="sede${sede.id}">
                    <label class="form-check-label" for="sede${sede.id}">${sede.nombre}</label>
                </div>
            `);
                });
                $container.removeClass('d-none');
            } else if (tipo === 'carrera') {
                carreras.forEach(carrera => {
                    $container.append(`
                <div class="form-check">
                    <input class="form-check-input filtroCheck" type="checkbox" value="${carrera.id}" id="carrera${carrera.id}">
                    <label class="form-check-label" for="carrera${carrera.id}">${carrera.nombre}</label>
                </div>
            `);
                });
                $container.removeClass('d-none');
            } else {
                $container.addClass('d-none');
            }
        });
    </script>
@endsection
