<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('titulo', 'Reporte Universidad')</title>
    <style>
        /* Fuente principal */
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 50px 25px 50px 25px;
            color: #333;
        }

        /* Header con tabla (compatible Dompdf) */
        header {
            position: fixed;
            top: -10px;
            left: 0;
            right: 0;
            height: 100px;
            border-bottom: 3px solid #851a1a; 
            padding-bottom: 5px;
        }
        header .tabla_header {
            width: 100%;
            border-collapse: collapse;
        }
        header .tabla_header td {
            vertical-align: middle;
        }
        header .tabla_header img {
            max-height: 60px;
        }
        header .tabla_header .titulo {
            text-align: center;
        }
        header .tabla_header h1 {
            font-size: 20px;
            margin: 0;
            color: #851a1a;
        }
        header .tabla_header h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
            color: #333;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            border-top: 2px solid #851a1a;
            font-size: 12px;
            color: #555;
        }

        /* Contenido */
        .contenido {
            margin-top: 120px; /* espacio para header */
            margin-bottom: 60px; /* espacio para footer */
        }

        /* Número de página */
        .pagenum:before {
            content: counter(page);
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<header>
    <table class="tabla_header">
        <tr>            
            <!-- Logo izquierdo -->
            <td style="width: 20%; text-align: left;">
                <img src="{{ public_path('assets/upea_logo.webp') }}" alt="Logo UPEA" style="max-height: 90px">
            </td>

            <!-- Nombre Universidad centrado -->
            <td style="width: 60%; text-align: center;">
                <h1 style="color: #851a1a; font-family: Georgia, 'Times New Roman', Times, serif; font-size:30px">@yield('nombreUniversidad', 'Universidad Publica de El Alto')</h1>      
                <span style="color: #00539C">El Alto-La Paz-Bolivia</span>      
            </td>

            <!-- Logo derecho -->
            <td style="width: 20%; text-align: right;">
                <img src="{{ public_path('assets/disbedc_logo.webp') }}" alt="Logo DISBEDC" style="max-height: 100px">
            </td>
        </tr>
    </table>
    
</header>



<main class="contenido">
    @yield('contenido')
</main>


{{-- <footer>
    Página <span class="pagenum"></span>
</footer> --}}
</body>
</html>

