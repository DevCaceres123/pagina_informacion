import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";


$("#formNuevaNoticia").on("submit", function (e) {
    e.preventDefault();

    $("#formNuevaNoticia").prop("disabled", true);
    let formData = new FormData(this);

    const btn = $("#btnGuardarNoticia");
    const tipo = btn.data("tipo");  // 👈 obtenemos tipo (nuevo o editar)
    const id = btn.data("id") ?? null;      // 👈 obtenemos id si existe
    btn.prop("disabled", true).html('<i class="me-1"></i> Subiendo...');

    vaciar_errores("formNuevaNoticia");

    // Ajustamos ruta y método
    let url = "admin/noticia";
    let metodo = "POST";

    if (tipo === "editar" && id) { 
        url = `admin/actualizarNoticia`;
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

        vaciar_formulario("formNuevaNoticia");
        mensajeAlerta(response.mensaje, response.tipo);

        if (tipo === "editar" && id) { 
            setTimeout(() => {
             window.location.href = "/admin/editarNoticia/" + id; 
            }, 1000);
        }
    });
});


// Validar al seleccionar imágenes
$("#fotos").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }
});

// Validar al seleccionar imágenes
$("#portada").on("change", function () {
    let archivo = this.files;
    if (validarArchivos(archivo, "imagen") == false) {
        $(this).val(""); // Limpiar input
    }
});

// funcion que nos servira para validar imagenes y pdf
function validarArchivos(archivos, tipo) {
    const maxSizeImagen = 5 * 1024 * 1024; // 3 MB
    const maxSizePdf = 5 * 1024 * 1024; // 2 MB

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
                    `La imagen "${file.name}" excede el tamaño máximo de 5 MB.`,
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
                `El archivo "${archivos.name}" excede el tamaño máximo de 5 MB.`,
                "error"
            );

            return false;
        }
    }
    return true; // Si pasa todas las validaciones
}

document.getElementById("youtube_url").addEventListener("input", function () {
    let url = this.value.trim();
    let preview = document.getElementById("youtube_preview");
    let iframe = document.getElementById("youtube_iframe");

    // Expresión para capturar el ID del video (de URLs normales o shorts)
    let regex =
        /(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    let match = url.match(regex);

    if (match) {
        let videoId = match[1];
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
        preview.classList.remove("d-none");
    } else {
        iframe.src = "";
        preview.classList.add("d-none");
    }
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
                crud("admin/eliminarImagenNoticia", "DELETE", id, null, function (error, response) {
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

