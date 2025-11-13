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
    tabla = $("#tabla_titulados").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarTitulados", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            data: function (d) {
                d.gestion = $("#anio").val(); // Agrega la fecha al request
                d.colacion = $("#fecha_filtro").val(); // Agrega la fecha al request
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
                data: "grado_academico",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                       <span class="badge bg-primary">${data}</span>
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
    $("#btnFiltrar").on("click", function (e) {
        e.preventDefault();        
        const textoVisible = $("#fecha_filtro option:selected").text(); // ej: "12 de marzo"
        $("#fecha_filtrada").text(` ${textoVisible}`);
        tabla.ajax.reload();
    });
}

// Llamada a la función para recargar la tabla después de una operación
function actualizarTabla() {
    tabla.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}
$("#tabla_titulados").on("change", ".editar-fila", function () {
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
        const gradoCelda = $row.find("td:eq(6)");

        // Guardar valores actuales
        const nombreActual = nombreCelda.text().trim();
        const docActual = docCelda.text().trim();
        const generoActual = generoCelda.text().trim();
        const gradoActual = gradoCelda.text().trim();

        // Reemplazar con inputs
        nombreCelda.html(
            `<input type="text" class="form-control form-control-sm" name="nombreCompleto" value="${nombreActual}" />`
        );
        docCelda.html(
            `<input type="text" class="form-control form-control-sm" name="documentoIdentidad" value="${docActual}" />`
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

        gradoCelda.html(`
            <select class="form-select form-select-sm" name="grado_academico">
                <option value="licenciatura" ${
                    gradoActual === "licenciatura" ? "selected" : ""
                }>Licenciatura</option>
                <option value="tecnico medio" ${
                    gradoActual === "tecnico medio" ? "selected" : ""
                }>Técnico Medio</option>
                <option value="tecnico superior" ${
                    gradoActual === "tecnico superior" ? "selected" : ""
                }>Técnico superior</option>                
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
        const gradoNuevo = $row.find("select[name='grado_academico']").val();

        // Volver a texto normal
        $row.find("td:eq(1)").text(nombreNuevo);
        $row.find("td:eq(2)").text(docNuevo);
        $row.find("td:eq(5)").html(
            `<span class="badge  ${
                generoNuevo === "masculino" ? "bg-success" : "bg-danger"
            } ">${generoNuevo}</span>`
        );
        $row.find("td:eq(6)").html(
            `<span class="badge bg-primary">${gradoNuevo}</span>`
        );
    }
});

// Cuando se hace clic en el botón de actualizar
$("#tabla_titulados").on("click", ".actualizar_informacion", function (e) {
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
    const grado =
        $row.find("select[name='grado_academico']").val() ||
        $row.find("td:eq(6)").text().trim();

    // 🔹 Puedes armar un objeto con toda la data
    const datos = {
        nombreCompleto: nombre,
        documentoIdentidad: documento,
        genero: genero,
        grado_academico: grado,
    };

    crud(
        "admin/actualizar_registro_titulado",
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

// previsualizar datos en tabla

$("#formSubirDatosExcel").on("submit", function (e) {
    e.preventDefault();
    $("#btn-importar").prop("disabled", true);
    let formData = new FormData(this);

    crud(
        "admin/previsualizarTitulados",
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

// funcion pra subir los datos del archivo
$("#btnConfirmar").on("click", function (e) {
    e.preventDefault();

    // Obtenemos el formulario completo
    let form = $("#formSubirDatosExcel")[0];

    // Creamos el FormData (necesario para enviar archivos)
    let formData = new FormData(form);

    // Verificamos que se haya seleccionado un archivo
    if (!$("#archivo").val()) {
        alert(
            "Por favor selecciona un archivo CSV o Excel antes de continuar."
        );
        return;
    }

    // Deshabilitamos el botón mientras se sube
    $("#btnConfirmar").prop("disabled", true).text("Importando...");

    crud(
        "admin/subirDatosTituladoscsv",
        "POST",
        null,
        formData,
        function (error, response) {
            $("#btnConfirmar")
                .prop("disabled", false)
                .text("Subir Definitivamente");

            // Verificamos que no haya un error o que todos los campos sean llenados
            if (response.tipo === "errores") {
                mensajeAlerta(response.mensaje, "errores");
                return;
            }

            if (response.tipo == "error_validacion") {
                // Mostrar los mensajes de validación (cabeceras faltantes o columnas extra)
                mostrarErroresImportacion(
                    response.errores_validacion,
                    response.errores_personalizados,
                    null
                );

                $("#previewContainer").addClass("d-none");
                $("#previewTable tbody").empty();
                $("#previewTable thead tr").empty();
                return;
            }

            mostrarErroresImportacion(
                response.errores_validacion,
                response.errores_personalizados,
                response.filas_insertadas
            );
            $("#archivo").val("");

            $("#previewContainer").addClass("d-none");
            $("#previewTable tbody").empty();
            $("#previewTable thead tr").empty();
            actualizarTabla();
        }
    );
});

function mostrarErroresImportacion(
    erroresValidacion = [],
    erroresPersonalizados = [],
    filas_insertadas
) {
    const alertContainer = $("#alertContainer");

    // Limpiamos el contenido anterior
    alertContainer.removeClass("d-none alert-success alert-danger").empty();

    // Si no hay errores, mostramos un mensaje verde de éxito
    if (erroresValidacion.length === 0 && erroresPersonalizados.length === 0) {
        alertContainer
            .addClass("alert-success")
            .html(
                `<strong>✅ Importación completada con éxito.</strong> Filas insertadas:${filas_insertadas}`
            );
        return;
    }

    // Si hay errores, mostramos en rojo
    alertContainer.addClass("alert-danger");

    let html =
        "<strong>Se encontraron errores en la importación:</strong><ul class='mt-2'>";

    // 🔸 Errores de validación (columnas, formatos, etc.)
    erroresValidacion.forEach((err) => {
        html += `
            <li>
                <b>Fila ${err.row ?? "?"}</b>: ${
            err.errors ? err.errors.join(", ") : "Error de validación"
        }
            </li>
        `;
    });

    // 🔸 Errores personalizados (carrera, sede o relación inexistente)
    erroresPersonalizados.forEach((err) => {
        html += `
            <li>
                <b>Fila ${err.fila ?? "?"}</b> – Campo: <b>${err.campo}</b><br>
                Valor: <i>${err.valor}</i><br>
                Mensaje: ${err.mensaje}
            </li>
        `;
    });

    html += "</ul>";

    alertContainer.html(html);
}

function mensajeAlertaTexto(mensaje, tipo) {
    let alertClass =
        tipo === "exito" ? "alert-success d-block" : "alert-danger d-block";

    $("#alertContainer")
        .removeClass("d-none alert-success alert-danger")
        .addClass(alertClass)
        .html(mensaje);
}
