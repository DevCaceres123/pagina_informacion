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
    tabla = $("#tabla_docentes").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarDocentes", // Ruta que recibe la solicitud en el servidor
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
                data: "nombreCompleto",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "documentoIdentidad",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "carrera.nombre",
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
                data: "profesion",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },

             {
                data: "grado_academico",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
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



$("#tabla_docentes").on("change", ".editar-fila", function () {
    const $row = $(this).closest("tr");
    const checked = $(this).is(":checked");

    if (checked) {
        // 🟢 Activar modo edición
        $row.addClass("table-warning");

        // Habilitamos el botón de actualizar
        $row.find(".actualizar_informacion")
            .prop("disabled", false)
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");

        // === Campos ===
        const nombreCelda = $row.find("td:eq(1)");
        const docCelda = $row.find("td:eq(2)");
        const generoCelda = $row.find("td:eq(5)");
        const profesionCelda = $row.find("td:eq(6)");
        const gradoCelda = $row.find("td:eq(7)");

        // Guardar valores actuales
        const nombreActual = nombreCelda.text().trim();
        const docActual = docCelda.text().trim();
        const generoActual = generoCelda.text().trim();
        const profesion = profesionCelda.text().trim();
        const grado = gradoCelda.text().trim();

        // Reemplazar con inputs
        nombreCelda.html(
            `<input type="text" class="form-control form-control-sm" name="nombreCompleto" value="${nombreActual}" />`
        );
        docCelda.html(
            `<input type="text" class="form-control form-control-sm" name="documentoIdentidad" value="${docActual}" />`
        );

        profesionCelda.html(
            `<input type="text" class="form-control form-control-sm" name="profesion" value="${profesion}" />`
        );
        gradoCelda.html(
            `<input type="text" class="form-control form-control-sm" name="grado" value="${grado}" />`
        );
        

        generoCelda.html(`
            <select class="form-select form-select-sm" name="genero">
                <option value="masculino" ${
                    generoActual === "masculino" ? "selected" : ""
                }>Masculino</option>
                <option value="femenino" ${
                    generoActual === "femenino" ? "selected" : ""
                }>Femenino</option>
            </select>
        `);

        
    } else {
        // 🔴 Desactivar modo edición
        $row.removeClass("table-warning");

        // Deshabilitamos el botón de actualizar
        $row.find(".actualizar_informacion")
            .prop("disabled", true)
            .removeClass("btn-primary")
            .addClass("btn-outline-primary");

        // Tomar valores actualizados
        const nombreNuevo = $row.find("input[name='nombreCompleto']").val();
        const docNuevo = $row.find("input[name='documentoIdentidad']").val();
        const generoNuevo = $row.find("select[name='genero']").val();
        const profesion = $row.find("input[name='profesion']").val();
        const grado = $row.find("input[name='grado']").val();

        // Volver a texto normal
        $row.find("td:eq(1)").text(nombreNuevo);
        $row.find("td:eq(2)").text(docNuevo);
        $row.find("td:eq(5)").html(
            `<span class="badge  ${
                generoNuevo === "masculino" ? "bg-success" : "bg-danger"
            } ">${generoNuevo}</span>`
        );
        $row.find("td:eq(6)").text(profesion);
        $row.find("td:eq(7)").text(grado);
    }
});



// Cuando se hace clic en el botón de actualizar
$("#tabla_docentes").on("click", ".actualizar_informacion", function (e) {
    e.preventDefault();
    const $row = $(this).closest("tr");

    // Obtenemos el ID (viene del atributo data-id del botón)
    const id = $(this).data("id");

    // Obtenemos los valores actuales (si hay inputs, tomamos su valor; si no, el texto)
    const nombre =
        $row.find("input[name='nombreCompleto']").val() ||
        $row.find("td:eq(1)").text().trim();
    const documento =
        $row.find("input[name='documentoIdentidad']").val() ||
        $row.find("td:eq(2)").text().trim();
    const genero =
        $row.find("select[name='genero']").val() ||
        $row.find("td:eq(5)").text().trim();
    const profesion =
        $row.find("input[name='profesion']").val() ||
        $row.find("td:eq(6)").text().trim();

      const grado =
         $row.find("input[name='grado']").val() ||
        $row.find("td:eq(7)").text().trim();

    // 🔹 Puedes armar un objeto con toda la data
    const datos = {
        nombreCompleto: nombre,
        documentoIdentidad: documento,
        genero: genero,
        profesion: profesion,
        grado_academico: grado,
    };

    console.log(datos);

    crud(
        "admin/actualizar_registro_docente",
        "PUT",
        id,
        datos,
        function (error, response) {
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
        }
    );
});