@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Docentes')
@section('nombreUniversidad', 'Universidad Pública de El Alto')

@section('contenido')

    @include('administrador.reporte.estilosAcademico')

    {{-- Barra de gestión y generador --}}
    <table class="info-generador">
        <tr>
            <td>Gestión: <strong>{{ $gestion ?? '—' }}</strong></td>
            <td class="info-derecha">Documento generado por:
                <strong>{{ $usuarioGenerador['nombres'] ?? '' }} {{ $usuarioGenerador['apellidos'] ?? '' }}</strong>
            </td>
        </tr>
    </table>

    {{-- Reporte por Carrera --}}
    @if ($tipo === 'carrera')
        @php
            $total_general = 0;
            $porCarrera = $estadisticas->groupBy('carrera');
        @endphp

        <div class="titulo-seccion">Detalle por Carrera</div>
        <table class="datos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Carrera</th>
                    <th>Sede</th>
                    <th class="col-numerica">Total Docentes</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach ($porCarrera as $carrera => $sedes)
                    @php $totalCarrera = $sedes->sum('total'); @endphp

                    <tr class="grupo-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2">{{ strtoupper($carrera) }}</td>
                        <td class="col-numerica">{{ $totalCarrera }}</td>
                    </tr>

                    @foreach ($sedes as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $s->sede }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
                        </tr>
                    @endforeach

                    @php $total_general += $totalCarrera; @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Reporte por Sede --}}
    @if ($tipo === 'sede')
        @php
            $total_general = 0;
            $porSede = $estadisticas->groupBy('sede');
        @endphp

        <div class="titulo-seccion">Detalle por Sede</div>
        <table class="datos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sede</th>
                    <th>Carrera</th>
                    <th class="col-numerica">Total Docentes</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach ($porSede as $sede => $carreras)
                    @php $totalSede = $carreras->sum('total'); @endphp

                    <tr class="grupo-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2">{{ strtoupper($sede) }}</td>
                        <td class="col-numerica">{{ $totalSede }}</td>
                    </tr>

                    @foreach ($carreras as $c)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $c->carrera }}</td>
                            <td class="col-numerica">{{ $c->total }}</td>
                        </tr>
                    @endforeach

                    @php $total_general += $totalSede; @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    <td class="col-numerica">{{ $total_general }}</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
