import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permisosGlobal;
let tabla_sedes;

$(document).ready(function () {
    listar_afiliado();
});

function listar_afiliado() {
    tabla_sedes = $("#tabla_listar_sedes").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarSedes", // Ruta que recibe la solicitud en el servidor
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
                data: "descripcion",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "resolucion",
                className: "table-td text-uppercase",
                render: function (data) {
                    return data;
                },
            },
            {
                data: "carreras",
                className: "table-td text-uppercase text-center",
                render: function (data, type, row, meta) {
                    return `
                    <button type="button" class="btn btn-sm btn-success rounded ver-carreras" data-carreras='${JSON.stringify(
                        data
                    )}'>
                        <i class="fas fa-graduation-cap me-1"></i> Ver Carreras
                    </button>
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
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_sede me-1" data-id="${row.id}" title="Editar Sede">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                            : ``
                        }
                      
                        ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center ver_resolucion me-1" data-id="${row.id}" data-resolucion="${row.resolucion_pdf}"  title="Ver Resolucion">
                            <i class="fas fa-file-pdf fs-16 fs-16"></i>
                        </a>`
                            : ``
                        }
                        
                         ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-primary px-2 d-inline-flex align-items-center ver_imagenes me-1" data-id="${row.id}" title="Actualizar Imagenes">
                            <i class="fas fa-images fs-16"></i>
                        </a>`
                            : ``
                        }

                          ${permisosGlobal.eliminar
                            ? ` <a href='ubicacionSede/${row.id}' class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center me-1" data-id="${row.id}" title="Agregar Rutas">
                            <i class="fas fa-map-marked fs-16"></i>
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
    tabla_sedes.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}

$("#formNuevaSede").on("submit", function (e) {
    e.preventDefault();
    $("#btn_guardar_sede").prop("disabled", true);
    let formData = new FormData(this);
    // console.log(formData);
    vaciar_errores("formNuevaSede");
    crud("admin/sedes", "POST", null, formData, function (error, response) {
        $("#btn_guardar_sede").prop("disabled", false);
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
        $("#modalSede").modal("hide");
        vaciar_formulario("formNuevaSede");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });
});

// Validar al seleccionar imágenes
$("#galeria").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }

});

// Validar al seleccionar PDF
$("#resolucion_archivo").on("change", function () {
    let archivo = this.files[0];

    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }


});

// ver y editar la resolucion

$(document).on("click", ".ver_resolucion", function () {
    let id = $(this).data("id");
    let resolucion_pdf = $(this).data("resolucion");

    // Opcional: Mostrar loader aquí

    // Asumiendo que tu controlador devuelve la ruta en response.data.resolucion_pdf
    let pdfUrl = `/storage/resoluciones/${resolucion_pdf}`;
    $("#iframeResolucion").attr("src", pdfUrl);
    $("#modalVerResolucion").modal("show");

    // Guardar el ID actual para actualizar
    $("#btnActualizarPdf").data("id", id);
});

// Actualizar PDF
$("#btnActualizarPdf").on("click", function () {
    let id = $(this).data("id");
    let archivo = $("#nuevoPdf")[0].files[0];
    // console.log(archivo);
    if (!archivo) {
        mensajeAlerta("Selecciona un archivo PDF para actualizar.", "error");
        return;
    }

    if (archivo.type !== "application/pdf") {
        mensajeAlerta("Solo se permite archivos PDF.", "error");
        return;
    }

    if (archivo.size > 3 * 1024 * 1024) {
        // 3MB
        mensajeAlerta("El archivo no debe superar los 3 MB.", "error");
        return;
    }

    let formData = new FormData();
    formData.append("nuevoPdf", archivo);
    formData.append("_method", "POST"); // O PUT según tu ruta
    formData.append("id", id);
    // console.log(formData);
    $("#btnActualizarPdf").prop("disabled", true);

    crud(
        `admin/resolucion/${id}/actualizar_pdf`,
        "POST",
        null,
        formData,
        function (error, response) {
            $("#btnActualizarPdf").prop("disabled", false);
            // console.log(response);

            if (response.tipo != "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }

            //si todo esta correcto muestra el mensaje de correcto
            $("#modalVerResolucion").modal("hide");
            mensajeAlerta(response.mensaje, response.tipo);
            actualizarTabla();
        }
    );
});

$(document).on("click", ".eliminar_sede", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de Eliminar la sede?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
            crud("admin/sedes", "DELETE", id, null, function (error, response) {
                console.log(response);
                // Verificamos que no haya un error o que todos los campos sean llenados
                if (response.tipo === "errores") {
                    mensajeAlerta(response.mensaje, "errores");
                    return;
                }
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

// cambiar estado afiliado
$("#tabla_listar_sedes").on("click", ".cambiar_estado_sede", function (e) {
    e.preventDefault(); // Evitar que el enlace recargue la página

    // Obtener el valor de data-id
    var dataId = $(this).data("id");

    // Separar el id y el estado
    var values = dataId.split(",");

    let datos = {
        id_afiliado: values[0],
        estado: values[1],
    };

    crud("admin/sedes", "PUT", values[0], datos, function (error, response) {
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje.estado[0], "error");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        mensajeAlerta(response.mensaje, response.tipo);

        actualizarTabla();
    });
});

// cargar imagenes de la sede

$(document).on("click", ".ver_imagenes", function () {
    let id = $(this).data("id");
    $("#modalGaleria").modal("show");
    cargarGaleria(id);
});

// listar imagenes sede
function cargarGaleria(idSede) {
    $("#id_sede_actual").val(idSede);
    $("#galeriaContenedor").html(
        '<div class="text-center w-100">Cargando...</div>'
    );

    crud("admin/listarImagenes", "GET", idSede, null, function (error, response) {
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        let html = "";
        response.mensaje.forEach(element => {
            html += `
                <div class="col-6 col-md-3 position-relative" style="width: 220px; height: 180px;">
                    <img src="/storage/galeria_sedes/${element.imagen}" class="img-fluid rounded border" alt="Imagen" style="width: 100%; height: 100%; object-fit: contain;">
                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 eliminar_imagen" data-id="${element.id}">
                        <i class="fas fa-window-close fs-16"></i>
                    </button>
                </div>
            `;
        });
        $("#galeriaContenedor").html(html || "<p>No hay imágenes.</p>");

    });
}


// Eliminar imagen de la galeria

$(document).on("click", ".eliminar_imagen", function () {
    const idImagen = $(this).data("id");

    Swal.fire({
        title: "¿Eliminar imagen?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            crud("admin/eliminarImagen", "DELETE", idImagen, null, function (error, response) {
                if (error) {
                    mensajeAlerta("Error al eliminar imagen", "error");
                    return;
                }
                mensajeAlerta(response.mensaje, response.tipo);
                cargarGaleria($("#id_sede_actual").val()); // o mantener en variable el id actual
            });
        }
    });
});

