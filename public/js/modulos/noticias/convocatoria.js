import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permisosGlobal;
let tabla;

$(document).ready(function () {
    listar_infraestructuras();
});

function listar_infraestructuras() {
    tabla = $("#tabla_listar_convocatorias").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarConvocatorias", // Ruta que recibe la solicitud en el servidor
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
                     let start = $("#tabla_listar_convocatorias")
                        .DataTable()
                        .page.info().start;
                    return start + meta.row + 1;
                },
            },
            {
                data: "titulo",
                className: "table-td text-capitalize",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "categoria.nombre",
                className: "table-td text-capitalize",
                render: function (data) {
                    return `                            
                       <span class="badge rounded-pill bg-danger text-light p-2 fs-6">${data}</span>
                    `;
                },
            },

            {
                data: "created_at_formateado",
                className: "table-td text-capitalize",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
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
                            <a class="cambiar_estado_destacado" data-id="${row.id},${row.estado_destacado}">
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

                         ${
                             permisosGlobal.eliminar
                                 ? `
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_convocatoria me-1" data-id="${row.id}" title="Eliminar Noticia">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                                 : ``
                         }
                      
                             ${
                                 permisosGlobal.eliminar
                                     ? ` <a  href="editarNoticia/${row.id}" class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_sede me-1" data-id="${row.id}" title="Editar Noticia">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                                     : ``
                             }`;
                },
            },
        ],
    });
}

// Llamada a la función para recargar la tabla después de una operación
function actualizarTabla() {
    tabla.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}



// eliminar noticia
$(document).on("click", ".eliminar_convocatoria", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de Eliminar la Convocatoria?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
            crud("admin/convocatoria", "DELETE", id, null, function (error, response) {
                // console.log(response);
               
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
