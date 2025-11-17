import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

$(document).ready(function () {

    
    // Configurar token CSRF globalmente
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Escucha cuando el usuario escribe
    $('#buscadorCarreras').on('keyup', function () {
        let valorBuscado = $(this).val().trim();

        // Si el texto tiene más de 2 caracteres, realiza la búsqueda
        if (valorBuscado.length > 2) {

            // console.log("Buscando carrera: " + valorBuscado);

            $.ajax({
                url: '/buscarCarrera',  // Asegúrate que esta ruta exista
                method: 'POST',
                data: { 
                    q: valorBuscado, 
                    id_sede: $('#sede_id').val()  // Envía el ID de la sede
                },
                 success: function (response) {
                    
                    let html = '';
                    console.log(response);
                    if (response.carreras.length > 0) {
                        response.carreras.forEach(carrera => {
                            html += `
                                <div class="col-6">
                                    <div class="card card-carrera-item h-100 border-0 shadow-sm">
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <h6 class="card-title fw-bold mb-2 text-uppercase">${carrera.nombre}</h6>
                                            <div class="mt-3 btn-group-vertical w-100" role="group">
                                                ${carrera.malla_curricular_pdf ? `
                                                    <a href="/storage/mallas_curriculares/${carrera.malla_curricular_pdf}" class="btn btn-danger btn-sm w-100 btn-action-malla" download>
                                                        <i class="fas fa-download me-1"></i> Malla Curricular
                                                    </a>` : ''}
                                                <a href="${carrera.vinculo_web}" target="_blank"
                                                    class="btn btn-outline-dark btn-sm w-100 btn-action-web">
                                                    <i class="fas fa-external-link-alt me-1"></i> Ver Página
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = `<p class="text-center text-muted">No hay carreras registradas en esta sede.</p>`;
                    }

                    $('#contenedorCarreras').html(html);
                },
                error: function (xhr, status, error) {
                    console.error("Error al buscar carrera:", error);
                }
            });
        }
    });

});



// desactivar bton de descargar resolucion para evitar multiples clics
$(document).ready(function () {
    $('#btnDescargarResolucion').on('click', function (e) {
        const $btn = $(this);

        // Evitar clics múltiples
        if ($btn.hasClass('disabled')) {
            e.preventDefault();
            return false;
        }

        // Cambiar el estado visual
        $btn.addClass('disabled');
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Descargando...');

        // Volver a habilitar después de unos segundos (por si acaso)
        setTimeout(() => {
            $btn.removeClass('disabled');
            $btn.html('<i class="fas fa-download me-2"></i> Descargar Resolución (PDF)');
        }, 3000);
    });
});

