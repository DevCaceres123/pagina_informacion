import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permisosGlobal;
let tabla_carreras;

$(document).ready(function () {
    listar_carreras();
});

function listar_carreras() {
    tabla_carreras = $("#tabla_listar_carreras").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarCarreras", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            dataSrc: function (json) {
                permisosGlobal = json.permisos;
                // console.log(permisosGlobal); // Guardar los permisos para usarlos en las columnas
                return json.data; // Data que se pasará al DataTable
            },
        },
        columns: [
            {
                data: null,
                className: "table-td",
                render: function (data, type, row, meta) {
                    return meta.row + 1; // Usar meta.row para obtener el índice de la fila
                },
            },
            {
                data: "nombre",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "modalidad",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        <span class="badge rounded-pill p-2 bg-success">${data}</span>
                    `;
                },
            },
            {
                data: "sede.nombre",
                className: "table-td text-uppercase text-center",
                render: function (data) {
                    return data;
                },
            },

            {
                data: null,
                className: "table-td",
                render: function (data, type, row) {
                    let estadoChecked =
                        row.estado === "activo" ? "checked" : "";

                    // Aquí verificamos el permiso de desactivar
                    let desactivarContent =
                        permisosGlobal["estado"] == false
                            ? `
                            <a class="cambiar_estado_carrera" data-id="${row.id},${row.estado}">
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" 
                                           ${estadoChecked} style="transform: scale(2.0);">
                                </div>
                            </a>`
                            : `
                           <p>No permitido...<p/>
                        `;

                    return `
                            <div data-class="">
                                ${desactivarContent}
                            </div>`;
                },
            },
            {
                data: null,
                className: "table-td text-end",
                render: function (data, type, row) {
                    return ` <div class="d-flex justify-content-center">

                         ${permisosGlobal.eliminar
                            ? `
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_carrera me-1" data-id="${row.id}" title="Eliminar carrera">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                            : ``
                        }                      
                             ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_carrera me-1" data-id="${row.id}" title="Editar carrera">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                            : ``
                        } 
                        ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center verMallaCurricular me-1" data-id="${row.id}" data-malla='${row.malla_curricular_pdf}' title="Ver Malla curricular">
                            <i class="fas fa-file-pdf fs-16"></i>
                        </a>`
                            : ``
                        } 
                                                                                                              
                        </div>`;
                },
            },
        ],
    });
}

// Llamada a la función para recargar la tabla después de una operación
function actualizarTabla() {
    tabla_carreras.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}


// Ingresar nueva carrera
$("#formCarrera").on("submit", function (e) {
    e.preventDefault();
    $("#btnGuardarCarrera").prop("disabled", true);
    let formData = new FormData(this);
    // console.log(formData);
    vaciar_errores("formCarrera");
    crud("admin/carrera", "POST", null, formData, function (error, response) {
        $("#btnGuardarCarrera").prop("disabled", false);
        // console.log(response);

        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        //si todo esta correcto muestra el mensaje de correcto
        $("#modalCarrera").modal("hide");
        vaciar_formulario("formCarrera");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });
});



// eliminar carrera
$(document).on("click", ".eliminar_carrera", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de Eliminar la carrera?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
            crud("admin/carrera", "DELETE", id, null, function (error, response) {
                console.log(response);
                // Verificamos que no haya un error o que todos los campos sean llenados
                if (response.tipo === "errores") {
                    mensajeAlerta(response.mensaje, "errores");
                    return;
                }
                if (response.tipo != "exito") {
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }
                // si todo esta correcto muestra el mensaje de correcto
                mensajeAlerta(response.mensaje, response.tipo);
                actualizarTabla();
            });
        } else {
            alerta_top("error", "Se canceló la eliminacion");
        }
    });
});


// cambiar estado carrera
$("#tabla_listar_carreras").on("click", ".cambiar_estado_carrera", function (e) {
    e.preventDefault(); // Evitar que el enlace recargue la página

    // Obtener el valor de data-id
    var dataId = $(this).data("id");

    // Separar el id y el estado
    var values = dataId.split(",");

    let datos = {
        estado: values[1],
    };

    crud("admin/cambiarEstado", "PUT", values[0], datos, function (error, response) {
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        mensajeAlerta(response.mensaje, response.tipo);

        actualizarTabla();
    });
});


// mostrar modal editar
$(document).on("click", ".editar_carrera", function () {

    const idImagen = $(this).data("id");
    let id_carrera = $(this).data('id'); // Obtener el id del alumno desde el data-id


    crud("admin/carrera", "GET", id_carrera + '/edit', null, function (error, response) {

        // console.log(response);

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        $('#id_carrera').val(response.mensaje.id);
        $('#nombre_edit').val(response.mensaje.nombre);
        $('#modalidad_edit').val(response.mensaje.modalidad);
        $('#sede_id_edit').val(response.mensaje.sede.id);
        $('#vinculo_web_edit').val(response.mensaje.vinculo_web);

        $('#modalCarreraEditar').modal('show');

        // si todo esta correcto muestra el mensaje de correcto
    })
});


// actualizar datos de carrera


$("#formCarreraEditar").on("submit", function (e) {
    e.preventDefault();
    $("#btnGuardarCarreraedit").prop("disabled", true);

    let datos = {
        nombre: $('#nombre_edit').val(),
        modalidad: $('#modalidad_edit').val(),
        sede_id: $('#sede_id_edit').val(),
        vinculo_web: $('#vinculo_web_edit').val(),
        // agrega más campos si los tienes
    };

    let id_carrera = $('#id_carrera').val();
    vaciar_errores("formCarreraEditar");

    crud("admin/carrera", "PUT", id_carrera, datos, function (error, response) {
        $("#btnGuardarCarreraedit").prop("disabled", false);
        // console.log(response);

        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        // //si todo esta correcto muestra el mensaje de correcto
        $("#modalCarreraEditar").modal("hide");
        vaciar_formulario("formCarreraEditar");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });
});




$(document).on("click", ".verMallaCurricular", function () {
    let id = $(this).data("id");
    let malla_curiicular = $(this).data("malla");
    // Opcional: Mostrar loader aquí

    // Asumiendo que tu controlador devuelve la ruta en response.data.resolucion_pdf
    let pdfUrl = `/storage/mallas_curriculares/${malla_curiicular}`;
    $("#iframeMalla").attr("src", pdfUrl);
    $("#modalVerMalla").modal("show");

    // Guardar el ID actual para actualizar
    $("#btnActualizarMalla").data("id", id);
});


// Actualizar PDF
$("#btnActualizarMalla").on("click", function () {
    let id = $(this).data("id");
    let archivo = $("#nuevoPdf")[0].files[0];
    // console.log(archivo);
    if (!archivo) {
        mensajeAlerta("Selecciona un archivo PDF para actualizar.", "error");
        return;
    }

    let formData = new FormData();
    formData.append("malla_curricular", archivo);
    formData.append("id", id);
    // console.log(formData);
    $("#btnActualizarMalla").prop("disabled", true);

    crud(
        `admin/malla/${id}/actualizar_malla`,
        "POST",
        null,
        formData,
        function (error, response) {
            $("#btnActualizarMalla").prop("disabled", false);
            // console.log(response);

            if (response.tipo != "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }

            //si todo esta correcto muestra el mensaje de correcto
            $("#modalVerMalla").modal("hide");
            mensajeAlerta(response.mensaje, response.tipo);
            actualizarTabla();
        }
    );
});


$("#nuevoPdf").on("change", function () {
    let archivo = this.files[0];
    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }
});



// Validar al seleccionar pdf
$("#malla_curricular").on("change", function () {
    let archivo = this.files[0];
    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }

});


// funcion que nos servira para validar imagenes y pdf
function validarArchivos(archivos, tipo) {

    const maxSizeImagen = 3 * 1024 * 1024; // 3 MB
    const maxSizePdf = 2 * 1024 * 1024; // 5 MB

    if (tipo === "imagen") {


        for (let i = 0; i < archivos.length; i++) {
            const file = archivos[i];

            if (!file.type.match("image.*")) {
                mensajeAlerta(`El archivo "${file.name}" no es una imagen.`, "error");

                return false;
            }

            if (file.size > maxSizeImagen) {
                mensajeAlerta(
                    `La imagen "${file.name}" excede el tamaño máximo de 3 MB.`,
                    "error"
                );

                return false;
            }
        }
    }

    if (tipo === "pdf") {


        if (!archivos) {
            mensajeAlerta("No se seleccionó ningún archivo.", "error");
            return false;
        }

        if (archivos.type !== "application/pdf") {
            mensajeAlerta(`El archivo "${archivos.name}" no es un PDF.`, "error");

            return false;
        }

        if (archivos.size > maxSizePdf) {
            mensajeAlerta(
                `El archivo "${archivos.name}" excede el tamaño máximo de 2 MB.`,
                "error"
            );

            return false;
        }
    }
    return true; // Si pasa todas las validaciones
}
