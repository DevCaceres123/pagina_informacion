@extends('principal')
@section('titulo', 'PERMISOS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                  <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                           <h4 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-user-shield  me-2"></i> Permisos
                            </h4>
                        </div>
                        <div class="col-auto">
                            @can('admin.permiso.crear')
                                <button class="btn btn-primary" onclick="abrirModalPermiso()">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_permiso">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>NOMBRE</th>
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_permiso" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="permisoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="permisoModalLabel">Permiso</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cerrarModalPermiso()"></button>
                </div>
                <div class="modal-body">
                    <form id="form_permiso" autocomplete="off" method="POST">
                        <input type="hidden" id="permiso_id" name="permiso_id">
                        <div class="mb-3 row">
                            <label for="permiso" class="col-sm-2 col-form-label">Permiso</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="permiso" name="permiso"
                                    placeholder="Ingrese el permiso">
                                <div id="_permiso"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        onclick="cerrarModalPermiso()">Cerrar</button>
                    <button type="button" id="btn_guardar_permiso" class="btn btn-dark btn-sm">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Definimos un array para almacenar los posibles mensajes de error relacionados con los permisos
        let errores_msj = ['_permiso'];

        // Inicializamos el modal de Bootstrap con el ID 'modal_permiso', que es la ventana emergente donde crearemos o editaremos permisos
        let modal_permiso = new bootstrap.Modal(document.getElementById('modal_permiso'));

        // Obtenemos el formulario y el botón de guardar dentro del modal
        let form_permiso = document.getElementById('form_permiso');
        let btn_guardar_permiso = document.getElementById('btn_guardar_permiso');

        // Función para abrir el modal. Si 'id' es null, se asume que es para crear un nuevo permiso, de lo contrario, se edita uno existente
        function abrirModalPermiso(id = null) {
            vaciar_errores(errores_msj);

            // Reseteamos los campos del formulario (vacía el formulario)
            form_permiso.reset();

            // Asignamos el ID del permiso en un campo oculto del formulario (si es null, será un nuevo permiso)
            document.getElementById('permiso_id').value = id;

            // Cambiamos el título del modal dependiendo de si es una edición o un nuevo permiso
            document.getElementById('permisoModalLabel').textContent = id ? 'Editar Permiso' : 'Nuevo Permiso';

            // Si estamos editando un permiso existente, cargamos los datos del permiso llamando a la función cargarDatosPermiso()
            if (id) {
                cargarDatosPermiso(id);
            }

            // Mostramos el modal
            modal_permiso.show();
        }

        // Función para cargar los datos del permiso seleccionado (cuando se edita)
        async function cargarDatosPermiso(id) {
            try {
                let response = await fetch(`{{ route('permisos.edit', ':id') }}`.replace(':id', id), {
                    method: "GET",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                let data = await response.json();
                if (data.tipo === 'success') {
                    document.getElementById('permiso').value = data.mensaje.name;
                } else {
                    alerta_top(data.tipo, data.mensaje);
                }
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }

        // Función para cerrar el modal y limpiar el formulario
        function cerrarModalPermiso() {
            form_permiso.reset();
            modal_permiso.hide();
            vaciar_errores(errores_msj);
        }

        // Listener para el botón de guardar permiso
        btn_guardar_permiso.addEventListener('click', async () => {
            // Convertimos los datos del formulario en un objeto clave-valor
            let datos = Object.fromEntries(new FormData(form_permiso).entries());

            // Definimos la URL y el método de la solicitud dependiendo de si estamos creando o editando un permiso
            let url = datos.permiso_id ? `{{ route('permisos.update', ':id') }}`.replace(':id', datos.permiso_id) : "{{ route('permisos.store') }}";
            let method = datos.permiso_id ? "PUT" : "POST";

            // Deshabilitamos el botón de guardar mientras se realiza la solicitud y mostramos un mensaje de "Guardando..."
            validar_boton(true, "Guardando...", btn_guardar_permiso);

            try {
                // Realizamos la solicitud (POST o PUT) con los datos del formulario
                let respuesta = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(datos)
                });

                let data = await respuesta.json();

                // Limpiamos los errores si hay
                vaciar_errores(errores_msj);


                if (data.tipo === 'errores') {
                    mostrarErrores(data.mensaje);
                } else if (data.tipo === 'error' || data.tipo === 'success') {
                    // Mostramos una alerta dependiendo del tipo de respuesta (éxito o error)
                    alerta_top(data.tipo, data.mensaje);

                    // Si el tipo es 'success', recargamos la lista de permisos y cerramos el modal
                    if (data.tipo === 'success') {
                        listar_permiso();
                        cerrarModalPermiso();
                    }
                }
            } catch (error) {
                console.log('Error: ' + error);
            } finally {
                // Rehabilitamos el botón de guardar una vez finalizada la solicitud
                validar_boton(false, 'Guardar', btn_guardar_permiso);
            }
        });

        // Función para listar los permisos existentes
        async function listar_permiso() {
            try {
                let response = await fetch("{{ route('permisos.listar') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });
                let data = await response.json();
                permiso_tabla(data);
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }

        // Función para renderizar la tabla de permisos utilizando DataTables
        function permiso_tabla(data) {
            const permissions = data.permissions; // 👈 los sacamos aquí

            $('#tabla_permiso').DataTable({
                responsive: true,
                data: data.listar_permiso,
                columns: [{
                        data: null,
                        className: 'table-td',
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name',
                        className: 'table-td'
                    },
                    {
                        data: null,
                        className: 'table-td',
                        render: function (data, type, row, meta) {
                            // row = { id, name, ... }
                            return `
                                ${permissions.editar ? `
                                    <button type="button" class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalPermiso('${row.id}')">
                                        <i class="las la-pen fs-18"></i>
                                    </button>
                                ` : ''}

                                ${permissions.eliminar ? `
                                    <button type="button" class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarPermiso('${row.id}')">
                                        <i class="las la-trash-alt fs-18"></i>
                                    </button>
                                ` : ''}
                            `;
                        }
                    },
                ],
                destroy: true
            });
        }

        // Función para eliminar un permiso
        function eliminarPermiso(id) {
            Swal.fire({
                title: "NOTA!",
                text: "¿Está seguro de eliminar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, Eliminar",
                cancelButtonText: "Cancelar",
            }).then(async function(result) {
                if (result.isConfirmed) {
                    try {
                        let response = await fetch(`{{ route('permisos.destroy', '') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        let data = await response.json();
                        alerta_top(data.tipo, data.mensaje);
                        if (data.tipo === 'success') {
                            listar_permiso();
                        }
                    } catch (error) {
                        console.error("Error al eliminar:", error);
                    }
                } else {
                    alerta_top('error', 'Se canceló la eliminación');
                }
            })
        }

        listar_permiso();
    </script>
@endsection
