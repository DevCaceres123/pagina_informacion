<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>@yield('titulo', 'Reporte UPEA')</title>

    <style>
        /* ===== RESET Y FUENTES ===== */
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            margin: 0;
            color: #2c3e50;
            /* Un gris oscuro es más elegante y legible que el negro puro */
            font-size: 12px;
        }

        /* ===== CONFIGURACIÓN PDF ===== */
        @page {
            margin: 150px 40px 70px 40px;
            /* Márgenes ligeramente más amplios para respirar */
        }

        /* ===== HEADER ===== */
        header {
            position: fixed;
            top: -150px;
            left: 0;
            right: 0;
            height: 140px;
            /* Evita que el contenido del body se superponga */
            background-color: white;
        }

        /* Líneas decorativas institucionales */
        .linea-superior {
            height: 5px;
            background-color: #851a1a;
        }

        .linea-inferior {
            height: 1px;
            background-color: #851a1a;
            margin-top: 10px;
        }

        /* Tabla del Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .header-left {
            width: 20%;
            text-align: left;
            /* Asegura que el logo pegue a la izquierda */
            vertical-align: middle;
        }

        .header-center {
            width: 60%;
            text-align: center;
            vertical-align: middle;
        }

        .header-right {
            width: 20%;
            text-align: right;
            /* Asegura que el logo pegue a la derecha */
            vertical-align: middle;
        }

        /* Logos */
        .logo {
            max-height: 70px;
        }

        .logo-right {
            max-height: 80px;
        }

        /* Textos del Header */
        .nombre-institucion {
            font-size: 18px;
            font-weight: bold;
            color: #851a1a;
            font-family: Georgia, 'Times New Roman', serif;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .unidad {
            font-size: 11px;
            margin-top: 6px;
            color: #34495e;
            font-weight: bold;
        }

        .ubicacion {
            font-size: 10px;
            margin-top: 4px;
            color: #7f8c8d;
        }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .contenido {
            margin-top: 10px;
        }

        /* Título del Documento */
        .titulo-documento {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        /* ===== TABLAS DE DATOS (REPORTES) ===== */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabla-datos thead th {
            background-color: #851a1a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px;
            text-align: left;
            border: 1px solid #851a1a;
        }

        .tabla-datos tbody td {
            font-size: 11px;
            padding: 8px;
            border: 1px solid #bdc3c7;
            color: #333;
        }

        /* Filas alternas estilo "zebra" */
        .tabla-datos tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Evita que una fila se corte entre dos páginas */
        .tabla-datos tr {
            page-break-inside: avoid;
        }

        /* ===== FOOTER ===== */
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #851a1a;
            font-size: 10px;
            color: #7f8c8d;
            padding-top: 5px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
        }

        .footer-left {
            text-align: left;
            width: 50%;
        }

        .footer-right {
            text-align: right;
            width: 50%;
        }

        .pagenum:before {
            content: counter(page);
        }

        .pagecount:before {
            content: counter(pages);
        }
    </style>
</head>

<body>

    <header>
        <div class="linea-superior"></div>

        <table class="header-table">
            <tr>
                <td class="header-left">
                    <img class="logo" src="{{ public_path('assets/upea_logo.webp') }}">
                </td>
                <td class="header-center">
                    <div class="nombre-institucion">
                        @yield('nombreUniversidad', 'UNIVERSIDAD PÚBLICA DE EL ALTO')
                    </div>
                    <div class="unidad">
                        DIRECCIÓN DE INTERACCIÓN SOCIAL, BIENESTAR ESTUDIANTIL,<br>
                        DEPORTES Y CULTURA
                    </div>
                    <div class="ubicacion">
                        El Alto - La Paz - Bolivia
                    </div>
                </td>
                <td class="header-right">
                    <img class="logo logo-right" src="{{ public_path('assets/disbedc_logo.webp') }}">
                </td>
            </tr>
        </table>

        <div class="linea-inferior"></div>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Generado el: {{ date('d/m/Y H:i') }}<br>
                    Sistema de Reportes DISBEDC
                </td>
                <td class="footer-right">
                    Página <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </footer>

    <main class="contenido">
        @hasSection('contenido')
            @yield('contenido')
        @else
            <p>No hay datos para mostrar en este reporte.</p>
        @endif
    </main>

</body>

</html>