// Editar sede
$(document).on("click", ".editar_sede", function () {
    const idImagen = $(this).data("id");
    let id_sede = $(this).data('id'); // Obtener el id del alumno desde el data-id

    crud("admin/sedes", "GET", id_sede + '/edit', null, function (error, response) {

        console.log(response);

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        $('#id_sede_edit').val(response.mensaje.id);
        $('#nombre_edit').val(response.mensaje.nombre);
        $('#descripcion_edit').val(response.mensaje.descripcion);
        $('#resolucion_numero_edit').val(response.mensaje.resolucion);
        $('#facebook_edit').val(response.mensaje.facebook);
        $('#youtube_edit').val(response.mensaje.youtobe);
        $('#whatsapp_edit').val(response.mensaje.whatsapp);

        $('#modalSedeEdit').modal('show')
        // si todo esta correcto muestra el mensaje de correcto
    })
});



$("#formNuevaSedeEdit").on("submit", function (e) {
    e.preventDefault();
    $("#btn_guardar_sede-edit").prop("disabled", true);
    let formData = new FormData(this);
    vaciar_errores("formNuevaSedeEdit");

    crud("admin/actualizarDatos", "POST", null, formData, function (error, response) {
        $("#btn_guardar_sede-edit").prop("disabled", false);
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

        // //si todo esta correcto muestra el mensaje de correcto
        $("#modalSedeEdit").modal("hide");
        vaciar_formulario("formNuevaSedeEdit");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });            
});

// Mostrar vistas previas al seleccionar imágenes
$("#nuevasImagenes").on("change", function () {

    const contenedorVistaPrevia = $("#vistaPreviaGaleria");
    contenedorVistaPrevia.empty(); // limpiar antes de cargar nuevas vistas previas

    const archivos = this.files;

    if (validarArchivos(archivos, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }


    Array.from(archivos).forEach((archivo) => {
        if (!archivo.type.match("image.*")) {

            return;
        }

        const lector = new FileReader();
        lector.onload = function (e) {
            const imagenHtml = `
                <div class="col-6 col-md-3 position-relative rounded border p-2 ms-2 mb-1" style="width: 300px; height: 180px;">
                    <img src="${e.target.result}" class="img-fluid " alt="Vista previa" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
            `;
            contenedorVistaPrevia.append(imagenHtml);
        };
        lector.readAsDataURL(archivo);
    });
});


// Agregar nuevos archivos de imagenes a la sede
$("#formSubirImagenes").on("submit", function (e) {

    e.preventDefault();

    const input = document.getElementById("nuevasImagenes");
    const files = input.files;

    if (files.length === 0) {
        mensajeAlerta("Selecciona imágenes para subir.", "error");
        return;
    }

    // Validar que todos los archivos sean imágenes
    for (let i = 0; i < files.length; i++) {
        if (!files[i].type.match("image.*")) {
            mensajeAlerta(`El archivo ${files[i].name} no es una imagen válida.`, "error");
            return;
        }
    }

    let formData = new FormData(this);

    const idSede = $("#id_sede_actual").val();

    // Opcional: Desactivar botón para evitar doble clic    
    const btn = $("#btnAgregarImagenes");
    btn.prop("disabled", true).html('<i class="ri-loader-4-line spin"></i> Subiendo...');

    crud("admin/agregarImagenes", "POST", idSede, formData, function (error, response) {
        btn.prop("disabled", false).html('<i class="ri-upload-cloud-line me-1"></i> Subir Imágenes');

        if (error) {
            mensajeAlerta("Error al subir imágenes.", "error");
            console.error(error);
            return;
        }

        if (response.tipo === "exito") {
            mensajeAlerta(response.mensaje, "exito");
            $("#nuevasImagenes").val(""); // Limpiar input
            cargarGaleria(idSede); // Recargar galería
            $("#vistaPreviaGaleria").empty(); // Limpiar vista previa


        } else {
            mensajeAlerta(response.mensaje, "error");
        }
    });
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
