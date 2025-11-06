@extends('principal')
@section('titulo', 'ROLES')
@section('contenido')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                                 <i class="fas fa-user-shield  me-2"></i> LISTA DE ROLES
                            </h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" onclick="abrirModalRol()">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($listar_roles as $lis)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body p-4 color-bg rounded text-center">
                        <h4 class="text-white opacity-75 fs-16 mb-0">{{ $lis->name }}</h4>
                    </div>
                    <div class="position-relative">
                        <div class="shape overflow-hidden text-card-bg">
                            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="card-body mt-n5">
                        <div class="position-relative">
                            <img src="{{ asset('admin_template/images/logos/lang-logo/slack.png') }}" alt=""
                                class="rounded-circle thumb-xxl">
                        </div>
                        <div class="mt-3">
                            <a href="#" class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center"
                                onclick="abrirModalRol('{{ $lis->id }}')">
                                <i class="iconoir-warning-circle fs-14 me-1"></i>
                                Editar
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center"
                                onclick="eliminarRol('{{ $lis->id }}')">
                                <i class="iconoir-trash fs-14 me-1"></i>
                                Eliminar
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary px-2 d-inline-flex align-items-center"
                                onclick="vizualizarRolPermiso('{{ $lis->id }}')">
                                <i class="iconoir-eye fs-14 me-1"></i>
                                Vizualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_rol" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="rolModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="rolModalLabel">Rol</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cerrarModalRol()"></button>
                </div>
                <div class="modal-body">
                    <form id="form_rol" autocomplete="off" method="POST">
                        <input type="hidden" id="rol_id" name="rol_id">
                        <div class="mb-3 row">
                            <label for="rol" class="col-sm-2 col-form-label">Rol</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="rol" name="rol"
                                    placeholder="Ingrese el rol">
                                <div id="_rol"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h5>Permisos</h5>
                            <!-- Permission table -->
                            <div class="table-responsive">
                                <table class="table table-flush-spacing">
                                    <tbody>

                                        @if ($permisos->isEmpty())
                                            <hr>
                                            No hay ningun permiso registrado
                                        @else
                                            <tr>
                                                <td class="text-nowrap fw-medium">Seleccionar todos<i
                                                        class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Seleccionar todos los permisos disponibles solo para superadministrador"></i>
                                                </td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="marcar_des"
                                                            onchange="marcar_desmarcar(this);" />
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach ($permisos as $key => $value)
                                                <tr>
                                                    <td class="text-nowrap fw-medium">{{ $value->name }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="form-check me-3 me-lg-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permisos[]" id="{{ $value->id }}"
                                                                    value="{{ $value->name }}" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        onclick="cerrarModalRol()">Cerrar</button>
                    <button type="button" id="btn_guardar_rol" class="btn btn-dark btn-sm">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_vizualizar" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="rolNombre" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="rolNombre">Rol</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body pt-0" id="permisoListar">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        //para el error
        let errores_msj = ['_rol'];
        // Inicializamos el modal de Bootstrap
        let modal_rol = new bootstrap.Modal(document.getElementById('modal_rol'));
        let modal_vizualizar = new bootstrap.Modal(document.getElementById('modal_vizualizar'));
        //para la parte de los botones
        let form_rol = document.getElementById('form_rol');
        let btn_guardar_rol = document.getElementById('btn_guardar_rol');

        function abrirModalRol(id = null) {
            vaciar_errores(errores_msj);
            form_rol.reset();
            document.getElementById('rol_id').value = id;
            document.getElementById('rolModalLabel').textContent = id ? 'Editar Rol' : 'Nuevo Rol';
            if (id) {
                cargarDatosRol(id);
            }
            modal_rol.show();
        }
        // Función para cerrar el modal y limpiar el formulario
        function cerrarModalRol() {
            form_rol.reset();
            modal_rol.hide();
            vaciar_errores(errores_msj);
        }

        //para marcar o desmarcar los permisos
        function marcar_desmarcar(source) {
            let checkboxes = form_rol.getElementsByTagName('input');
            for (i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == "checkbox") {
                    checkboxes[i].checked = source.checked;
                }
            }
        }

        //para cuando se edita
        async function cargarDatosRol(id) {
            try {
                let respuesta = await fetch(`{{ route('roles.edit', ':id') }}`.replace(':id', id), {
                    method: "GET",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                let data = await respuesta.json();
                if (data.tipo === 'success') {
                    document.getElementById('rol').value = data.mensaje.rol.name;
                    let permisos = data.mensaje.permisos;
                    permisos.forEach(function(permiso) {
                        let checkbox = document.getElementById(permiso.id);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                } else {
                    alerta_top(data.tipo, data.mensaje);
                }
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }

        //para guardar el rol con los permisos
        btn_guardar_rol.addEventListener('click', async () => {
            let rol_id = document.getElementById('rol_id').value;
            let rol = document.getElementById('rol').value;
            let permisos = [];
            let chexbox = document.querySelectorAll('input[name="permisos[]"]:checked');
            chexbox.forEach(function(checkbox) {
                permisos.push(checkbox.value);
            });

            let url = rol_id ? `{{ route('roles.update', ':id') }}`.replace(':id', rol_id) :
                "{{ route('roles.store') }}";
            let method = rol_id ? "PUT" : "POST";

            validar_boton(true, 'Guardando...', btn_guardar_rol);

            try {
                let respuesta = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        rol_id: rol_id,
                        rol: rol,
                        permisos: permisos
                    })
                });
                let data = await respuesta.json();
                vaciar_errores(errores_msj);
                if (data.tipo === 'errores') {
                    mostrarErrores(data.mensaje);
                } else if (data.tipo === 'error' || data.tipo === 'success') {
                    alerta_top(data.tipo, data.mensaje);
                    if (data.tipo === 'success') {
                        cerrarModalRol();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                }
            } catch (error) {
                console.log('Error: ' + error);
            } finally {
                validar_boton(false, 'Guardar', btn_guardar_rol);
            }

        });

        //para eliminar el rol
        async function eliminarRol(id) {
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
                        let response = await fetch(`{{ route('roles.destroy', '') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                        });

                        let data = await response.json();
                        alerta_top(data.tipo, data.mensaje);
                        if (data.tipo === 'success') {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    } catch (error) {
                        console.error("Error al eliminar:", error);
                    }
                } else {
                    alerta_top('error', 'Se canceló la eliminación');
                }
            })
        }

        let rolNombre       = document.getElementById('rolNombre');
        let permisoListar   = document.getElementById('permisoListar');
        async function vizualizarRolPermiso(id) {
            permisoListar.innerHTML = '';
            try {
                let response = await fetch(`{{ route('roles.show', '') }}/${id}`, {
                    method: "GET",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                let data = await response.json();
                if(data.tipo === 'success'){
                    modal_vizualizar.show();
                    rolNombre.innerHTML = data.mensaje.rol;
                    data.mensaje.permisos.forEach(elem => {
                        permisoListar.innerHTML += `
                            <span class="badge bg-primary">${elem}</span>
                        `;
                    });
                }
                if(data.tipo === 'error'){
                    alerta_top(data.tipo, data.mensaje);
                }

            } catch (error) {
                console.error("Error al eliminar:", error);
            }
        }
    </script>
@endsection
