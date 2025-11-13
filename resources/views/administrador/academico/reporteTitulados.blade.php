@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Estudiantes')
@section('nombreUniversidad', 'Universidad Pública de El Alto')
@section('titulo_header', 'Reporte de Titulados')


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

        .subtotal-row {
            font-weight: bold;
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
        .total-general-row {
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
        <p class="text-muted gestion">Fecha de colación:
            <strong>
                {{ \Carbon\Carbon::parse($gestion)->translatedFormat('d \d\e F Y') }}
            </strong>
        </p>

        <p class="usuario">Documento generado por:
            <strong>{{ $usuarioGenerador['nombres'] ?? 'sin' }}
                {{ $usuarioGenerador['apellidos'] ?? 'datos' }}
            </strong>
        </p>
    </div>


    @if ($tipo == 'sede')
        {{-- ================== DETALLE POR SEDE ================== --}}
        <div class="titulo-seccion">Detalle por sede</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>SEDE</th>
                    <th>CARRERA</th>
                    <th>TÉCNICO MEDIO</th>
                    <th>TÉCNICO SUPERIOR</th>
                    <th>LICENCIATURA</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $num = 1;
                    $totalGeneral = ['tecnico medio' => 0, 'tecnico superior' => 0, 'licenciatura' => 0, 'total' => 0];
                    $agrupadoPorSede = $estadisticas->groupBy('sede');
                @endphp

                @foreach ($agrupadoPorSede as $sede => $registros)
                    @php
                        $subtotal = ['tecnico medio' => 0, 'tecnico superior' => 0, 'licenciatura' => 0, 'total' => 0];
                    @endphp

                    <tr class="total-row">
                        <td>{{ $num++ }}</td>
                        <td class="text-left" colspan="6">{{ strtoupper($sede) }}</td>
                    </tr>

                    @foreach ($registros->groupBy('carrera') as $carrera => $itemsCarrera)
                        @php
                            $tecnicoMedio = $itemsCarrera->where('grado_academico', 'tecnico medio')->sum('total');
                            $tecnicoSuperior = $itemsCarrera
                                ->where('grado_academico', 'tecnico superior')
                                ->sum('total');
                            $licenciatura = $itemsCarrera->where('grado_academico', 'licenciatura')->sum('total');
                            $totalCarrera = $tecnicoMedio + $tecnicoSuperior + $licenciatura;

                            // Sumar a subtotal
                            $subtotal['tecnico medio'] += $tecnicoMedio;
                            $subtotal['tecnico superior'] += $tecnicoSuperior;
                            $subtotal['licenciatura'] += $licenciatura;
                            $subtotal['total'] += $totalCarrera;
                        @endphp

                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-left">{{ strtolower($carrera) }}</td>
                            <td>{{ $tecnicoMedio }}</td>
                            <td>{{ $tecnicoSuperior }}</td>
                            <td>{{ $licenciatura }}</td>
                            <td>{{ $totalCarrera }}</td>
                        </tr>
                    @endforeach

                    <tr class="subtotal-row">
                        <td colspan="3" class="text-right">Subtotal {{ strtolower($sede) }}</td>
                        <td>{{ $subtotal['tecnico medio'] }}</td>
                        <td>{{ $subtotal['tecnico superior'] }}</td>
                        <td>{{ $subtotal['licenciatura'] }}</td>
                        <td>{{ $subtotal['total'] }}</td>
                    </tr>

                    @php
                        $totalGeneral['tecnico medio'] += $subtotal['tecnico medio'];
                        $totalGeneral['tecnico superior'] += $subtotal['tecnico superior'];
                        $totalGeneral['licenciatura'] += $subtotal['licenciatura'];
                        $totalGeneral['total'] += $subtotal['total'];
                    @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    <td>{{ $totalGeneral['tecnico medio'] }}</td>
                    <td>{{ $totalGeneral['tecnico superior'] }}</td>
                    <td>{{ $totalGeneral['licenciatura'] }}</td>
                    <td>{{ $totalGeneral['total'] }}</td>
                </tr>
            </tbody>
        </table>
    @elseif ($tipo == 'carrera')
        {{-- ================== DETALLE POR CARRERA ================== --}}
        <div class="titulo-seccion">Detalle por carrera</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>CARRERA</th>
                    <th>SEDE</th>
                    <th>TÉCNICO MEDIO</th>
                    <th>TÉCNICO SUPERIOR</th>
                    <th>LICENCIATURA</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $num = 1;
                    $totalGeneral = ['tecnico medio' => 0, 'tecnico superior' => 0, 'licenciatura' => 0, 'total' => 0];
                    $agrupadoPorCarrera = $estadisticas->groupBy('carrera');
                @endphp

                @foreach ($agrupadoPorCarrera as $carrera => $registros)
                    @php
                        $subtotal = ['tecnico medio' => 0, 'tecnico superior' => 0, 'licenciatura' => 0, 'total' => 0];
                    @endphp

                    <tr class="total-row">
                        <td>{{ $num++ }}</td>
                        <td class="text-left" colspan="6">{{ strtoupper($carrera) }}</td>
                    </tr>

                    @foreach ($registros->groupBy('sede') as $sede => $itemsSede)
                        @php
                            $tecnicoMedio = $itemsSede->where('grado_academico', 'tecnico medio')->sum('total');
                            $tecnicoSuperior = $itemsSede->where('grado_academico', 'tecnico superior')->sum('total');
                            $licenciatura = $itemsSede->where('grado_academico', 'licenciatura')->sum('total');
                            $totalSede = $tecnicoMedio + $tecnicoSuperior + $licenciatura;

                            $subtotal['tecnico medio'] += $tecnicoMedio;
                            $subtotal['tecnico superior'] += $tecnicoSuperior;
                            $subtotal['licenciatura'] += $licenciatura;
                            $subtotal['total'] += $totalSede;
                        @endphp

                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-left">{{ strtolower($sede) }}</td>
                            <td>{{ $tecnicoMedio }}</td>
                            <td>{{ $tecnicoSuperior }}</td>
                            <td>{{ $licenciatura }}</td>
                            <td>{{ $totalSede }}</td>
                        </tr>
                    @endforeach

                    <tr class="subtotal-row">
                        <td colspan="3" class="text-right">Subtotal {{ strtolower($carrera) }}</td>
                        <td>{{ $subtotal['tecnico medio'] }}</td>
                        <td>{{ $subtotal['tecnico superior'] }}</td>
                        <td>{{ $subtotal['licenciatura'] }}</td>
                        <td>{{ $subtotal['total'] }}</td>
                    </tr>

                    @php
                        $totalGeneral['tecnico medio'] += $subtotal['tecnico medio'];
                        $totalGeneral['tecnico superior'] += $subtotal['tecnico superior'];
                        $totalGeneral['licenciatura'] += $subtotal['licenciatura'];
                        $totalGeneral['total'] += $subtotal['total'];
                    @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    <td>{{ $totalGeneral['tecnico medio'] }}</td>
                    <td>{{ $totalGeneral['tecnico superior'] }}</td>
                    <td>{{ $totalGeneral['licenciatura'] }}</td>
                    <td>{{ $totalGeneral['total'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif



    <div class="footer">
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
@endsection
