@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Estudiantes')
@section('nombreUniversidad', 'Universidad Pública de El Alto')
@section('titulo_header', 'Reporte de Estudiantes')

@section('contenido')
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2,
        h3 {
            text-align: center;
            margin: 5px 0;
            color: #1a237e;
        }

        .info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #e8eaf6;
            color: #1a237e;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover td {
            background-color: #e3f2fd;
        }

        .total-row {
            font-weight: bold;
            background-color: #c5cae9;
            color: #1a237e;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 11px;
            color: #555;
        }

        .titulo-seccion {
            background-color: #1a237e;
            color: white;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            text-align: left;
            margin-top: 20px;
        }
    </style>

    <h2>Universidad Pública de El Alto</h2>
    <h3>Reporte de Estudiantes — Gestión {{ $gestion }}</h3>
    <div class="info">
        Tipo de reporte: <strong>{{ ucfirst($tipo) }}</strong>
    </div>

    {{-- 🔹 Reporte por Carrera --}}
    @if ($tipo === 'carrera')
        @php
            $total_hombres = $estadisticas->sum('cantidad_hombres');
            $total_mujeres = $estadisticas->sum('cantidad_mujeres');
            $total_general = $estadisticas->sum('total');
        @endphp

        <div class="titulo-seccion">Detalle por Carrera</div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Carrera</th>
                    {{-- <th>Sedes</th> --}}
                    <th>Hombres</th>
                    <th>Mujeres</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estadisticas as $index => $e)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $e->carrera->nombre ?? 'Sin carrera' }}</td>
                        {{-- <td>
                            @foreach ($e->carrera->sedes as $s)
                                {{ $s->nombre }}<br>
                            @endforeach
                        </td> --}}
                        <td>{{ $e->cantidad_hombres ?? 0 }}</td>
                        <td>{{ $e->cantidad_mujeres ?? 0 }}</td>
                        <td>{{ $e->total ?? 0 }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTALES GENERALES</td>
                    <td>{{ $total_hombres }}</td>
                    <td>{{ $total_mujeres }}</td>
                    <td>{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif


    {{-- 🔹 Reporte por Sede --}}
    @if ($tipo === 'sede')
        @php
            $total_hombres = collect($resumenSedes)->sum('total_hombres');
            $total_mujeres = collect($resumenSedes)->sum('total_mujeres');
            $total_general = collect($resumenSedes)->sum('total_general');
        @endphp

        <div class="titulo-seccion">Detalle por Sede</div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sede</th>
                    <th>Hombres</th>
                    <th>Mujeres</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resumenSedes as $index => $s)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $s['sede'] }}</td>
                        <td>{{ $s['total_hombres'] }}</td>
                        <td>{{ $s['total_mujeres'] }}</td>
                        <td>{{ $s['total_general'] }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTALES GENERALES</td>
                    <td>{{ $total_hombres }}</td>
                    <td>{{ $total_mujeres }}</td>
                    <td>{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
@endsection
