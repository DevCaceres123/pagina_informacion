@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Administrativos')
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

    {{-- Reporte por Servicio --}}
    @if ($tipo === 'servicio')
        @php
            $total_general = 0;
            $porServicio = $estadisticas->groupBy('servicio');
            $index = 1;
        @endphp

        <div class="titulo-seccion">Detalle por Servicio</div>
        <table class="datos">
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
                    @php $totalServicio = $sedes->sum('total'); @endphp

                    <tr class="grupo-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2">{{ strtoupper($servicio) }}</td>
                        <td class="col-numerica">{{ $totalServicio }}</td>
                    </tr>

                    @foreach ($sedes as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ ucfirst($s->sede) }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
                        </tr>
                    @endforeach

                    @php $total_general += $totalServicio; @endphp
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
            $index = 1;
        @endphp

        <div class="titulo-seccion">Detalle por Sede</div>
        <table class="datos">
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
                    @php $totalSede = $servicios->sum('total'); @endphp

                    <tr class="grupo-row">
                        <td>{{ $index++ }}</td>
                        <td colspan="2">{{ strtoupper($sede) }}</td>
                        <td class="col-numerica">{{ $totalSede }}</td>
                    </tr>

                    @foreach ($servicios as $s)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ ucfirst($s->servicio) }}</td>
                            <td class="col-numerica">{{ $s->total }}</td>
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
