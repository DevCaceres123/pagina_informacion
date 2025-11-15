import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";


$("#formNuevaConvocatoria").on("submit", function (e) {
    
    e.preventDefault();

    $("#formNuevaConvocatoria").prop("disabled", true);
    let formData = new FormData(this);

    const btn = $("#btnGuardarNoticia");
    const tipo = btn.data("tipo");  // 👈 obtenemos tipo (nuevo o editar)
    const id = btn.data("id") ?? 'null';      // 👈 obtenemos id si existe
    btn.prop("disabled", true).html('<i class="me-1"></i> Subiendo...');

    vaciar_errores("formNuevaConvocatoria");

    // Ajustamos ruta y método
    let url = "admin/convocatoria";
    let metodo = "POST";

    if (tipo === "editar" && id) { 
        url = `admin/actualizarConvocatoria`;
        metodo = "POST";
    }


    crud(url, metodo, id, formData, function (error, response) {
        btn.prop("disabled", false).html(
            '<i class="ri-upload-cloud-line me-1"></i> Guardar Noticia'
        );

        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        vaciar_formulario("formNuevaConvocatoria");
        mensajeAlerta(response.mensaje, response.tipo);

        if (tipo === "editar" && id) { 
            setTimeout(() => {
             window.location.href = "/admin/editarConvocatoria/" + id; 
            }, 1000);
        }
    });
});


//Eliminar Foto de noticia
$(document).on('click', '.eliminar-foto', function () {
    let id = $(this).data('id');  // obtiene el id del atributo data-id

    Swal.fire({
            title: "NOTA!",
            text: "¿Está seguro de Eliminar la Imagen?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, Estoy seguro",
            cancelButtonText: "Cancelar",
        }).then(async function (result) {
            if (result.isConfirmed) {
                crud("admin/eliminarImagenConvocatoria", "DELETE", id, null, function (error, response) {
                    // console.log(response);
                   
                    if (response.tipo != "exito") {
                        mensajeAlerta(response.mensaje, response.tipo);
                        return;
                    }
                    // si todo esta correcto muestra el mensaje de correcto
                    mensajeAlerta(response.mensaje, response.tipo);
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                });
            } else {
                alerta_top("error", "Se canceló la eliminacion");
            }
        });
    
});




// Validar al seleccionar imágenes
$("#convocatoria").on("change", function () {
    let archivo = this.files[0];
    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }
});

// Validar al seleccionar imágenes
$("#fotos").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
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

    if (tipo === "pdf") {
        if (!archivos) {
            mensajeAlerta("No se seleccionó ningún archivo.", "error");
            return false;
        }

        if (archivos.type !== "application/pdf") {
            mensajeAlerta(
                `El archivo "${archivos.name}" no es un PDF.`,
                "error"
            );

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
