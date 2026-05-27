import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";

let permisosGlobal;
let tabla;

$(document).ready(function () {
    listar();
});

// ============================================================
// DataTable
// ============================================================
function listar() {
    tabla = $("#tablaEstudiantes").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,        
        ajax: {
            url: "seguimiento/listar",
            type: "GET",
            dataSrc: function (json) {
                permisosGlobal = json.permisos;
                return json.data;
            },
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: "nombre_completo",
                className: "text-uppercase text-center",
            },
            { 
                data: "matricula",
                className: "text-center",
            },
            {
                data: "tipo_documento",
                className: "table-td text-center",
                render: function (data) {
                    const colores = { CI: "primary", Pasaporte: "info", Otro: "secondary" };
                    return `<span class="badge bg-${colores[data] || 'secondary'}">${data}</span>`;
                },
            },
            { 
                data: "numero_documento" ,
                className: "table-td text-center",
            },
            {
                data: "sede.nombre",
                className: "text-uppercase text-center",
                render: function (data) {
                    return `<span class="badge bg-dark">${data}</span>`;
                },
            },
            {
                data: "carrera.nombre",
                className: "text-uppercase text-center",
            },
            { data: "gestion", className: "text-center" },
            {
                data: null,
                className: "text-center",
                render: function (data, type, row) {
                    let btns = `<div class="d-flex justify-content-center gap-1">`;

                    if (permisosGlobal?.ficha) {
                        btns += `
                        <a href="${SEGUIMIENTO_URL}/${row.id}/reporte" target="_blank"
                            class="btn btn-sm btn-outline-danger" title="Ficha de Seguimiento PDF">
                            <i class="fas fa-file-pdf fs-16"></i>
                        </a>`;
                    }

                    if (permisosGlobal?.documentos) {
                        btns += `
                            <button class="btn btn-sm btn-outline-success btn-docs" data-id="${row.id}"
                                title="Documentos">
                                <i class="fas fa-folder-open fs-16"></i>
                            </button>`;
                    }
                    if (permisosGlobal?.editar) {
                        btns += `
                            <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${row.id}"
                                title="Editar">
                                <i class="fas fa-pen fs-16"></i>
                            </button>`;
                    }
                    if (permisosGlobal?.eliminar) {
                        btns += `
                            <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${row.id}"
                                title="Eliminar">
                                <i class="fas fa-trash fs-16"></i>
                            </button>`;
                    }

                    btns += `</div>`;
                    return btns;
                },
            },
        ],
    });
}

function recargar() {
    tabla.ajax.reload(null, false);
}

// ============================================================
// Modal Nuevo
// ============================================================
$("#btnNuevo").on("click", function () {
    limpiarFormularioEstudiante();
    $("#tituloModalEstudiante").text("Nuevo Estudiante");
    new bootstrap.Modal(document.getElementById("modalEstudiante")).show();
});

// ============================================================
// Guardar (crear / actualizar)
// ============================================================
$("#btnGuardarEstudiante").on("click", async function () {
    const id = $("#est_id").val();
    const datos = {
        nombre_completo:  $("#nombre_completo").val().trim(),
        matricula:        $("#matricula").val().trim(),
        genero:           $("#genero").val(),
        tipo_documento:   $("#tipo_documento").val(),
        numero_documento: $("#numero_documento").val().trim(),
        sede_id:          $("#sede_id").val(),
        carrera_id:       $("#carrera_id").val(),
        gestion:          $("#gestion").val(),
        observacion:      $("#observacion").val().trim(),
    };

    if (!datos.nombre_completo || !datos.matricula || !datos.genero || !datos.tipo_documento ||
        !datos.numero_documento || !datos.sede_id || !datos.carrera_id || !datos.gestion) {
        mensajeAlerta("Complete todos los campos obligatorios.", "error");
        return;
    }

    const metodo = id ? "PUT" : "POST";
    const url    = id ? "admin/seguimiento" : "admin/seguimiento";
    const regId  = id || null;

    crud(url, metodo, regId, datos, function (err, res) {
        if (err || !res) { mensajeAlerta("Error de conexión.", "error"); return; }
        if (res.tipo !== "exito") { mensajeAlerta(res.mensaje, res.tipo); return; }

        mensajeAlerta(res.mensaje, res.tipo);
        bootstrap.Modal.getInstance(document.getElementById("modalEstudiante")).hide();
        recargar();
    });
});

