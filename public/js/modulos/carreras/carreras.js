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
                    return data;
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
                            <a class="cambiar_estado_sede" data-id="${row.id},${row.estado}">
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
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_sede me-1" data-id="${row.id}" title="Eliminar Sede">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                            : ``
                        }                      
                             ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_afiliado me-1" data-id="${row.id}" title="Editar Sede">
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