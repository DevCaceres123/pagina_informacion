import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";


$(document).on("click", ".btn-enviar", function () {
    let tipo = $(this).data("tipo");
    let id = $(this).data("id");

    // Obtenemos el form correspondiente al tipo
    let form = $(`#form-${tipo}`)[0];    
    let datos = new FormData(form);   // Creamos FormData con todos los inputs del form
    datos.append("tipo", tipo);       // agregamos tipo
    datos.append("idInfraestructura", id); // agregamos id

    //console.log([...datos]); // Para ver el contenido de FormData en la consola
    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de Subir el documento?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(function (result) {
        if (result.isConfirmed) {
            // Modificamos tu función crud para enviar FormData
            crud("admin/guardarDocumentos", "POST", null, datos, function (error, response) {
                //console.log(response);
                if (response.tipo === "errores") {
                    mensajeAlerta(response.mensaje, "errores");
                    return;
                }
                if (response.tipo != "exito") {
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }
                mensajeAlerta(response.mensaje, response.tipo);
                $(`#${tipo}`).val(""); // Limpiar input file
                  setTimeout(() => {
                            location.reload();
                        }, 1500);
            }, true); // el último parámetro indica que es FormData
        } else {
            alerta_top("error", "Se canceló la operación");
        }
    });
});


// Validar al seleccionar PDF
$("#solicitud").on("change", function () {
    let archivo = this.files[0];

    if (validarArchivos(archivo, "pdf") == false) {
        $(this).val(""); // Limpiar input
    }
});



// Validar al seleccionar PDF
$("#nota").on("change", function () {
    let archivo = this.files[0];

    if (validarArchivos(archivo, "pdf") == false) {
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

    const maxSizeImagen = 5 * 1024 * 1024; // 3 MB
    const maxSizePdf = 5 * 1024 * 1024; // 2 MB

    if (tipo === "imagen") {


        for (let i = 0; i < archivos.length; i++) {
            const file = archivos[i];

            if (!file.type.match("image.*")) {
                mensajeAlerta(`El archivo "${file.name}" no es una imagen.`, "error");

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
            mensajeAlerta(`El archivo "${archivos.name}" no es un PDF.`, "error");

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
