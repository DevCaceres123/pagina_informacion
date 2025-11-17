@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Administrativo')
@section('nombreUniversidad', 'Universidad Pública de El Alto')
@section('titulo_header', 'Reporte de Administrativo')

@section('contenido')
    <style>
        /* 🎨 Tipografía y Estilo Base */
        body {
            font-family: Arial, Helvetica, sans-serif;
            /* Tipografía más estándar y profesional */
            font-size: 10px;
            /* Reducimos ligeramente la fuente para más espacio */
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* 🏛️ Encabezados del Reporte */
        h2 {
            text-align: center;
            margin-bottom: 2px;
            color: #1a237e;
            /* Azul de la universidad */
            font-size: 18px;
            font-weight: bold;
        }

        h3 {
            text-align: center;
            margin-top: 2px;
            margin-bottom: 15px;
            color: #2c3e50;
            /* Gris oscuro para subtítulos */
            font-size: 14px;
            font-weight: normal;
        }



        /* 👤 Bloque de Generador */
        .info-generador {
            position: relative;
            width: 100%;
            height: 16px;
            font-size: 10px;
            color: #777;
            margin-top: 25px;
            margin-bottom: 15px;
            /* Espaciado antes del inicio del reporte */
            padding-top: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ccc;
            /* Separador sutil con línea discontinua */
            padding-right: 15px;
            /* Pequeño margen para que no toque el borde del PDF */
        }

        .info-generador .gestion {
            position: absolute;
            top: 0;
            left: 0;

        }

        .info-generador .usuario {
            position: absolute;
            top: 0;
            right: 0;
        }

        .info-generador strong {
            color: #333;
            font-weight: bold;
        }

        /* 📊 Tabla Principal */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        /* Eliminamos bordes de celda, usamos solo bordes horizontales */
        th,
        td {
            border: none;
            padding: 8px 10px;
            text-align: left;
            /* Alineación a la izquierda para mejor lectura, excepto números */
        }

        /* 📌 Encabezados de Columna */
        th {
            background-color: #2c3e50;
            /* Azul muy oscuro o gris corporativo */
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 3px solid #1a237e;
            /* Línea de color de la universidad */
            font-size: 10px;
            vertical-align: middle;
        }

        /* 📈 Filas de Datos */
        tbody tr:nth-child(even) {
            background-color: #f7f9fc;
            /* Rayado muy sutil */
        }

        tbody tr {
            border-bottom: 1px solid #eee;
            /* Divisores de fila suaves */
        }

        /* 🔢 Alineación de números y totales */
        .col-numerica {
            text-align: right;
        }

        /* 🌟 Filas de Totales (Subtotales y General) */
        .total-row {
            font-weight: bold;
            background-color: #e8eaf6 !important;
            /* Mantenemos un color claro para los totales */
            color: #1a237e;
            border-top: 2px solid #1a237e;
            /* Línea gruesa antes de los totales */
        }

        .total-row td {
            padding: 8px 10px;
        }

        /* 🛑 Fila de TOTAL GENERAL */
        #total-general-row {
            background-color: #1a237e !important;
            color: white;
            border-top: 4px solid #000;
            font-size: 12px;
        }

        /* 📘 Título de la Sección */
        .titulo-seccion {
            background-color: #3f51b5;
            /* Un azul ligeramente diferente para destacar */
            color: white;
            padding: 6px 10px;
            border-radius: 2px;
            font-weight: bold;
            text-align: left;
            margin-top: 25px;
            text-transform: uppercase;
        }

        /* 📝 Pie de Página */
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }
    </style>

    <div class="info-generador">
        <p class="text-muted gestion">Gestion:
            <strong>
                {{ $gestion ?? 'sin' }}
            </strong>
        </p>

        <p class="usuario">Documento generado por:
            <strong>{{ $usuarioGenerador['nombres'] ?? 'sin' }}
                {{ $usuarioGenerador['apellidos'] ?? 'datos' }}
            </strong>
        </p>
    </div>
    {{-- 🔹 Reporte por Servicio --}}
    @if ($tipo === 'servicio')
        @php
            $total_general = 0;

            // Agrupar por servicio
            $porServicio = $estadisticas->groupBy('servicio');
            $index = 1;
        @endphp

        <div class="titulo-seccion">Detalle por Servicio</div>
        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Servicio</th>
                    <th>Sede</th>
                    <th class="col-numerica">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porServicio as $servicio => $sedes)
                    @php
                        $totalServicio = $sedes->sum('total');
                    @endphp

                    {{-- SUBTOTAL POR SERVICIO --}}
                    <tr class="total-row" style="font-weight: bold; background-color: #f0f0f0;">
                        <td>{{ $index++ }}</td>
                        <td colspan="2" style="text-align: left;">{{ ucfirst($servicio) }} (Subtotal)</td>
                        <td class="col-numerica">{{ $totalServicio }}</td>
                    </tr>

                    {{-- LISTADO DE SEDES --}}
                    @foreach ($sedes as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $s->sede }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
                        </tr>
                    @endforeach

                    @php
                        $total_general += $totalServicio;
                    @endphp
                @endforeach

                {{-- TOTAL GENERAL --}}
                <tr style="font-weight: bold; background-color: #d0d0d0;">
                    <td colspan="3" style="text-align: left;">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif


    {{-- 🔹 Reporte por Sede --}}
    @if ($tipo === 'sede')
        @php
            $total_general = 0;

            // Agrupar por sede
            $porSede = $estadisticas->groupBy('sede');
            $index = 1;
        @endphp

        <div class="titulo-seccion">Detalle por Sede</div>
        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sede</th>
                    <th>Servicio</th>
                    <th class="col-numerica">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porSede as $sede => $servicios)
                    @php
                        $totalSede = $servicios->sum('total');
                    @endphp

                    {{-- SUBTOTAL POR SEDE --}}
                    <tr class="total-row" style="font-weight: bold; background-color: #f0f0f0;">
                        <td>{{ $index++ }}</td>
                        <td colspan="2" style="text-align: left;">{{ $sede }} (Subtotal)</td>
                        <td class="col-numerica">{{ $totalSede }}</td>
                    </tr>

                    {{-- LISTADO DE SERVICIOS --}}
                    @foreach ($servicios as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ ucfirst($s->servicio) }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
                        </tr>
                    @endforeach

                    @php
                        $total_general += $totalSede;
                    @endphp
                @endforeach

                {{-- TOTAL GENERAL --}}
                <tr style="font-weight: bold; background-color: #d0d0d0;">
                    <td colspan="3" style="text-align: left;">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif


    <div class="footer">
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
@endsection