// ============================================================
// Editar
// ============================================================
$("#tablaEstudiantes").on("click", ".btn-editar", function () {
    const id = $(this).data("id");
    crud("admin/seguimiento", "GET", id, null, function (err, res) {
        if (err || res?.tipo !== "exito") { mensajeAlerta("Error al cargar datos.", "error"); return; }
        const e = res.mensaje;
        limpiarFormularioEstudiante();
        $("#est_id").val(e.id);
        $("#nombre_completo").val(e.nombre_completo);
        $("#matricula").val(e.matricula);
        $("#genero").val(e.genero);
        $("#tipo_documento").val(e.tipo_documento);
        $("#numero_documento").val(e.numero_documento);
        $("#sede_id").val(e.sede_id);
        $("#carrera_id").val(e.carrera_id);
        $("#gestion").val(e.gestion);
        $("#observacion").val(e.observacion);
        $("#tituloModalEstudiante").text("Editar Estudiante");
        new bootstrap.Modal(document.getElementById("modalEstudiante")).show();
    });
});

// ============================================================
// Eliminar
// ============================================================
$("#tablaEstudiantes").on("click", ".btn-eliminar", function () {
    const id = $(this).data("id");
    Swal.fire({
        title: "¿Eliminar estudiante?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;
        crud("admin/seguimiento/" + id + "/eliminar", "POST", null, {}, function (err, res) {
            if (err || !res) { mensajeAlerta("Error de conexión.", "error"); return; }
            mensajeAlerta(res.mensaje, res.tipo);
            if (res.tipo === "exito") recargar();
        });
    });
});

function limpiarFormularioEstudiante() {
    $("#est_id, #nombre_completo, #matricula, #numero_documento, #gestion, #observacion").val("");
    $("#genero, #tipo_documento, #sede_id, #carrera_id").val("");
}

// ============================================================
// Modal Documentos
// ============================================================
$("#tablaEstudiantes").on("click", ".btn-docs", function () {
    const id = $(this).data("id");
    cargarDocumentos(id);
});

function cargarDocumentos(id) {
    $("#doc_estudiante_id").val(id);
    crud("admin/seguimiento/" + id + "/documentos", "GET", null, null, function (err, res) {
        if (err || res?.tipo !== "exito") { mensajeAlerta("Error al cargar documentos.", "error"); return; }

        const { estudiante, formularios, requisitos } = res.mensaje;

        $("#docEstudianteInfo").text(
            estudiante.nombre_completo.toUpperCase() + " — Matrícula: " + estudiante.matricula
        );

        // Documentos principales
        const campos = [
            "certificado_habilitacion",
            "copia_titulo_tec_medio",
            "copia_titulo_tec_superior",
            "copia_titulo_licenciatura",
        ];
        campos.forEach(function (campo) {
            const val = estudiante[campo];
            if (val) {
                $("#estado_" + campo).html(`<span class="badge bg-success"><i class="fas fa-check me-1"></i>Subido</span>`);
                $("#link_" + campo)
                    .attr("href", SEGUIMIENTO_URL + "/ver-documento/" + campo + "/" + id)
                    .removeClass("d-none");
            } else {
                $("#estado_" + campo).html(`<span class="badge bg-secondary">Sin archivo</span>`);
                $("#link_" + campo).addClass("d-none");
            }
        });

        renderizarFormularios(formularios);
        renderizarExpediente(res.mensaje.requisitos_por_tipo, res.mensaje.aprobaciones);

        new bootstrap.Modal(document.getElementById("modalDocumentos")).show();
    });
}

