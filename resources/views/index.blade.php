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
    {{-- <script src="assetsLeaflet/leaflet.js"></script>
    <link rel="stylesheet" href="assetsLeaflet/leaflet.css"> --}}
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

    @yield('script')

</body>


</html>
