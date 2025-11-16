import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permisosGlobal;
let tabla_infraestructura;

$(document).ready(function () {
    listar_infraestructuras();
});

function listar_infraestructuras() {
    tabla_infraestructura = $("#tabla_listar_infraestructura").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarInfraestructuras", // Ruta que recibe la solicitud en el servidor
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
                data: "sede.nombre",
                className: "table-td text-capitalize",
                render: function (data) {
                    return `                            
                        ${data}
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
                data: "tiempo_tramite",
                className: "table-td text-capitalize text-center",
                render: function (data) {
                    return `                            
                      <span class="badge bg-primary">${data}</span>
                    `;
                },
            },
            {
                data: "estado_inmueble",
                className: "table-td text-uppercase text-center",
                render: function (data) {
                    if (data === 'bueno') {
                        return `                            
                      <span class="badge bg-primary">${data}</span>
                    `;
                    }

                    if (data === 'regular') {
                        return `                            
                        <span class="badge bg-info">${data}</span>
                    `;
                    }

                    if (data === 'malo') {
                        return `                            
                        <span class="badge bg-danger">${data}</span>
                    `;
                    }

                },
            },
            {
                data: "estado_tramite",
                className: "table-td text-uppercase text-center",
                render: function (data) {
                    if (data === 'inicial') {
                        return `                            
                      <span class="badge bg-secondary">${data}</span>
                    `;
                    }

                    if (data === 'proceso') {
                        return `                            
                        <span class="badge bg-info">${data}</span>
                    `;
                    }

                    if (data === 'finalizado') {
                        return `                            
                        <span class="badge bg-primary">${data}</span>
                    `;
                    }
                },
            },

            {
                data: null,
                className: "table-td text-uppercase text-center",
                render: function (data, type, row, meta) {
                    return `
                    

                     ${
                        permisosGlobal.planos
                            ? `
                            <button type="button" class="btn btn-sm btn-success rounded ver-planos" data-id='${row.id}'>
                                <i class="fas fa-university me-1" title='ver planos'></i> Planos
                             </button>
                            `
                            : ``
                    }
                `;
                },
            },


            {
                data: null,
                className: "table-td text-end",
                render: function (data, type, row) {
                    return ` <div class="d-flex justify-content-center">
                                          
                        ${permisosGlobal.editar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_sede me-1" data-id="${row.id}" title="Editar Infraestructura">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                            : ``
                        }
                      
                        ${permisosGlobal.ver_documentos
                            ? ` <a href='docuementosInfraestructura/${row.id}' class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center ver_resolucion me-1"  title="Ver Documentos">
                            <i class="fas fa-file-pdf fs-16 fs-16"></i>
                        </a>`
                            : ``
                        }                
                          ${permisosGlobal.cambiar_estado
                            ? ` <a href='' class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center me-1 btnCambiarEstado" data-id="${row.id}" title="Cambiar de estado">
                            <i class="fas fa-retweet fs-16"></i>
                        </a>`
                            : ``
                        }

                        ${permisosGlobal.datos_ubicacion
                            ? ` <a href='' class="btn btn-sm btn-outline-primary px-2 d-inline-flex align-items-center me-1 actualizarUbicacion" data-id="${row.id}" title="Datos de ubicacion">
                            <i class="fas fa-university fs-16"></i>
                        </a>`
                            : ``
                        }
                         
                         ${permisosGlobal.generar_reporte
                            ? ` <button class="btn btn-sm btn-outline-primary px-2 d-inline-flex align-items-center me-1 generar_reporte" data-id="${row.id}" title="Generar Reporte">
                            <i class="fas fa-file-archive  fs-16"></i>
                        </button>`
                            : ``
                        }

                         ${permisosGlobal.eliminar
                            ? `
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_infraestructura me-1" data-id="${row.id}" title="Eliminar Infraestructura">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
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
    tabla_infraestructura.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}


$('#formInfraestructura').on('submit', function (e) {
    e.preventDefault();
    $("#btnGuardarInfraestructura").prop("disabled", true);
    let formData = new FormData(this);

    vaciar_errores("formInfraestructura");
    crud("admin/infraestructura", "POST", null, formData, function (error, response) {
        $("#btnGuardarInfraestructura").prop("disabled", false);


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
        $("#modalInfraestructura").modal("hide");
        vaciar_formulario("formInfraestructura");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });

});

$(document).on("click", ".btnCambiarEstado", function (e) {

    e.preventDefault();
    const id = $(this).data("id");
    //  $("#guardarEstadoBtn").prop("disabled", true);
    crud("admin/estadoTramite", "GET", id, null, function (error, response) {
        //    $("#guardarEstadoBtn").prop("disabled", true);
        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        $("#idElementoEstado").val(response.mensaje.id);
        $("#estado_select").val(response.mensaje.estado_tramite);
        $("#modalCambiarEstado").modal("show");
    });


});

// Guardar (solo ejemplo de lógica, debes hacer tu AJAX aquí)
$("#guardarEstadoBtn").on("click", function () {
    const id = $("#idElementoEstado").val();
    const estado = $("#estado_select").val();


    let datos = {
        id: id,
        estado: estado,
    };
    crud("admin/cambiarEstadoTramite", "POST", null, datos, function (error, response) {

        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        actualizarTabla();
        mensajeAlerta(response.mensaje, response.tipo);

        $("#modalCambiarEstado").modal("hide");
    });
});


// Validar al seleccionar imágenes
$("#planos").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }

});

// Validar al seleccionar PDF
$("#contrato").on("change", function () {
    let archivo = this.files[0];

    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }


});



// funcion que nos servira para validar imagenes y pdf
function validarArchivos(archivos, tipo) {

    const maxSizeImagen = 3 * 1024 * 1024; // 3 MB
    const maxSizePdf = 2 * 1024 * 1024; // 2 MB

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

$(document).on("click", ".eliminar_infraestructura", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de Eliminar la Infraestructura?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
            crud("admin/infraestructura", "DELETE", id, null, function (error, response) {
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


//obtener datos de ubicacion
$(document).on("click", ".actualizarUbicacion", function (e) {
    e.preventDefault();
    $("#datosUbicacionModal").modal("show");
    const id = $(this).data("id");
    let datos = { infraestructura_id: id };

    crud("admin/datosUbicacion", "POST", null, datos, function (error, response) {
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        if (response.mensaje == null) {
            vaciar_formulario("formInfraestructuraUbicacion");
            $("#infraestructura_id").val(id);
            return;
        }
        // ✅ Poblar el formulario
        let data = response.mensaje;
        //console.log(data);
        $("#infraestructura_id").val(id);
        $("#escala").val(data.escala);
        $("#distrito").val(data.distrito);
        $("#ubicacion").val(data.ubicacion);
        $("#urb").val(data.urb);
        $("#manzano").val(data.manzano);
        $("#lote").val(data.lote);

        $("#sup_test").val(data.sup_test);
        $("#sup_lev").val(data.sup_lev);
        $("#sup_adju").val(data.sup_adju);
        $("#sup_util").val(data.sup_util);
    });
});


//guardar datos de ubicacion
$('#formInfraestructuraUbicacion').on('submit', function (e) {
    e.preventDefault();
    $("#btnGuardarInfraestructuraubicacion").prop("disabled", true);
    let formData = new FormData(this);

    vaciar_errores("formInfraestructuraUbicacion");
    crud("admin/guardarDatosUbicacion", "POST", null, formData, function (error, response) {
        $("#btnGuardarInfraestructuraubicacion").prop("disabled", false);


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
        $("#datosUbicacionModal").modal("hide");
        mensajeAlerta(response.mensaje, response.tipo);

    });

});


$(document).on("click", ".ver-planos", function () {
    let id = $(this).data("id");
    $("#modalPlanos").modal("show");
    cargarGaleria(id);

});


// listar imagenes de planos
function cargarGaleria(idInfraestructura) {
    $("#id_infrastructura").val(idInfraestructura);
    $("#galeriaContenedor").html(
        '<div class="text-center w-100">Cargando...</div>'
    );
    crud("admin/listarImagenesPlanos", "GET", idInfraestructura, null, function (error, response) {
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        let html = "";
        response.mensaje.forEach(element => {
            html += `
        <div class="col-6 col-md-3 position-relative" style="width: 220px; height: 180px;">
            <img src="${element.url}" class="img-fluid rounded border" alt="${element.nombre}" style="width: 100%; height: 100%; object-fit: contain;">
            <span class="badge bg-secondary position-absolute bottom-0 start-0 m-1">${element.mime}</span>
            <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 eliminar_imagen" data-id="${element.id}">
                <i class="fas fa-window-close fs-16"></i>
            </button>
        </div>
    `;
        });
        $("#galeriaContenedor").html(html || "<p>No hay imágenes.</p>");
    });
}


//Validar al seleccionar imágenes
$("#nuevasImagenes").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }
});


// Agregar nuevos archivos de imagenes del plano
$("#formSubirImagenesPlanos").on("submit", function (e) {

    e.preventDefault();

    const input = document.getElementById("nuevasImagenes");
    const files = input.files;

    if (files.length === 0) {
        mensajeAlerta("Selecciona imágenes para subir.", "error");
        return;
    }
    vaciar_errores("formSubirImagenesPlanos");
    let formData = new FormData(this);

    const idInfraestructura = $("#id_infrastructura").val();

    // Opcional: Desactivar botón para evitar doble clic    
    const btn = $("#btnAgregarImagenes");
    btn.prop("disabled", true).html('<i class="ri-loader-4-line spin"></i> Subiendo...');

    crud("admin/agregarImagenesPlanos", "POST", idInfraestructura, formData, function (error, response) {
        btn.prop("disabled", false).html('<i class="ri-upload-cloud-line me-1"></i> Subir Imágenes');

        if (error) {
            mensajeAlerta("Error al subir imágenes.", "error");
            console.error(error);
            return;
        }

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        mensajeAlerta(response.mensaje, "exito");
        $("#nuevasImagenes").val(""); // Limpiar input
        cargarGaleria(idInfraestructura); // Recargar galería
        $("#vistaPreviaGaleria").empty(); // Limpiar vista previa        
    });
});


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
            crud("admin/eliminarImagenPlano", "DELETE", idImagen, null, function (error, response) {
                if (error) {
                    mensajeAlerta("Error al eliminar imagen", "error");
                    return;
                }
                mensajeAlerta(response.mensaje, response.tipo);
                cargarGaleria($("#id_infrastructura").val()); // o mantener en variable el id actual
            });
        }
    });
});


// Editar datos de infraestructura
$(document).on("click", ".editar_sede", function () {
    let id_infraestructura = $(this).data('id'); // Obtener el id del alumno desde el data-id


    crud("admin/infraestructura", "GET", id_infraestructura + '/edit', null, function (error, response) {

        // console.log(response);

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        $('#id_infraestructuraEdit').val(response.mensaje.id);
        $('#sede_idEdit').val(response.mensaje.sede_id);
        $('#propiedadEdit').val(response.mensaje.propiedad);
        $('#uso_asignadoEdit').val(response.mensaje.uso_asignado);
        $('#estado_inmuebleEdit').val(response.mensaje.estado_inmueble);
        $('#observacion_estadoEdit').val(response.mensaje.observacion_estado);

        $('#modalInfraestructuraEdit').modal('show')
        // si todo esta correcto muestra el mensaje de correcto
    })
});



$("#formInfraestructuraEdit").on("submit", function (e) {
    e.preventDefault();
    $("#btnGuardarInfraestructuraEdit").prop("disabled", true);
    let formData = new FormData(this);
    vaciar_errores("formNuevaSedeEdit");

    crud("admin/actualizarInfraestructura", "POST", null, formData, function (error, response) {
        $("#btnGuardarInfraestructuraEdit").prop("disabled", false);
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
        $("#modalInfraestructuraEdit").modal("hide");
        vaciar_formulario("formInfraestructuraEdit");
        mensajeAlerta(response.mensaje, response.tipo);
        actualizarTabla();
    });
});



// Editar datos de infraestructura
$(document).on("click", ".generar_reporte", function () {
    let id_infraestructura = $(this).data('id'); // Obtener el id del alumno desde el data-id

    $(".generar_reporte").prop("disabled", true);
    crud("admin/reporteInfraestructura", "GET", id_infraestructura, null, function (error, response) {
        $(".generar_reporte").prop("disabled", false);
        //console.log(response);
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        mensajeAlerta('Reporte generado espere porfavor....', "exito");

        setTimeout(() => {
            let pdfUrl = generarURlBlob(response.mensaje);
            window.open(pdfUrl, "_blank");
        }, 1500);

    })
});


// nos servira para crear una url para poder visualizar nuestro pdf

function generarURlBlob(pdfbase64) {

    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map(c => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: 'application/pdf' });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}

