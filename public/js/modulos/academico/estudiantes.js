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

$('#tabla_estudiantes').on('click', '.actualizar_informacion', function(e) {
    e.preventDefault(); // Evita que haga submit o navegue
    const $row = $(this).closest('tr');

    const id = $(this).data('id');
    const hombres = parseInt($row.find('input.hombres').val()) || 0;
    const mujeres = parseInt($row.find('input.mujeres').val()) || 0;
    //const total = parseInt($row.find('input.total').val()) || 0;
    
    const datos = {
        id: id,
        hombres: hombres,
        mujeres: mujeres,        
    };

    crud("admin/actualizar_registro_estudiante", "PUT", id, datos, function (error, response) {
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
    });
    
});





// $('#guardar_totales').on('click', function() {
//     const table = $('#tabla_estudiantes').DataTable();
//     const gestion = $('#gestion').val(); // si quieres mandar el año

//     const allData = [];

//     table.rows().every(function() {
//         const $row = $(this.node());
//         const checked = $row.find('.editar-fila').is(':checked');
//         if (!checked) return;

//         const id = $row.find('button.actualizar_informacion').data('id');
//         const hombres = parseInt($row.find('input.hombres').val()) || 0;
//         const mujeres = parseInt($row.find('input.mujeres').val()) || 0;
//         const total = parseInt($row.find('input.total').val()) || 0;

//         allData.push({ id, hombres, mujeres, total, gestion });
//     });

//     console.log("Datos masivos:", allData);

//     // Aquí enviarías por AJAX a Laravel
//     /*
//     $.post('/ruta/guardar_estadisticas', { estadisticas: allData, _token: $('input[name=_token]').val() }, function(response){
//         alert('Totales guardados!');
//     });
//     */
// });



