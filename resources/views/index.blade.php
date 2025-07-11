<!DOCTYPE html>
<html lang="es" dir="ltr">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   <title>DISBEDC | @yield('titulo')</title>

    @include('plantilla_web/iconos')
    <script src="{{ asset('pagina_template/assets/lib/overlayscrollbars/OverlayScrollbars.min.js') }}"></script>

    @include('plantilla_web/estilos')

    <!-- ===============================================-->
    <!--    Librerias leaflet-->
    <!-- ===============================================-->
    <script src="assetsLeaflet/leaflet.js"></script>
    <link rel="stylesheet" href="assetsLeaflet/leaflet.css">
</head>

<!-- ===============================================-->
<!--    TAMAÑO DEL MAPA-->
<!-- ===============================================-->
<style>
    #map {
        width: 100%;
        height: 400px;
    }
</style>


<body>


    @include('plantilla_web/navegacion')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="preloader" id="preloader">
            <div class="loader">
                <div class="line-scale">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        @yield('contenido')

    </main>
    <!-- Final redes sociales -->

  


@include('plantilla_web/paginas/pie_de_pagina')

    @include('plantilla_web.script');
    {{-- <script>
        var map = L.map('map').setView([-17, -65], 13);

        //---------------FONDO BASE (TIPO DE CAPA O MAPA)--------------
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        //var OpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        //maxZoom: 17,
        //attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
        //}).addTo(map);
        let puntosPoligono = [];

        sedes.forEach(element => {
            L.marker([element.latitud_punto, element.longitud_punto]).addTo(map)
                .bindPopup(element.nombre)
                .openPopup();
            puntosPoligono.push(element.latitud_poligono + ',' + element.longitud_poligono);

        });
        //--------------VISUALIZACION DE PUNTO LINEA Y POLIGONO-------------
        console.log(puntosPoligono);
        var geojsonData = {
            "type": "FeatureCollection",
            "features": [{
                    "type": "Feature",
                    "properties": {
                        "nombre": "SEDE VIACHA UPEA",
                        "direccion": "AV.ALFREDO QUISBERT CALLE SUCRE"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [-68.293649703835868, -16.650385869921386],
                                [-68.293714405472571, -16.65047368731102],
                                [-68.29460135707572, -16.650094005954905],
                                [-68.294329071021252, -16.649776312813412],
                                [-68.293649703835868, -16.650385869921386]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "properties": {
                        "nombre": "Ruta de acceso"
                    },
                    "geometry": {
                        "type": "LineString",
                        "coordinates": [
                            [-68.292763842812079, -16.644991586513367],
                            [-68.295732180975605, -16.647829720184482],
                            [-68.295505150244807, -16.648018302959823],
                            [-68.295872221319769, -16.648397170413777],
                            [-68.294325912250429, -16.649732544793348],
                            [-68.294485508369988, -16.64990923485372],
                            [-68.294460682306934, -16.649928772686934]
                        ]
                    }
                }
            ]
        };

        L.geoJSON(geojsonData).addTo(map);
    </script> --}}
</body>


</html>
