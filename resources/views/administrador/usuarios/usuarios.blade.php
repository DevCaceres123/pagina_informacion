@extends('principal')
@section('titulo', 'USUARIOS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                               <i class="fas fa-users me-2"></i> Usuarios
                            </h4>
                        </div>
                        <div class="col-auto">
                            @can('admin.usuario.crear')
                            <button class="btn btn-primary" onclick="abrirModalUsuario()">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_usuarios">
                            <thead class="table-light">
                                <tr>
                                    <th>ACCIÓN</th>
                                    <th>Nº</th>
                                    <th>CI</th>
                                    <th>NOMBRES</th>
                                    <th>ESTADO</th>
                                    <th>ROL</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_usuario" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="usuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="usuarioModalLabel">Usuario</h6>
                    <span class="ms-3 text-light">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cerrarModalUsuario()"></button>
                </div>
                <div class="modal-body">
                    <form id="form_usuario" autocomplete="off" method="POST">
                        <input type="hidden" id="usuario_id" name="usuario_id">
                        <div class="mb-3 row">
                            <label for="ci" class="col-sm-2 col-form-label">CI <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control " id="ci" name="ci"
                                    placeholder="Ingrese ci">
                                <div id="_ci"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="ci" class="col-sm-2 col-form-label">Nombres <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uppercase-input"  id="nombres" name="nombres"
                                    placeholder="Ingrese nombres">
                                <div id="_nombres"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="apellidos" class="col-sm-2 col-form-label">Apellidos <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uppercase-input" id="apellidos" name="apellidos"
                                    placeholder="Ingrese los apellidos">
                                <div id="_apellidos"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="email" class="col-sm-2 col-form-label">Email <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control lowercase-input" id="email" name="email"
                                    placeholder="Ingrese el email">
                                <div id="_email"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="role" class="col-sm-2 col-form-label">Selec. Rol <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <select id="roles" name="roles" class="form-control text-uppercase">
                                    <option selected disabled>[ ROLES ]</option>
                                    @foreach ($roles as $lis)
                                        <option class="text-uppercase" value="{{ $lis->id }}">{{ $lis->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="_roles"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="usuario" class="col-sm-2 col-form-label">Usuario <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="usuario" name="usuario"
                                    placeholder="Ingrese el usuario">
                                <div id="_usuario"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="password" class="col-sm-2 col-form-label">Contraseña <strong
                                    class="text-danger">(*)</strong></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="password" name="password"
                                    placeholder="Ingrese la contraseña">
                                <div id="_password"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        onclick="cerrarModalUsuario()">Cerrar</button>
                    <button type="button" id="btn_guardar_usuario" class="btn btn-dark btn-sm">Guardar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let errores_msj = ['_ci', '_nombres', '_apellidos', '_email', '_roles', '_usuario', '_password'];

        let modal_usuario = new bootstrap.Modal(document.getElementById('modal_usuario'));

        let form_usuario = document.getElementById('form_usuario');
        let btn_guardar_usuario = document.getElementById('btn_guardar_usuario');

        function abrirModalUsuario(id = null) {
            vaciar_errores(errores_msj);
            form_usuario.reset();
            document.getElementById('usuario_id').value = id;
            document.getElementById('usuarioModalLabel').textContent = id ? 'Editar Usuario' : 'Nuevo Usuario';
            if (id) {
                cargarDatosUsuario(id);
            }
            modal_usuario.show();
        }


        // Función para cargar los datos del usuario seleccionado (cuando se edita)
        async function cargarDatosUsuario(id) {
            try {
                let response = await fetch(`{{ route('user.edit', ':id') }}`.replace(':id', id), {
                    method: "GET",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                let data = await response.json();
                if (data.tipo === 'success') {
                    document.getElementById('ci').value    = data.mensaje.ci;
                    document.getElementById('nombres').value    = data.mensaje.nombres;
                    document.getElementById('apellidos').value  = data.mensaje.apellidos;
                    document.getElementById('email').value      = data.mensaje.email;
                    document.getElementById('roles').value      = data.mensaje.roles[0].id;
                    document.getElementById('usuario').value      = data.mensaje.usuario;

                } else {
                    alerta_top(data.tipo, data.mensaje);
                }
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }

        // Función para cerrar el modal y limpiar el formulario
        function cerrarModalUsuario() {
            form_usuario.reset();
            modal_usuario.hide();
            vaciar_errores(errores_msj);
        }

        //para guardar el usuario
        btn_guardar_usuario.addEventListener('click', async () => {
            let datos = Object.fromEntries(new FormData(form_usuario).entries());
            let url = datos.usuario_id ? `{{ route('user.update', ':id') }}`.replace(':id', datos.usuario_id) : "{{ route('user.store') }}";
            let metodo = datos.usuario_id ? "PUT" : "POST";
            validar_boton(true, "Validando . . . . ", btn_guardar_usuario);

            try {
                let respuesta = await fetch(url, {
                    method: metodo,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });
                let data = await respuesta.json();
                //console.log(data);

                // Limpiamos los errores si hay
                vaciar_errores(errores_msj);
                if (data.tipo === 'errores') {
                    mostrarErrores(data.mensaje);
                }
                if (data.tipo === 'success') {
                    alerta_top(data.tipo, data.mensaje);
                    listar_usuario();
                    setTimeout(() => {
                        cerrarModalUsuario();
                    }, 1000);
                }
                if (data.tipo === 'error') {
                    // Mostramos una alerta dependiendo del tipo de respuesta (éxito o error)
                    alerta_top(data.tipo, data.mensaje);
                }
                validar_boton(false, "Guardar", btn_guardar_usuario);
            } catch (error) {
                console.log('Error: ' + error);
            } finally {
                // Rehabilitamos el botón de guardar una vez finalizada la solicitud
                validar_boton(false, 'Guardar', btn_guardar_usuario);
            }

        });

        async function listar_usuario() {
            let respuesta = await fetch("{{ route('user.listar') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });
            let dato = await respuesta.json();
            let i = 1;
            $('#tabla_listar_usuarios').DataTable({
                responsive: true,
                data: dato.usuario,
                columns: [{
                        data: null,
                        className: 'table-td',
                         render: (data, type, row) => `
                            ${dato.permissions['editar'] ?
                                `<button type="button" class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalUsuario('${row.id}')">
                                            <i class="las la-pen fs-18"></i>
                                        </button>` 
                             : ``}

                           ${dato.permissions['eliminar'] ?
                                `<button type="button" class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarUsuario('${row.id}')">
                                            <i class="las la-trash-alt fs-18"></i>
                                        </button>` 
                              : ``}
                        `
                    },
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'ci',
                    },
                    {
                        data: null,
                        className: 'table-td text-uppercase',
                        render: function(data, type, row, meta) {
                            return data.nombres + ' ' + data.apellidos;
                        }
                    },
                    {
                        data: null,
                        className: 'table-td',
                        render: function(data, type, row, meta) {
                            return `
                                 ${dato.permissions['desactivar'] ?
                                `<div class="form-check form-switch form-switch-primary">
                                                <input class="form-check-input" onclick="estado_usuario('${row.id}')" type="checkbox" id="customSwitchDark" ${row.estado === 'activo' ? 'checked' : ''} >
                                            </div>`
                                : ``}
                            `;
                        }
                    },

                    {
                        data: 'roles', // Asumiendo que 'roles' es el nombre de la columna en tu conjunto de datos
                        className: 'table-td text-uppercase',
                        render: function(data, type, row, meta) {
                            // Verifica si data es un array y tiene elementos
                            if (Array.isArray(data) && data.length > 0) {
                                // Utiliza map para obtener una cadena con los nombres de los roles
                                let rolesString = data.map(function(element) {
                                    return element
                                        .name; // Asumiendo que 'name' es el atributo que contiene el nombre del rol
                                }).join(
                                    ', '); // Unirá los nombres de los roles con una coma y un espacio
                                return rolesString;
                            } else {
                                return 'Sin roles'; // O algún otro mensaje predeterminado si el usuario no tiene roles
                            }
                        }
                    },
                ],
                destroy: true,
            });
        }
        listar_usuario();


        // Función para eliminar un permiso
        function eliminarUsuario(id) {
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
                        let response = await fetch(`{{ route('user.destroy', '') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        let data = await response.json();
                        alerta_top(data.tipo, data.mensaje);
                        if (data.tipo === 'success') {
                            listar_usuario();
                        }
                    } catch (error) {
                        console.error("Error al eliminar:", error);
                    }
                } else {
                    alerta_top('error', 'Se canceló la eliminación');
                }
            })
        }

        //para cambiar el estado del registro
        async function estado_usuario(id) {
            Swal.fire({
                title: "NOTA!",
                text: "¿Está seguro de cambiar el estado?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, Cambiar",
                cancelButtonText: "Cancelar",
            }).then(async function(result) {
                if (result.isConfirmed) {
                    try {
                        let response = await fetch(`{{ route('user.show', '') }}/${id}`, {
                            method: "GET",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        let data = await response.json();
                        alerta_top(data.tipo, data.mensaje);
                        listar_usuario();
                    } catch (error) {
                        console.error("Error al cambiar el estado:", error);
                    }
                } else {
                    alerta_top('error', 'Se canceló para cambiar el estado');
                    listar_usuario();
                }
            })
        }
    </script>
@endsection
