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
    tabla = $("#tabla_estudiantes").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarEstudiantes", // Ruta que recibe la solicitud en el servidor
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
                data: "hombres",
                className: "table-td text-uppercase",
                title: "Hombres",
                render: function (data, type, row) {
                    return `<input type="number" class="form-control form-control-sm text-center hombres" 
                       name="estadisticas[${row.id}][hombres]" 
                       value="${data && data !== "" ? data : 0}" 
                       min="0" disabled>`;
                },
            },
            {
                data: "mujeres",
                className: "table-td text-uppercase",
                title: "Mujeres",
                render: function (data, type, row) {
                    return `<input type="number" class="form-control form-control-sm text-center mujeres" 
                       name="estadisticas[${row.id}][mujeres]" 
                       value="${data && data !== "" ? data : 0}" 
                       min="0" disabled>`;
                },
            },

            {
                data: "total",
                className: "table-td text-uppercase ",
                title: "Total",
                render: function (data, type, row) {
                    // Calculamos total dinámicamente: hombres + mujeres
                    const total =
                        (row.cantidad_hombres ?? 0) +
                        (row.cantidad_mujeres ?? 0);
                    return `${data && data !== "" ? data : 0}`;
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

$("#tabla_estudiantes").on("change", ".editar-fila", function () {
    const $row = $(this).closest("tr");
    const checked = $(this).is(":checked");

    // Habilitar/deshabilitar inputs
    $row.find("input.hombres, input.mujeres").prop("disabled", !checked);

    // Habilitar/deshabilitar botón
    $row.find("button.actualizar_informacion").prop("disabled", !checked);

    // Opcional: cambiar apariencia del botón
    if (checked) {
        $row.find("button.actualizar_informacion")
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");
    } else {
        $row.find("button.actualizar_informacion")
            .removeClass("btn-primary")
            .addClass("btn-outline-primary");
    }
});

$("#tabla_estudiantes").on("click", ".actualizar_informacion", function (e) {
    e.preventDefault(); // Evita que haga submit o navegue
    const $row = $(this).closest("tr");

    const id = $(this).data("id");
    const hombres = parseInt($row.find("input.hombres").val()) || 0;
    const mujeres = parseInt($row.find("input.mujeres").val()) || 0;
    const gestion = $("#gestion_filtro").val();
    //const total = parseInt($row.find('input.total').val()) || 0;

    const datos = {
        id: id,
        hombres: hombres,
        mujeres: mujeres,
        gestion: gestion,
    };

    crud(
        "admin/actualizar_registro_estudiante",
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
        "admin/generar_reporte_estudiante",
        "POST",
        null,
        datos,
        function (error, response) {
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

            // habilitar boton cunado termine
            $("#generarReporte").prop("disabled", false).html(originalContent);
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

$("#formSubirDatosExcel").on("submit", function (e) {
    e.preventDefault();
    $("#btn-importar").prop("disabled", true);
    let formData = new FormData(this);

    crud(
        "admin/previsualizarExcel",
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
        "admin/subirDatosEstudiantecsv",
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
    const maxSizeCsv = 30 * 1024 * 1024; // 30 MB

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
