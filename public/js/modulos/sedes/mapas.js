import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

const map = L.map("map").setView([-16.5, -68.15], 13);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

const drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);
const drawControl = new L.Control.Draw({
    edit: { featureGroup: drawnItems },
    draw: {
        polygon: false,
        marker: false,
        polyline: false,
        rectangle: false,
        circle: false,
        circlemarker: false,
    },
});
map.addControl(drawControl);
let currentDrawer = null;
let geojsonFeatures = []; // Almacena todas las geometrías con nombre

// Mostrar POLIGONOS existentes en el mapa y en el panel
POLIGONOS.forEach(function (pol) {
    if (pol.geometry) {
        const layer = L.geoJSON(pol.geometry, {
            style: { color: "blue", fillOpacity: 0.3 },
            onEachFeature: function (feature, layer) {
                // Asignar el id de BD como propiedad al layer
                layer._idBD = pol.id;
            }
        }).bindTooltip(pol.ubicacion, { permanent: true, direction: "top" });
        layer.addTo(map);

        // Mostrar en la lista de info
        const li = document.createElement("p");
        li.textContent = ` ${pol.ubicacion}`;
        document.getElementById("info-poligonos").appendChild(li);

        // ➡️ Añadir cada capa de polígono al drawnItems
        layer.eachLayer(function (l) {
            drawnItems.addLayer(l);
        });
    }
});

// Mostrar PUNTOS existentes en el mapa y en el panel
PUNTOS.forEach(function (punto) {
    if (punto.geometry) {
        const layer = L.geoJSON(punto.geometry, {
            pointToLayer: function (feature, latlng) {
                return L.marker(latlng);
            },
            onEachFeature: function (feature, layer) {
                layer._idBD = punto.id;
            }
        }).bindTooltip(punto.ubicacion, {
            permanent: true,
            direction: "top",
        });
        layer.addTo(map);

        // Mostrar en la lista de info
        const li = document.createElement("p");
        li.textContent = `${punto.ubicacion}`;
        document.getElementById("info-puntos").appendChild(li);


        // ➡️ Añadir cada capa de punto al drawnItems
        layer.eachLayer(function (l) {
            drawnItems.addLayer(l);
        });
    }
});

// boton para agreagr los elementos al mapa
document.getElementById("activarDibujo").addEventListener("click", function () {
    const tipo = document.getElementById("tipo").value;
    const nombre = document.getElementById("nombre").value;

    if (!tipo) {
        mensajeAlerta(
            "Selecciona un tipo de geometría antes de activar el dibujo.",
            "error"
        );
        return;
    }
    if (!nombre) {
        mensajeAlerta("Ingrese el nombre de la ubicacion", "error");
        return;
    }

    if (currentDrawer) {
        currentDrawer.disable();
    }

    if (tipo === "poligono") {
        currentDrawer = new L.Draw.Polygon(map);
        currentDrawer.enable();
    } else if (tipo === "punto") {
        currentDrawer = new L.Draw.Marker(map);
        currentDrawer.enable();
    } else {
        currentDrawer = null;
    }
});

map.on("draw:created", function (e) {
    const layer = e.layer;
    drawnItems.addLayer(layer);

    const nombre = document.getElementById("nombre").value || "Sin nombre";

    const geojsonFeature = layer.toGeoJSON();
    geojsonFeature.properties = { nombre: nombre };

    geojsonFeatures.push(geojsonFeature);

    document.getElementById("geojson").value = JSON.stringify({
        type: "FeatureCollection",
        features: geojsonFeatures,
    });

    const miObjetoGeoJSON = {
        type: "FeatureCollection",
        features: geojsonFeatures,
    };

    console.log(JSON.stringify(miObjetoGeoJSON, null, 2)); // ✅ Para verlo ordenado en consola
    document.getElementById("geojson").value = JSON.stringify(miObjetoGeoJSON);

    const tipo = document.getElementById("tipo").value;

    const puntosList = document.getElementById("info-puntos");
    const poligonosList = document.getElementById("info-poligonos");
    const item = document.createElement("li");
    item.classList.add("list-group-item");

    if (tipo === "poligono") {
        const coords = layer
            .getLatLngs()[0]
            .map(
                (coord) => `[${coord.lat.toFixed(5)}, ${coord.lng.toFixed(5)}]`
            )
            .join(", ");
        item.textContent = `🟩 ${nombre}: ${layer.getLatLngs()[0].length
            } vértices`;
        poligonosList.appendChild(item);

        layer
            .bindTooltip(nombre, { permanent: true, direction: "center" })
            .openTooltip();

        if (currentDrawer) {
            currentDrawer.disable();
        }
        currentDrawer = null;
    }

    if (tipo === "punto") {
        const latlng = layer.getLatLng();
        item.textContent = `🟢 ${nombre}: [${latlng.lat.toFixed(
            5
        )}, ${latlng.lng.toFixed(5)}]`;
        puntosList.appendChild(item);

        layer
            .bindTooltip(nombre, { permanent: true, direction: "top" })
            .openTooltip();
    }
});


