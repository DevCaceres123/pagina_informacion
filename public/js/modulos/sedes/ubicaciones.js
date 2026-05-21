const map = L.map("map").setView([-16.5, -68.15], 12);

var normal = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
});
var satelite = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    { maxZoom: 18 }
);
var etiquetas = L.tileLayer(
    'https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
    { maxZoom: 18 }
);
var sateliteConCalles = L.layerGroup([satelite, etiquetas]);

normal.addTo(map);
L.control.layers({ "Mapa 🗺️": normal, "Satélite 🛰️": sateliteConCalles }).addTo(map);

L.Control.geocoder({
    position: 'topleft',
    placeholder: 'Buscar lugar...',
    suggestMinLength: 3,
    suggestTimeout: 250
}).addTo(map);

var allBounds = null;
var sedeBounds = {};

// Puntos agrupados por sede_id para filtrar independientemente
var puntosGlobal = L.layerGroup();   // todos los puntos
var puntosPorSede = {};              // { sede_id: L.layerGroup }
var puntosVisible = false;
var puntosActivo = null;             // la capa de puntos actualmente en el mapa

UBICACIONES.forEach(function (u) {
    if (!u.geometry) return;

    var editUrl = SEDE_EDIT_URL + '/' + u.sede_id;

    var layer;

    if (u.tipo === 'Poligono') {
        var popupContent =
            '<div style="min-width:165px; line-height:1.7">' +
                '<strong class="d-block mb-1">' + u.nombre + '</strong>' +
                '<small style="color:#6c757d">' + u.sede_nombre.toUpperCase() + '</small><br>' +
                '<span style="display:inline-block;background:#0d6efd;color:#fff;font-size:11px;padding:1px 7px;border-radius:4px;margin:4px 0 8px">Polígono</span><br>' +
                '<a href="' + editUrl + '" ' +
                   'style="display:block;text-align:center;padding:4px 0;background:#0d6efd;color:#fff;border-radius:4px;text-decoration:none;font-size:13px">' +
                    '<i class="fas fa-edit"></i> Ver y Editar Mapa' +
                '</a>' +
            '</div>';

        layer = L.geoJSON(u.geometry, {
            style: { color: '#0d6efd', fillColor: '#0d6efd', fillOpacity: 0.2, weight: 2 }
        });
        layer.bindTooltip(u.nombre, { permanent: true, direction: 'top' });
        layer.bindPopup(popupContent);
        layer.addTo(map);

        try {
            var bounds = layer.getBounds();
            if (bounds.isValid()) {
                allBounds = allBounds
                    ? allBounds.extend(bounds)
                    : L.latLngBounds(bounds.getSouthWest(), bounds.getNorthEast());

                if (sedeBounds[u.sede_id]) {
                    sedeBounds[u.sede_id].extend(bounds);
                } else {
                    sedeBounds[u.sede_id] = L.latLngBounds(bounds.getSouthWest(), bounds.getNorthEast());
                }
            }
        } catch (e) {}

    } else {
        var popupPunto =
            '<div style="min-width:165px; line-height:1.7">' +
                '<strong class="d-block mb-1">' + u.nombre + '</strong>' +
                '<small style="color:#6c757d">' + u.sede_nombre.toUpperCase() + '</small><br>' +
                '<span style="display:inline-block;background:#198754;color:#fff;font-size:11px;padding:1px 7px;border-radius:4px;margin:4px 0 8px">Punto de abordaje</span><br>' +
                '<a href="' + editUrl + '" ' +
                   'style="display:block;text-align:center;padding:4px 0;background:#0d6efd;color:#fff;border-radius:4px;text-decoration:none;font-size:13px">' +
                    '<i class="fas fa-edit"></i> Ver y Editar Mapa' +
                '</a>' +
            '</div>';

        layer = L.geoJSON(u.geometry, {
            pointToLayer: function (feature, latlng) {
                return L.marker(latlng);
            }
        });
        layer.bindTooltip(
            '<i class="fas fa-bus"></i> <strong>' + u.nombre + '</strong><br><small>' + u.sede_nombre + '</small>',
            { direction: 'top', sticky: true }
        );
        layer.bindPopup(popupPunto);

        // Agregar al grupo global y al grupo de su sede
        puntosGlobal.addLayer(layer);

        if (!puntosPorSede[u.sede_id]) {
            puntosPorSede[u.sede_id] = L.layerGroup();
        }
        puntosPorSede[u.sede_id].addLayer(layer);
    }
});

if (allBounds && allBounds.isValid()) {
    map.fitBounds(allBounds, { padding: [30, 30] });
}

// Muestra los puntos de la sede seleccionada (o todos si no hay filtro)
function mostrarPuntos(sedeId) {
    if (puntosActivo) {
        puntosActivo.remove();
        puntosActivo = null;
    }
    if (!sedeId) {
        puntosActivo = puntosGlobal;
    } else if (puntosPorSede[sedeId]) {
        puntosActivo = puntosPorSede[sedeId];
    }
    if (puntosActivo) {
        puntosActivo.addTo(map);
    }
}

// Filtro por sede
document.getElementById('filtroSede').addEventListener('change', function () {
    var sedeId = this.value;

    if (!sedeId) {
        if (allBounds && allBounds.isValid()) {
            map.flyToBounds(allBounds, { padding: [30, 30] });
        }
    } else if (sedeBounds[sedeId] && sedeBounds[sedeId].isValid()) {
        map.flyToBounds(sedeBounds[sedeId], { padding: [60, 60] });
    }

    // Si los puntos están visibles, actualizar cuáles se muestran
    if (puntosVisible) {
        mostrarPuntos(sedeId);
    }
});

// Toggle puntos de abordaje
var btnToggle = document.getElementById('btnTogglePuntos');
btnToggle.addEventListener('click', function () {
    puntosVisible = !puntosVisible;
    var sedeId = document.getElementById('filtroSede').value;

    if (puntosVisible) {
        mostrarPuntos(sedeId);
        btnToggle.innerHTML = '<i class="fas fa-bus me-1"></i> Ocultar puntos de abordaje';
        btnToggle.style.background = 'rgba(25, 135, 84, 0.85)';
        btnToggle.style.borderColor = '#198754';
    } else {
        if (puntosActivo) {
            puntosActivo.remove();
            puntosActivo = null;
        }
        btnToggle.innerHTML = '<i class="fas fa-bus me-1"></i> Mostrar puntos de abordaje';
        btnToggle.style.background = 'rgba(30, 30, 30, 0.75)';
        btnToggle.style.borderColor = '#aaa';
    }
});
