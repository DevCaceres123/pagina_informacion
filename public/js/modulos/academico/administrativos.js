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

$("#tabla_administrativos").on("change", ".editar-fila", function () {
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
        const generoCelda = $row.find("td:eq(4)");
        const servicio = $row.find("td:eq(7)");

        // Guardar valores actuales
        const nombreActual = nombreCelda.text().trim();
        const docActual = docCelda.text().trim();
        const generoActual = generoCelda.text().trim();
        const servicioActual = servicio.text().trim();

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

        servicio.html(`
            <select class="form-select form-select-sm" name="servicio">
                <option value="planta" ${
                    servicioActual === "planta" ? "selected" : ""
                }>Planta</option>
                <option value="contrato" ${
                    servicioActual === "contrato" ? "selected" : ""
                }>Contrato</option>
                <option value="linea" ${
                    servicioActual === "linea" ? "selected" : ""
                }>Linea</option>                
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
        const servicioNuevo = $row.find("select[name='servicio']").val();

        // Volver a texto normal
        $row.find("td:eq(1)").text(nombreNuevo);
        $row.find("td:eq(2)").text(docNuevo);
        $row.find("td:eq(4)").html(
            `<span class="badge  ${
                generoNuevo === "masculino" ? "bg-success" : "bg-danger"
            } ">${generoNuevo}</span>`
        );
        $row.find("td:eq(7)").text(servicioNuevo);
        
    }
});


// Cuando se hace clic en el botón de actualizar
$("#tabla_administrativos").on("click", ".actualizar_informacion", function (e) {
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
        $row.find("td:eq(4)").text().trim();
    const servicio =
        $row.find("select[name='servicio']").val() ||
        $row.find("td:eq(7)").text().trim();

    // 🔹 Puedes armar un objeto con toda la data
    const datos = {
        nombreCompleto: nombre,
        documentoIdentidad: documento,
        genero: genero,
        servicio: servicio,
    };

    console.log(datos);

    crud(
        "admin/actualizar_registro_administrativo",
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
        "admin/subirDatosAdministrativoscsv",
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

            if (response.estado == "error_validacion") {
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
            // actualizarTabla();

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    );
});


//funcion prara mostrar los errores de importacion
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



$("#generarReporte").on("click", function (e) {
    e.preventDefault();
    const tipo = $("#tipoReporte").val(); // 'sede' o 'carrera'
    const gestion = $("#gestion_filtro").val() || $("#gestion").val(); // por si manejas año

    if (!tipo) {
        mensajeAlerta("Seleccione primero si desea filtrar por sede o servicio",'error');
        return;
    }

    // Obtener IDs seleccionados
    const seleccionados = [];
    $(".check-item:checked").each(function () {
        seleccionados.push($(this).val());
    });

    if (seleccionados.length === 0) {
        mensajeAlerta('Seleccione al menos una opción o marque Listar todo','error');
        return;
    }

    const datos = {
        tipo: tipo,
        seleccionados: seleccionados,
        gestion: gestion,
    };
    console.log(datos);
    // Deshabilitamos el botón mientras se genera el reporte
    let originalContent = $("#generarReporte").html();
    $("#generarReporte")
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin me-2"></i> Generando...');
    crud(
        "admin/generar_reporte_administrativo",
        "POST",
        null,
        datos,
        function (error, response) {
            // habilitar boton cunado termine
            $("#generarReporte").prop("disabled", false).html(originalContent);
            if (error || !response) {
                mensajeAlerta("Error al cargar la información", "error");
                return;
            }

            if (response.tipo != "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }

            //si todo esta correcto muestra el mensaje de correcto
            //$("#modalReporte").modal("hide");

            mensajeAlerta("Reporte generado espere porfavor....", "exito");
     
            setTimeout(() => {
                let pdfUrl = generarURlBlob(response.mensaje);
                window.open(pdfUrl, "_blank");
            }, 1500);

            
        }
    );
});

function generarURlBlob(pdfbase64) {
    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map((c) => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: "application/pdf" });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}

function mensajeAlertaTexto(mensaje, tipo) {
    let alertClass =
        tipo === "exito" ? "alert-success d-block" : "alert-danger d-block";

    $("#alertContainer")
        .removeClass("d-none alert-success alert-danger")
        .addClass(alertClass)
        .html(mensaje);
}