function renderizarFormularios(formularios) {
    const tbody = $("#bodyFormularios");
    tbody.empty();
    if (!formularios || formularios.length === 0) {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">Sin formularios.</td></tr>');
        return;
    }
    formularios.forEach(function (f, i) {
        const url = SEGUIMIENTO_URL + "/ver-formulario/" + f.id;
        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td>${f.fecha_de_recepcion_formateada}</td>
                <td class="text-center">
                    <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger btn-del-formulario" data-id="${f.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

function renderizarExpediente(requisitosPorTipo, aprobaciones) {
    const tipos = ["tec_medio", "tec_superior", "licenciatura"];

    tipos.forEach(function (tipo) {
        const lista    = requisitosPorTipo[tipo] || [];
        const aprobado = aprobaciones[tipo] || false;
        const tbody    = $("#bodyRequisitos_" + tipo);

        // Estado de aprobación
        actualizarBadgeAprobacion(tipo, aprobado);

        tbody.empty();
        if (lista.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted py-2">Sin requisitos.</td></tr>');
            return;
        }
        lista.forEach(function (r, i) {
            const url = SEGUIMIENTO_URL + "/ver-requisito/" + r.id;
            tbody.append(`
                <tr>
                    <td class="ps-3" style="width:40px">${i + 1}</td>
                    <td class="text-capitalize">${r.nombre}</td>
                    <td class="text-center" style="width:60px">
                        <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                    <td class="text-center" style="width:60px">
                        <button class="btn btn-sm btn-outline-danger btn-del-requisito"
                            data-id="${r.id}" data-tipo="${tipo}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    });
}

function actualizarBadgeAprobacion(tipo, aprobado) {
    const badge = $("#badge_aprobado_" + tipo);
    const btn   = $("#btn_aprobar_" + tipo);
    if (aprobado) {
        badge.removeClass("bg-secondary").addClass("bg-success").text("Aprobado");
        btn.removeClass("btn-outline-success").addClass("btn-outline-danger")
           .html('<i class="fas fa-times-circle me-1"></i> Revocar Aprobación');
    } else {
        badge.removeClass("bg-success").addClass("bg-secondary").text("Pendiente");
        btn.removeClass("btn-outline-danger").addClass("btn-outline-success")
           .html('<i class="fas fa-check-circle me-1"></i> Marcar Aprobado');
    }
}

// Subir documentos principales (certificado, fotocopia, copia)
$(document).on("click", ".btn-subir-doc", function () {
    const tipo    = $(this).data("tipo");
    const estudId = $("#doc_estudiante_id").val();
    const input   = document.querySelector(`.doc-input[data-tipo="${tipo}"]`);
    const archivo = input?.files[0];

    if (!archivo) {
        mensajeAlerta("Seleccione un archivo PDF primero.", "error");
        return;
    }

    const btn = $(this);

    Swal.fire({
        title: "¿Subir documento?",
        text: `Se subirá el archivo: ${archivo.name}`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, subir",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append("tipo", tipo);
        formData.append("archivo", archivo);
        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

        btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Subiendo...');

        crud("admin/seguimiento/" + estudId + "/documento", "POST", null, formData, function (err, res) {
            btn.prop("disabled", false).html('<i class="fas fa-upload me-1"></i> Subir');
            if (err || !res) { mensajeAlerta("Error al subir documento.", "error"); return; }
            mensajeAlerta(res.mensaje, res.tipo);
            if (res.tipo === "exito") {
                input.value = "";
                cargarDocumentos(estudId);
            }
        });
    });
});

// Agregar formulario de inscripción
$("#btnAgregarFormulario").on("click", function () {
    const estudId  = $("#doc_estudiante_id").val();
    const fecha    = $("#form_fecha").val();
    const archivo  = document.getElementById("form_archivo").files[0];

    if (!fecha || !archivo) { mensajeAlerta("Complete la fecha y el archivo.", "error"); return; }

    const formData = new FormData();
    formData.append("fecha_recepcion", fecha);
    formData.append("archivo", archivo);
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    crud("admin/seguimiento/" + estudId + "/formulario", "POST", null, formData, function (err, res) {
        if (err || !res) { mensajeAlerta("Error al subir formulario.", "error"); return; }
        mensajeAlerta(res.mensaje, res.tipo);
        if (res.tipo === "exito") {
            $("#form_fecha").val("");
            $("#form_archivo").val("");
            cargarDocumentos(estudId);
        }
    });
});

// Eliminar formulario
$(document).on("click", ".btn-del-formulario", function () {
    const fId    = $(this).data("id");
    const estudId = $("#doc_estudiante_id").val();
    if (!confirm("¿Eliminar este formulario?")) return;
    crud("admin/seguimiento/formulario/" + fId + "/eliminar", "POST", null, {}, function (err, res) {
        if (err || !res) { mensajeAlerta("Error.", "error"); return; }
        mensajeAlerta(res.mensaje, res.tipo);
        if (res.tipo === "exito") cargarDocumentos(estudId);
    });
});

// Agregar requisito por tipo de título
$(document).on("click", ".btn-agregar-requisito", function () {
    const tipo    = $(this).data("tipo");
    const estudId = $("#doc_estudiante_id").val();
    const nombre  = $(`.req-nombre[data-tipo="${tipo}"]`).val().trim();
    const input   = document.querySelector(`.req-archivo[data-tipo="${tipo}"]`);
    const archivo = input?.files[0];

    if (!nombre || !archivo) { mensajeAlerta("Complete el nombre y el archivo.", "error"); return; }

    const formData = new FormData();
    formData.append("tipo_titulo", tipo);
    formData.append("nombre", nombre);
    formData.append("archivo", archivo);
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    crud("admin/seguimiento/" + estudId + "/requisito", "POST", null, formData, function (err, res) {
        if (err || !res) { mensajeAlerta("Error al subir requisito.", "error"); return; }
        mensajeAlerta(res.mensaje, res.tipo);
        if (res.tipo === "exito") {
            $(`.req-nombre[data-tipo="${tipo}"]`).val("");
            input.value = "";
            cargarDocumentos(estudId);
        }
    });
});

// Eliminar requisito
$(document).on("click", ".btn-del-requisito", function () {
    const rId     = $(this).data("id");
    const estudId = $("#doc_estudiante_id").val();
    Swal.fire({
        title: "¿Eliminar este requisito?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;
        crud("admin/seguimiento/requisito/" + rId + "/eliminar", "POST", null, {}, function (err, res) {
            if (err || !res) { mensajeAlerta("Error.", "error"); return; }
            mensajeAlerta(res.mensaje, res.tipo);
            if (res.tipo === "exito") cargarDocumentos(estudId);
        });
    });
});

// Aprobar / Revocar expediente por tipo de título
$(document).on("click", ".btn-aprobar-expediente", function () {
    const tipo    = $(this).data("tipo");
    const estudId = $("#doc_estudiante_id").val();
    const etiquetas = {
        tec_medio:    "Técnico Medio",
        tec_superior: "Técnico Superior",
        licenciatura: "Licenciatura",
    };

    const formData = new FormData();
    formData.append("tipo_titulo", tipo);
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    crud("admin/seguimiento/" + estudId + "/aprobar-expediente", "POST", null, formData, function (err, res) {
        if (err || !res) { mensajeAlerta("Error de conexión.", "error"); return; }
        if (res.tipo !== "exito") { mensajeAlerta(res.mensaje.mensaje, "error"); return; }
        const { aprobado, mensaje } = res.mensaje;
        mensajeAlerta(mensaje, "exito");
        actualizarBadgeAprobacion(tipo, aprobado);
    });
});

// ============================================================
// Reporte General
// ============================================================

// ── Filtrar carreras según sedes seleccionadas ──────────────
function filtrarCarrerasPorSede() {
    const sedesSeleccionadas = Array.from(document.querySelectorAll(".chk-sede:checked"))
        .map(c => parseInt(c.value));

    const busqueda = document.getElementById("buscarCarrera").value.toLowerCase();

    document.querySelectorAll(".chk-carrera-wrap").forEach(function (wrap) {
        const chk       = wrap.querySelector(".chk-carrera");
        const carreraId = parseInt(chk.value);
        const nombre    = wrap.querySelector("label").textContent.toLowerCase();

        let disponiblePorSede = true;
        if (sedesSeleccionadas.length > 0) {
            disponiblePorSede = sedesSeleccionadas.some(sedeId =>
                CARRERA_SEDE[sedeId] && CARRERA_SEDE[sedeId].includes(carreraId)
            );
        }

        const coincideBusqueda = nombre.includes(busqueda);
        wrap.hidden  = !disponiblePorSede || !coincideBusqueda;
        chk.disabled = !disponiblePorSede;
        if (!disponiblePorSede) chk.checked = false;
    });

    // Sincronizar estado del "Todas" de carreras
    actualizarEstadoTodasCarreras();
}

function actualizarEstadoTodasSedes() {
    const total   = document.querySelectorAll(".chk-sede").length;
    const checked = document.querySelectorAll(".chk-sede:checked").length;
    const chkAll  = document.getElementById("chkTodasSedes");
    chkAll.checked       = checked === total && total > 0;
    chkAll.indeterminate = checked > 0 && checked < total;
}

function actualizarEstadoTodasCarreras() {
    const visibles = document.querySelectorAll(".chk-carrera-wrap:not([hidden]) .chk-carrera:not(:disabled)");
    const checked  = document.querySelectorAll(".chk-carrera-wrap:not([hidden]) .chk-carrera:checked");
    const chkAll   = document.getElementById("chkTodasCarreras");
    chkAll.checked       = visibles.length > 0 && visibles.length === checked.length;
    chkAll.indeterminate = checked.length > 0 && checked.length < visibles.length;
}

// ── Buscador de sedes ───────────────────────────────────────
document.getElementById("buscarSede").addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll(".chk-sede-wrap").forEach(function (wrap) {
        const nombre = wrap.querySelector("label").textContent.toLowerCase();
        wrap.style.display = nombre.includes(q) ? "" : "none";
    });
});

// ── Buscador de carreras ────────────────────────────────────
document.getElementById("buscarCarrera").addEventListener("input", filtrarCarrerasPorSede);

// ── Checkbox "Todas las sedes" ──────────────────────────────
document.getElementById("chkTodasSedes").addEventListener("change", function () {
    document.querySelectorAll(".chk-sede").forEach(c => c.checked = this.checked);
    filtrarCarrerasPorSede();
    actualizarEstadoTodasSedes();
});

// ── Checkboxes individuales de sede ────────────────────────
document.querySelectorAll(".chk-sede").forEach(function (chk) {
    chk.addEventListener("change", function () {
        filtrarCarrerasPorSede();
        actualizarEstadoTodasSedes();
    });
});

// ── Checkbox "Todas las carreras" ───────────────────────────
document.getElementById("chkTodasCarreras").addEventListener("change", function () {
    document.querySelectorAll(".chk-carrera-wrap:not([hidden]) .chk-carrera:not(:disabled)")
        .forEach(c => c.checked = this.checked);
    actualizarEstadoTodasCarreras();
});

// ── Checkboxes individuales de carrera ──────────────────────
document.querySelectorAll(".chk-carrera").forEach(function (chk) {
    chk.addEventListener("change", actualizarEstadoTodasCarreras);
});

// ── Toggle "Agrupar por" según tipo de vista ────────────────
document.querySelectorAll("input[name='rep_vista']").forEach(function (radio) {
    radio.addEventListener("change", function () {
        document.getElementById("seccionAgruparPor").style.display =
            this.value === "totales" ? "" : "none";
    });
});

// ── Generar reporte (AJAX + blob) ───────────────────────────
$("#btnGenerarReporteGeneral").on("click", function () {
    const btn = $(this);
    btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Generando...');

    const formData = new FormData();
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    document.querySelectorAll(".chk-sede:checked")
        .forEach(c => formData.append("sede_id[]", c.value));
    document.querySelectorAll(".chk-carrera:checked")
        .forEach(c => formData.append("carrera_id[]", c.value));

    const genero       = $("#rep_genero").val();
    const gestionDesde = $("#rep_gestion_desde").val().trim();
    const gestionHasta = $("#rep_gestion_hasta").val().trim();
    const vista        = document.querySelector("input[name='rep_vista']:checked").value;

    if (genero)       formData.append("genero",        genero);
    if (gestionDesde) formData.append("gestion_desde", gestionDesde);
    if (gestionHasta) formData.append("gestion_hasta", gestionHasta);
    formData.append("vista", vista);
    if (vista === "totales") formData.append("agrupar_por", $("#rep_agrupar_por").val());

    $.ajax({
        url: SEGUIMIENTO_URL + "/reporte-general",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            btn.prop("disabled", false).html('<i class="fas fa-file-pdf me-1"></i> Generar PDF');
            if (res.tipo !== "exito") { mensajeAlerta(res.mensaje, "error"); return; }
            Swal.fire({
                icon: "success",
                title: "¡Reporte generado!",
                text: "El PDF se abrirá en una nueva pestaña.",
                timer: 2000,
                showConfirmButton: false,
            });
            window.open(generarUrlBlob(res.mensaje), "_blank");
        },
        error: function () {
            btn.prop("disabled", false).html('<i class="fas fa-file-pdf me-1"></i> Generar PDF');
            mensajeAlerta("Error al generar el reporte.", "error");
        },
    });
});

function generarUrlBlob(pdfBase64) {
    const bytes = atob(pdfBase64);
    const array = new Uint8Array(bytes.length);
    for (let i = 0; i < bytes.length; i++) array[i] = bytes.charCodeAt(i);
    return URL.createObjectURL(new Blob([array], { type: "application/pdf" }));
}

// ============================================================
// Reporte Pendientes
// ============================================================

function filtrarCarrerasPorSedePend() {
    const sedesSeleccionadas = Array.from(document.querySelectorAll(".chk-sede-pend:checked"))
        .map(c => parseInt(c.value));

    const busqueda = document.getElementById("buscarCarreraPend").value.toLowerCase();

    document.querySelectorAll(".chk-carrera-wrap-pend").forEach(function (wrap) {
        const chk       = wrap.querySelector(".chk-carrera-pend");
        const carreraId = parseInt(chk.value);
        const nombre    = wrap.querySelector("label").textContent.toLowerCase();

        let disponiblePorSede = true;
        if (sedesSeleccionadas.length > 0) {
            disponiblePorSede = sedesSeleccionadas.some(sedeId =>
                CARRERA_SEDE[sedeId] && CARRERA_SEDE[sedeId].includes(carreraId)
            );
        }

        const coincideBusqueda = nombre.includes(busqueda);
        wrap.hidden  = !disponiblePorSede || !coincideBusqueda;
        chk.disabled = !disponiblePorSede;
        if (!disponiblePorSede) chk.checked = false;
    });

    actualizarEstadoTodasCarrerasPend();
}

function actualizarEstadoTodasSedesPend() {
    const total   = document.querySelectorAll(".chk-sede-pend").length;
    const checked = document.querySelectorAll(".chk-sede-pend:checked").length;
    const chkAll  = document.getElementById("chkTodasSedesPend");
    chkAll.checked       = checked === total && total > 0;
    chkAll.indeterminate = checked > 0 && checked < total;
}

function actualizarEstadoTodasCarrerasPend() {
    const visibles = document.querySelectorAll(".chk-carrera-wrap-pend:not([hidden]) .chk-carrera-pend:not(:disabled)");
    const checked  = document.querySelectorAll(".chk-carrera-wrap-pend:not([hidden]) .chk-carrera-pend:checked");
    const chkAll   = document.getElementById("chkTodasCarrerasPend");
    chkAll.checked       = visibles.length > 0 && visibles.length === checked.length;
    chkAll.indeterminate = checked.length > 0 && checked.length < visibles.length;
}

document.getElementById("buscarSedePend").addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll(".chk-sede-wrap-pend").forEach(function (wrap) {
        const nombre = wrap.querySelector("label").textContent.toLowerCase();
        wrap.style.display = nombre.includes(q) ? "" : "none";
    });
});

document.getElementById("buscarCarreraPend").addEventListener("input", filtrarCarrerasPorSedePend);

document.getElementById("chkTodasSedesPend").addEventListener("change", function () {
    document.querySelectorAll(".chk-sede-pend").forEach(c => c.checked = this.checked);
    filtrarCarrerasPorSedePend();
    actualizarEstadoTodasSedesPend();
});

document.querySelectorAll(".chk-sede-pend").forEach(function (chk) {
    chk.addEventListener("change", function () {
        filtrarCarrerasPorSedePend();
        actualizarEstadoTodasSedesPend();
    });
});

document.getElementById("chkTodasCarrerasPend").addEventListener("change", function () {
    document.querySelectorAll(".chk-carrera-wrap-pend:not([hidden]) .chk-carrera-pend:not(:disabled)")
        .forEach(c => c.checked = this.checked);
    actualizarEstadoTodasCarrerasPend();
});

document.querySelectorAll(".chk-carrera-pend").forEach(function (chk) {
    chk.addEventListener("change", actualizarEstadoTodasCarrerasPend);
});

// ── Modalidades: checkbox "Todas" ─────────────────────────────────────────────
$("#chkTodasModPend").on("change", function () {
    $(".chk-modalidad-pend").prop("checked", this.checked);
});
$(".chk-modalidad-pend").on("change", function () {
    const total    = $(".chk-modalidad-pend").length;
    const checked  = $(".chk-modalidad-pend:checked").length;
    $("#chkTodasModPend").prop("checked", total === checked);
});

// ── Tipo de reporte: actualizar UI ───────────────────────────────────────────
$("input[name='pend_tipo_reporte']").on("change", function () {
    const esAprobados = this.value === "aprobados";

    if (esAprobados) {
        $("#titleReportePendientes").html(
            '<i class="fas fa-check-circle me-2 text-success"></i>Reporte de Aprobados para Titularse'
        );
        $("#descReportePendientes").text(
            "Muestra estudiantes según su estado de aprobación en el Expediente de Titulación."
        );
        $("#lbl_no_cumplen").text("No aprobados");
        $("#lbl_cumplen").text("Aprobados");
        $("#pend_estado_cumplen").prop("checked", true);
        $("#btnGenerarReportePendientes").removeClass("btn-warning").addClass("btn-success");
        $("#btnPendientesLabel").text("Generar PDF de Aprobados");
    } else {
        $("#titleReportePendientes").html(
            '<i class="fas fa-exclamation-triangle me-2 text-warning"></i>Reporte de Documentos Pendientes'
        );
        $("#descReportePendientes").text(
            "Muestra estudiantes según el estado de sus documentos de titulación."
        );
        $("#lbl_no_cumplen").text("Con pendientes");
        $("#lbl_cumplen").text("Completos");
        $("#pend_estado_no_cumplen").prop("checked", true);
        $("#btnGenerarReportePendientes").removeClass("btn-success").addClass("btn-warning");
        $("#btnPendientesLabel").text("Generar PDF");
    }
});

// Resetear al abrir el modal
$("#modalReportePendientes").on("show.bs.modal", function () {
    $(".chk-modalidad-pend").prop("checked", true);
    $("#chkTodasModPend").prop("checked", true);
    $("#pend_tipo_pendientes").prop("checked", true).trigger("change");
});

$("#btnGenerarReportePendientes").on("click", function () {
    const modalidadesSeleccionadas = $(".chk-modalidad-pend:checked");
    if (modalidadesSeleccionadas.length === 0) {
        mensajeAlerta("Seleccione al menos una modalidad.", "warning");
        return;
    }

    const btn = $(this);
    btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Generando...');

    const formData = new FormData();
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    document.querySelectorAll(".chk-sede-pend:checked")
        .forEach(c => formData.append("sede_id[]", c.value));
    document.querySelectorAll(".chk-carrera-pend:checked")
        .forEach(c => formData.append("carrera_id[]", c.value));
    modalidadesSeleccionadas.each(function () {
        formData.append("modalidad[]", this.value);
    });

    const tipoReporte  = $("input[name='pend_tipo_reporte']:checked").val();
    const estado       = $("input[name='pend_estado']:checked").val();
    const genero       = $("#pend_genero").val();
    const gestionDesde = $("#pend_gestion_desde").val().trim();
    const gestionHasta = $("#pend_gestion_hasta").val().trim();

    formData.append("tipo_reporte", tipoReporte);
    formData.append("estado", estado);
    if (genero)       formData.append("genero",        genero);
    if (gestionDesde) formData.append("gestion_desde", gestionDesde);
    if (gestionHasta) formData.append("gestion_hasta", gestionHasta);

    $.ajax({
        url: SEGUIMIENTO_URL + "/reporte-pendientes",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            const esAprobados = $("input[name='pend_tipo_reporte']:checked").val() === "aprobados";
            const labelBtn = esAprobados ? "Generar PDF de Aprobados" : "Generar PDF";
            btn.prop("disabled", false).html(`<i class="fas fa-file-pdf me-1"></i> ${labelBtn}`);
            if (res.tipo !== "exito") { mensajeAlerta(res.mensaje, "error"); return; }
            Swal.fire({
                icon: "success",
                title: "¡Reporte generado!",
                text: "El PDF se abrirá en una nueva pestaña.",
                timer: 2000,
                showConfirmButton: false,
            });
            window.open(generarUrlBlob(res.mensaje), "_blank");
        },
        error: function () {
            const esAprobados = $("input[name='pend_tipo_reporte']:checked").val() === "aprobados";
            const labelBtn = esAprobados ? "Generar PDF de Aprobados" : "Generar PDF";
            btn.prop("disabled", false).html(`<i class="fas fa-file-pdf me-1"></i> ${labelBtn}`);
            mensajeAlerta("Error al generar el reporte.", "error");
        },
    });
});

// ============================================================
// Modal CSV
// ============================================================
$("#formCSV").on("submit", function (e) {
    e.preventDefault();
    $("#btnPrevisualizarCSV").prop("disabled", true);

    const formData = new FormData(this);

    crud("admin/seguimiento/previsualizarCSV", "POST", null, formData, function (err, res) {
        $("#btnPrevisualizarCSV").prop("disabled", false);

        if (err || !res) { mensajeAlerta("Error de conexión.", "error"); return; }
        if (res.tipo !== "exito") {
            mostrarAlertaCSV(res.mensaje, "danger");
            $("#csv_preview").addClass("d-none");
            return;
        }

        const datos = res.mensaje;
        if (!Array.isArray(datos) || datos.length === 0) {
            mostrarAlertaCSV("No se encontraron datos.", "warning");
            return;
        }

        const headers = Object.keys(datos[0]);
        $("#csv_headers").empty();
        headers.forEach(h => $("#csv_headers").append(`<th>${h.toUpperCase()}</th>`));

        $("#csv_body").empty();
        datos.forEach(function (fila) {
            let tr = "<tr>";
            headers.forEach(h => { tr += `<td>${fila[h] ?? ""}</td>`; });
            tr += "</tr>";
            $("#csv_body").append(tr);
        });

        $("#csv_alert").addClass("d-none");
        $("#csv_preview").removeClass("d-none");
    });
});

$("#btnConfirmarCSV").on("click", function () {
    $(this).prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i> Importando...');
    const formData = new FormData(document.getElementById("formCSV"));

    crud("admin/seguimiento/importarCSV", "POST", null, formData, function (err, res) {
        $("#btnConfirmarCSV").prop("disabled", false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Subir Definitivamente');

        if (err || !res) { mensajeAlerta("Error de conexión.", "error"); return; }

        if (res.estado === "error_validacion") {
            mostrarErroresImportacion(res.errores_validacion, res.errores_personalizados, null);
            $("#csv_preview").addClass("d-none");
            return;
        }

        mostrarErroresImportacion([], [], res.filas_insertadas);
        $("#csv_preview").addClass("d-none");
        $("#csv_archivo").val("");
        recargar();
    });
});

function mostrarAlertaCSV(mensaje, tipo) {
    $("#csv_alert")
        .removeClass("d-none alert-success alert-danger alert-warning")
        .addClass("alert-" + tipo)
        .html(mensaje);
}

function mostrarErroresImportacion(erroresValidacion = [], erroresPersonalizados = [], filasInsertadas) {
    if (erroresValidacion.length === 0 && erroresPersonalizados.length === 0) {
        mostrarAlertaCSV(`✅ Importación completada. Filas insertadas: <strong>${filasInsertadas}</strong>`, "success");
        return;
    }

    let html = "<strong>Se encontraron errores:</strong><ul class='mt-2 mb-0'>";
    erroresValidacion.forEach(function (err) {
        html += `<li><b>Fila ${err.row ?? "?"}:</b> ${err.errors ? err.errors.join(", ") : "Error de validación"}</li>`;
    });
    erroresPersonalizados.forEach(function (err) {
        html += `<li><b>Fila ${err.fila ?? "?"}:</b> ${err.mensaje}</li>`;
    });
    html += "</ul>";
    mostrarAlertaCSV(html, "danger");
}
