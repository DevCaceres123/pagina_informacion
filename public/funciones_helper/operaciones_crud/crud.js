export async function crud(
    url,
    metodo,
    idRegistro = null,
    datos = null,
    callback
) {
    let response;
    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        const headers = {
            "X-CSRF-TOKEN": csrfToken,
        };

        // Detectar si datos es FormData para evitar fijar Content-Type manualmente
        const esFormData = datos instanceof FormData;

        // PUT
        if (idRegistro != null && datos != null && metodo === "PUT") {
            if (!esFormData) {
                headers["Content-Type"] = "application/json";
            }
            response = await fetch(`/${url}/${idRegistro}`, {
                method: metodo,
                headers: headers,
                body: esFormData ? datos : JSON.stringify(datos),
            });
        }

        // POST
        if (datos != null && metodo === "POST") {
            if (!esFormData) {
                headers["Content-Type"] = "application/json";
            }
            response = await fetch(
                `/${url}${idRegistro != null ? `/${idRegistro}` : ""}`,
                {
                    method: metodo,
                    headers: headers,
                    body: esFormData ? datos : JSON.stringify(datos),
                }
            );
        }

        // GET con id
        if (idRegistro != null && datos == null) {
            headers["Content-Type"] = "application/json";
            response = await fetch(`/${url}/${idRegistro}`, {
                method: metodo,
                headers: headers,
            });
        }

        // GET index
        if (datos == null && idRegistro == null) {
            headers["Content-Type"] = "application/json";
            response = await fetch(`/${url}`, {
                method: metodo,
                headers: headers,
            });
        }

        if (!response.ok && response.status != 422) {
            throw new Error(`Ocurrió un error: ${response.status}`);
        }

        const respuestaParseada = await response.json();
        callback(null, respuestaParseada);
    } catch (error) {
        callback(error, null);
    }
}

export function crearRegistro(url, datos, callback) {
    $.ajax({
        url: url,
        method: "POST",
        data: datos,
        success: function (datos) {
            callback(null, datos);
        },
        error: function (error) {
            callback(error, null);
        },
    });
}

export function listarRegistros(callback) {
    $.ajax({
        url: "listar_nota",
        method: "POST",
        dataType: "json",
        success: function (datos) {
            callback(null, datos);
        },
        error: function (error) {
            callback(error, null);
        },
    });
}

export function obtenerDatosDeUnRegistro(id, ruta, callback) {
    $.ajax({
        url: ruta,
        type: "POST",
        data: { id_dato: id },
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        },
    });
}

export function actualizarRegistro(url, nuevosDatos, callback) {
    $.ajax({
        url: url,
        type: "POST",
        data: nuevosDatos,
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        },
    });
}

export function eliminarRegistro(id, ruta, callback) {
    $.ajax({
        url: ruta,
        type: "POST",
        data: { id_dato: id },
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        },
    });
}
