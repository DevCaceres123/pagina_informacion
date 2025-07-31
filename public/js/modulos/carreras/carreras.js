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
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_sede me-1" data-id="${row.id}" title="Eliminar carrera">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                            : ``
                        }                      
                             ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_afiliado me-1" data-id="${row.id}" title="Editar carrera">
                            <i class="fas fa-pencil-alt fs-16"></i>
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





// Validar al seleccionar imágenes
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
