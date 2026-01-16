@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Estudiantes')
@section('nombreUniversidad', 'Universidad Pública de El Alto')
@section('titulo_header', 'Reporte de Estudiantes')

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
           
            /* Espaciado antes del inicio del reporte */
            padding-top: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ccc;
            /* Separador sutil con línea discontinua */
            padding-right: 15px;
            /* Pequeño margen para que no toque el borde del PDF */
        }
        .info-generador .gestion{
            position: absolute;
            top: 0;
            left: 0;
            
        }

         .info-generador .usuario{
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
    {{-- 🔹 Reporte por Carrera --}}
    @if ($tipo === 'carrera')
        @php
            $total_hombres = 0;
            $total_mujeres = 0;
            $total_general = 0;

            // Agrupar por carrera
            $porCarrera = $estadisticas->groupBy('carrera');
        @endphp

        <div class="titulo-seccion">Detalle por Carrera</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Carrera</th>
                    <th>Sede</th>
                    <th class="col-numerica">Hombres</th>
                    <th class="col-numerica">Mujeres</th>
                    <th class="col-numerica">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach ($porCarrera as $carrera => $sedes)
                    @php
                        $totalCarreraHombres = $sedes->sum('total_masculino');
                        $totalCarreraMujeres = $sedes->sum('total_femenino');
                        $totalCarrera = $sedes->sum('total');
                    @endphp

                    <tr class="total-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2" style="text-align: left;">{{ $carrera }} (Subtotal)</td>
                        <td class="col-numerica">{{ $totalCarreraHombres }}</td>
                        <td class="col-numerica">{{ $totalCarreraMujeres }}</td>
                        <td class="col-numerica">{{ $totalCarrera }}</td>
                    </tr>

                    @foreach ($sedes as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $s->sede }}</td>
                            <td class="col-numerica">{{ $s->total_masculino }}</td>
                            <td class="col-numerica">{{ $s->total_femenino }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
                        </tr>
                    @endforeach

                    @php
                        $total_hombres += $totalCarreraHombres;
                        $total_mujeres += $totalCarreraMujeres;
                        $total_general += $totalCarrera;
                    @endphp
                @endforeach

                <tr id="total-general-row">
                    <td colspan="3" style="text-align: left;">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_hombres }}</td>
                    <td class="col-numerica">{{ $total_mujeres }}</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif



    {{-- 🔹 Reporte por Sede --}}
    @if ($tipo === 'sede')
        @php
            $total_hombres = 0;
            $total_mujeres = 0;
            $total_general = 0;

            // Agrupar por sede
            $porSede = $estadisticas->groupBy('sede');
        @endphp

        <div class="titulo-seccion">Detalle por Sede</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sede</th>
                    <th>Carrera</th>
                    <th class="col-numerica">Hombres</th>
                    <th class="col-numerica">Mujeres</th>
                    <th class="col-numerica">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach ($porSede as $sede => $carreras)
                    @php
                        $totalSedeHombres = $carreras->sum('total_masculino');
                        $totalSedeMujeres = $carreras->sum('total_femenino');
                        $totalSede = $carreras->sum('total');
                    @endphp

                    <tr class="total-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2" style="text-align: left;">{{ $sede }} (Subtotal)</td>
                        <td class="col-numerica">{{ $totalSedeHombres }}</td>
                        <td class="col-numerica">{{ $totalSedeMujeres }}</td>
                        <td class="col-numerica">{{ $totalSede }}</td>
                    </tr>

                    @foreach ($carreras as $c)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $c->carrera }}</td>
                            <td class="col-numerica">{{ $c->total_masculino }}</td>
                            <td class="col-numerica">{{ $c->total_femenino }}</td>
                            <td class="col-numerica">{{ $c->total }}</td>
                        </tr>
                    @endforeach

                    @php
                        $total_hombres += $totalSedeHombres;
                        $total_mujeres += $totalSedeMujeres;
                        $total_general += $totalSede;
                    @endphp
                @endforeach

                <tr id="total-general-row">
                    <td colspan="3" style="text-align: left;">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_hombres }}</td>
                    <td class="col-numerica">{{ $total_mujeres }}</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif


    <div class="footer">
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
@endsection
