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
                    return `
        <div class="d-flex align-items-center justify-content-between">
            <span>${meta.row + 1}</span>
            <input type="checkbox" class="editar-fila" />
        </div>
    `;
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
                data: "sedes",
                className: "table-td text-uppercase",
                render: function (sedes) {
                    // sedes es un array de objetos [{nombre: "Sede1"}, {nombre: "Sede2"}]
                    if (!sedes || sedes.length === 0) return "";
                    return sedes
                        .map(
                            (s) =>
                                `<span class="badge bg-secondary">${s.nombre}</span>`
                        )
                        .join(" ");
                },
            },
            {
                data: "estadisticas[0].cantidad_hombres",
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
                data: "estadisticas[0].cantidad_mujeres",
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
                data: "estadisticas[0].total",
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
                    $("#previewHeaders").append(`<th class='bg-dark text-light'>${h.toUpperCase()}</th>`);
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
    let alertClass = tipo === "exito" ? "alert-success d-block" : "alert-danger d-block";

    $("#alertContainer")
        .removeClass("d-none alert-success alert-danger")
        .addClass(alertClass)
        .html(mensaje);
}
