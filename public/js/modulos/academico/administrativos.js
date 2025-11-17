import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permisosGlobal;
let tabla;

$(document).ready(function () {
    listar();
});

function listar() {
    tabla = $("#tabla_administrativos").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarAdministrativos", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            data: function (d) {
                d.fecha = $("#gestion_filtro").val(); // Agrega la fecha al request
                // console.log(d);
            },
            dataSrc: function (json) {
                permisosGlobal = json.permisos;

                return json.data; // Data que se pasará al DataTable
            },
        },
        columns: [
            {
                data: null,
                className: "table-td",
                render: function (data, type, row, meta) {
                    // 🔢 Obtenemos el índice real de la fila, considerando la paginación
                    let index = meta.row + meta.settings._iDisplayStart + 1;

                    // 🧩 Devolvemos el número de fila + checkbox
                    return `
                        <div class="d-flex align-items-center justify-content-between">
                            <span>${index}</span>
                            <input type="checkbox" class="editar-fila" />
                        </div>
                    `;
                },
            },
            {
                data: "nombre_completo",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "n_documento",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },          
            {
                data: "sede.nombre",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `<span class="badge bg-secondary">${data}</span>`;
                },
            },

            {
                data: "genero",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                       <span class="badge ${
                           data === "masculino" ? "bg-success" : "bg-danger"
                       } ">${data}</span>
                    `;
                },
            },

            {
                data: "cargo",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },

            {
                data: "profesion",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },

            {
                data: "servicio",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "estado",
                className: "table-td text-uppercase",
                render: function (data, type, row) {
                    return `                            
                       <span class="badge ${
                           data === "activo" ? "bg-primary" : "bg-danger"
                       } ">${data}</span>
                    `;
                },
            },

            {
                data: null,
                className: "table-td text-end",
                render: function (data, type, row) {
                    return ` <div class="d-flex justify-content-center">

                         ${
                             permisosGlobal.editar
                                 ? `
                        <button class="btn btn-md btn-outline-primary px-2 d-inline-flex align-items-center actualizar_informacion me-1" data-id="${row.id}" title="Actualizar Informacion" disabled>
                            <i class="fas fa-check-circle  fs-16"></i>
                        </button>
                            `
                                 : ``
                         }                                            
                         
                        </div>`;
                },
            },
        ],
    });

    // Permite filtrar por una fecha diferente
    $("#gestion_filtro").on("change", function () {
        tabla.ajax.reload();
    });
}

// Llamada a la función para recargar la tabla después de una operación
function actualizarTabla() {
    tabla.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}




// previsualizar datos en tabla

$("#formSubirDatosExcel").on("submit", function (e) {
    e.preventDefault();
    $("#btn-importar").prop("disabled", true);
    let formData = new FormData(this);

    crud(
        "admin/previsualizarAdministrativos",
        "POST",
        null,
        formData,
        function (error, response) {
            $("#btn-importar").prop("disabled", false);

            // Verificamos que no haya un error o que todos los campos sean llenados
            if (response.tipo === "errores") {
                mensajeAlerta(response.mensaje, "errores");
                return;
            }

            if (response.tipo != "exito") {
                // Mostrar los mensajes de validación (cabeceras faltantes o columnas extra)
                mensajeAlertaTexto(response.mensaje, response.tipo);

                // Si el tipo es error, también ocultamos la vista previa si estaba visible
                $("#previewContainer").addClass("d-none");
                $("#previewTable tbody").empty();
                $("#previewTable thead tr").empty();
                return;
            }

            const datos = response.mensaje;

            if (Array.isArray(datos) && datos.length > 0) {
                // Generar cabeceras dinámicamente desde las claves del primer objeto
                const headers = Object.keys(datos[0]);
                $("#alertContainer").addClass("d-none");
                // Limpiar contenedor anterior
                $("#previewHeaders").empty();
                $("#previewBody").empty();

                // Crear encabezados de tabla
                headers.forEach((h) => {
                    $("#previewHeaders").append(
                        `<th class='bg-dark text-light'>${h.toUpperCase()}</th>`
                    );
                });

                // Crear filas de datos
                datos.forEach((fila) => {
                    let htmlFila = "<tr>";
                    headers.forEach((h) => {
                        htmlFila += `<td>${fila[h] ?? ""}</td>`;
                    });
                    htmlFila += "</tr>";
                    $("#previewBody").append(htmlFila);
                });

                // Mostrar la tabla de previsualización
                $("#previewContainer").removeClass("d-none");
            } else {
                mensajeAlerta("No se encontraron datos para mostrar.", "info");
            }
        }
    );
});



function mensajeAlertaTexto(mensaje, tipo) {
    let alertClass =
        tipo === "exito" ? "alert-success d-block" : "alert-danger d-block";

    $("#alertContainer")
        .removeClass("d-none alert-success alert-danger")
        .addClass(alertClass)
        .html(mensaje);
}