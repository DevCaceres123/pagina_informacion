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
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "estado_inmueble",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                       <span class="badge rounded-pill bg-primary-subtle text-primary p-1 fs-6">${data}</span>
                    `;
                },
            },
            {
                data: "estado",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                       <span class="badge rounded-pill bg-primary-subtle text-success p-1 fs-6">${data}</span>
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
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_sede me-1" data-id="${row.id}" title="Eliminar Infraestructura">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                                 : ``
                         }
                      
                             ${
                                 permisosGlobal.eliminar
                                     ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_sede me-1" data-id="${row.id}" title="Editar Infraestructura">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                                     : ``
                             }
                      
                        ${
                            permisosGlobal.eliminar
                                ? ` <a class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center ver_resolucion me-1" data-id="${row.id}" data-resolucion="${row.resolucion_pdf}"  title="Ver Contrato">
                            <i class="fas fa-file-pdf fs-16 fs-16"></i>
                        </a>`
                                : ``
                        }                
                          ${
                              permisosGlobal.eliminar
                                  ? ` <a href='' class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center me-1 btnCambiarEstado" data-id="${row.id}" title="Cambiar de estado">
                            <i class="fas fa-retweet fs-16"></i>
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
    tabla_infraestructura.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}


$('#formInfraestructura').on('submit', function(e) {
    e.preventDefault();
      //$("#btnGuardarInfraestructura").prop("disabled", true);
      let formData = new FormData(this);
      console.log(formData);
      //vaciar_errores("formInfraestructura");
      crud("admin/infraestructura", "POST", null, formData, function (error, response) {
          //$("#btnGuardarInfraestructura").prop("disabled", false);
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
    $("#idElementoEstado").val(id);
    $("#estado_select").val("");
    $("#numero_nota_input").val("").removeAttr("required");
    $("#nota_input_group").addClass("d-none");

    $("#modalCambiarEstado").modal("show");
});

// Mostrar campo de nota solo si selecciona "proceso"
$("#estado_select").on("change", function () {
    if ($(this).val() === "proceso") {
        $("#nota_input_group").removeClass("d-none");
        $("#numero_nota_input").attr("required", true);
    } else {
        $("#nota_input_group").addClass("d-none");
        $("#numero_nota_input").removeAttr("required").val("");
    }
});

// Guardar (solo ejemplo de lógica, debes hacer tu AJAX aquí)
$("#guardarEstadoBtn").on("click", function () {
    const id = $("#idElementoEstado").val();
    const estado = $("#estado_select").val();
    const nota = $("#numero_nota_input").val();

    // Validación simple
    if (!estado) {
        alert("Selecciona un estado válido.");
        return;
    }

    // Aquí podrías enviar por AJAX...
    console.log("Enviando: ", { id, estado, nota });

    // Simulamos cierre
    $("#modalCambiarEstado").modal("hide");
});