// actualizar el GeoJSON desde los elementos dibujados
function actualizarGeoJSONDesdeDrawnItems() {
    geojsonFeatures = [];

    drawnItems.eachLayer(function (layer) {
        const geojsonFeature = layer.toGeoJSON();

        // Recuperar el nombre desde el tooltip si deseas
        const tooltip = layer.getTooltip();
        let nombre = "Sin nombre";
        if (tooltip) {
            nombre = tooltip.getContent();
        }

        geojsonFeature.properties = { nombre: nombre };
        geojsonFeatures.push(geojsonFeature);
    });

    document.getElementById("geojson").value = JSON.stringify({
        type: "FeatureCollection",
        features: geojsonFeatures,
    });

    // También puedes actualizar los paneles de información
    actualizarPanelInformacion();
}

// actualizar la tabla de información registrada
function actualizarPanelInformacion() {
    const puntosList = document.getElementById("info-puntos");
    const poligonosList = document.getElementById("info-poligonos");

    puntosList.innerHTML = "";
    poligonosList.innerHTML = "";

    geojsonFeatures.forEach((feature) => {
        const item = document.createElement("li");
        item.classList.add("list-group-item");

        const nombre = feature.properties.nombre || "Sin nombre";

        if (feature.geometry.type === "Point") {
            const coords = feature.geometry.coordinates;
            item.textContent = `🟢 ${nombre}: [${coords[1].toFixed(
                5
            )}, ${coords[0].toFixed(5)}]`;
            puntosList.appendChild(item);
        }
        if (
            feature.geometry.type === "Polygon" ||
            feature.geometry.type === "MultiPolygon"
        ) {
            const coords = feature.geometry.coordinates[0];
            item.textContent = `🟩 ${nombre}: ${coords.length} vértices`;
            poligonosList.appendChild(item);
        }
    });
}


// gurdar ubicacionmes
$("#guardarUbicaciones").on("submit", async function (e) {
    e.preventDefault();

    // Recolectar datos del formulario
    const form = $(this);
    const nombre = form.find("input[name='nombre']").val();
    const idSede = form.find("input[name='idSede']").val() || null;

    // Validar que haya al menos un feature
    if (geojsonFeatures.length === 0) {
        mensajeAlerta(
            "Debes agregar al menos un punto o polígono antes de guardar.",
            "error"
        );
        return;
    }

    // Construir FeatureCollection limpio
    const geojson = {
        type: "FeatureCollection",
        features: geojsonFeatures,
    };

    // Enviar usando crud
    crud(
        "admin/guardarUbicaciones",
        "POST",
        null,
        {
            nombre: nombre,
            idSede: idSede,
            geojson: JSON.stringify(geojson),
        },
        function (error, response) {
            console.log(response);

            if (response.tipo === "errores") {
                mensajeAlerta(response.mensaje, "errores");
                return;
            }
            if (response.tipo !== "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }

            $('#nombre').val('');
            $('#tipo').val('');
            mensajeAlerta(response.mensaje, response.tipo);

            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    );
});


// Actualizar GeoJSON después de editar
map.on("draw:edited", function (e) {
    e.layers.eachLayer(function (layer) {
        const id = layer._idBD ?? null; // Recuperar id si existe
        const geojson = layer.toGeoJSON();
        
        // console.log("Eliminando capa con id:", id, geojson);
        crud("admin/actualizarUbicacion", "PUT", id, geojson, function (error, response) {

            if (error) {
                mensajeAlerta("Error eliminar ubicacion.", "error");
                console.error(error);
                return;
            }

            if (response.tipo === "exito") {
                mensajeAlerta(response.mensaje, "exito");
                // Recargar la página después de eliminar
                setTimeout(() => {
                    location.reload();
                }, 1500);

            } else {
                mensajeAlerta(response.mensaje, "error");
            }
        });

    });
});



// Actualizar GeoJSON después de eliminar
map.on('draw:deleted', function (e) {
    e.layers.eachLayer(function (layer) {
        const id = layer._idBD ?? null; // Recuperar id si existe
        const geojson = layer.toGeoJSON();
        // console.log("Eliminando capa con id:", id, geojson);
        crud("admin/eliminarUbicacion", "PUT", id, geojson, function (error, response) {

            if (error) {
                mensajeAlerta("Error eliminar ubicacion.", "error");
                console.error(error);
                return;
            }

            if (response.tipo === "exito") {
                mensajeAlerta(response.mensaje, "exito");
                // Recargar la página después de eliminar
                setTimeout(() => {
                    location.reload();
                }, 1500);

            } else {
                mensajeAlerta(response.mensaje, "error");
            }
        });

    });
});
var customIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // URL del icono
    iconSize: [38, 38],
    iconAnchor: [19, 38], // Punto de anclaje
    popupAnchor: [0, -38]
});


document.getElementById('formBuscarUbicacion').addEventListener('submit', function(e) {
    e.preventDefault();

    var lat = parseFloat(document.getElementById('latitud').value);
    var lng = parseFloat(document.getElementById('longitud').value);

    if (!isNaN(lat) && !isNaN(lng)) {
        // Mover mapa
        map.setView([lat, lng], 16);

         // Agregar marcador con tooltip permanente
        marker = L.marker([lat, lng], { icon: customIcon })
            .addTo(map)
            .bindTooltip("📍 Ubicación", { 
                permanent: true, 
                direction: "top", 
                offset: [0, -10] // lo levanta un poquito para que no se encime
            })
            .openTooltip(); 
    } else {
       mensajeAlerta("Por favor, ingrese coordenadas válidas.", "error");
    }
});