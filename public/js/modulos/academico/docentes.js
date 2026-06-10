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
                data: "documento_identidad",
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

// previsualizar datos en tabla
$("#formSubirDatosExcel").on("submit", function (e) {
    e.preventDefault();
    $("#btn-importar").prop("disabled", true);
    let formData = new FormData(this);

    crud(
        "admin/previsualizarDocentes",
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

    Swal.fire({
        title: "⚠️ ¡Atención!",
        html: `
        Está a punto de <b>subir la planilla de docentes</b>.<br><br>
        <b>IMPORTANTE:</b> Solo se mantendrán <b>activos</b> los docentes 
        que estén en el archivo.<br><br>
        Si falta algún docente en el archivo, 
        <b>será marcado automáticamente como INACTIVO</b> en el sistema.<br><br>
        Por favor, asegúrese de que el archivo esté completo.
    `,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, subir archivo",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
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
                "admin/subirDatosDocentescsv",
                "POST",
                null,
                formData,
                function (error, response) {
                    $("#btnConfirmar")
                        .prop("disabled", false)
                        .text("Subir Definitivamente");

                    // Verificamos que no haya un error o que todos los campos sean llenados
                    if (response.estado === "error") {
                        mensajeAlerta(response.mensaje, "error");
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
        } else {
            alerta_top("error", "Se canceló la eliminacion");
        }
    });
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

// FUNCION PARA GENERAR REPORTE PDF DE DOCENTES

$("#generarReporte").on("click", function (e) {
    e.preventDefault();
    const tipo = $("#tipoReporte").val(); // 'sede' o 'carrera'
    const gestion = $("#gestion_filtro").val() || $("#gestion").val(); // por si manejas año

    if (!tipo) {
        alert("Seleccione primero si desea filtrar por sede o carrera.");
        return;
    }

    // Obtener IDs seleccionados
    const seleccionados = [];
    $(".check-item:checked").each(function () {
        seleccionados.push($(this).val());
    });

    if (seleccionados.length === 0) {
        alert('Seleccione al menos una opción o marque "Listar todo".');
        return;
    }

    const datos = {
        tipo: tipo,
        seleccionados: seleccionados,
        gestion: gestion,
    };
  // Deshabilitamos el botón mientras se genera el reporte
    let originalContent = $("#generarReporte").html();
    $("#generarReporte")
        .prop("disabled", true)
        .html('<i class="fas fa-spinner fa-spin me-2"></i> Generando...');
    
    crud(
        "admin/generar_reporte_docente",
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

// Validar al seleccionar pdf
$("#archivo").on("change", function () {
    let archivo = this.files[0];
    if (validarArchivos(archivo, "csv") == false) {
        $(this).val(""); // Limpiar input
    }
});




// funcion que nos servira para validar imagenes y pdf
function validarArchivos(archivos, tipo) {
    const maxSizeImagen = 3 * 1024 * 1024; // 3 MB
    const maxSizeCsv = 10 * 1024 * 1024; // 10 MB

    if (tipo === "imagen") {
        for (let i = 0; i < archivos.length; i++) {
            const file = archivos[i];

            if (!file.type.match("image.*")) {
                mensajeAlerta(
                    `El archivo "${file.name}" no es una imagen.`,
                    "error"
                );

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

    if (tipo === "csv") {
        if (!archivos) {
            mensajeAlerta("No se seleccionó ningún archivo.", "error");
            return false;
        }

        // Obtener la extensión del archivo
        const extension = archivos.name.split(".").pop().toLowerCase();

        if (extension !== "csv") {
            mensajeAlerta(
                `El archivo "${archivos.name}" no es un CSV.`,
                "error"
            );
            return false;
        }

        // Validar tamaño (por ejemplo 30 MB)
        const maxSizeCsv = 30 * 1024 * 1024; // 30 MB en bytes
        if (archivos.size > maxSizeCsv) {
            mensajeAlerta(
                `El archivo "${archivos.name}" excede el tamaño máximo de 30 MB.`,
                "error"
            );
            return false;
        }
    }

    return true; // Si pasa todas las validaciones
}
