@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Titulados')
@section('nombreUniversidad', 'Universidad Pública de El Alto')

@section('contenido')

    @include('administrador.reporte.estilosAcademico')

    {{-- Barra de fecha de colación y generador --}}
    <table class="info-generador">
        <tr>
            <td>Fecha de colación:
                <strong>{{ \Carbon\Carbon::parse($gestion)->translatedFormat('d \d\e F Y') }}</strong>
            </td>
            <td class="info-derecha">Documento generado por:
                <strong>{{ $usuarioGenerador['nombres'] ?? '' }} {{ $usuarioGenerador['apellidos'] ?? '' }}</strong>
            </td>
        </tr>
    </table>

    {{-- Detalle por Sede --}}
    @if ($tipo == 'sede')
        <div class="titulo-seccion">Detalle por Sede</div>
        <table class="datos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sede</th>
                    <th>Carrera</th>
                    @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Técnico Medio</th>
                    @endif
                    @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Técnico Superior</th>
                    @endif
                    @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Licenciatura</th>
                    @endif
                    <th class="col-numerica">Total</th>
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
                        // Pre-calcular totales del grupo para mostrarlos en la fila cabecera
                        $stm  = in_array('tecnico medio', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'tecnico medio')->sum('total') : 0;
                        $sts  = in_array('tecnico superior', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'tecnico superior')->sum('total') : 0;
                        $slic = in_array('licenciatura', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'licenciatura')->sum('total') : 0;
                        $stotal = $stm + $sts + $slic;
                    @endphp

                    {{-- Fila de grupo con totales incluidos --}}
                    <tr class="grupo-row">
                        <td>{{ $num++ }}</td>
                        <td colspan="2">{{ strtoupper($sede) }}</td>
                        @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $stm }}</td>
                        @endif
                        @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $sts }}</td>
                        @endif
                        @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $slic }}</td>
                        @endif
                        <td class="col-numerica">{{ $stotal }}</td>
                    </tr>

                    {{-- Detalle por carrera --}}
                    @foreach ($registros->groupBy('carrera') as $carrera => $itemsCarrera)
                        @php
                            $tm  = in_array('tecnico medio', $gradosSeleccionadosNombres)
                                       ? $itemsCarrera->where('grado_academico', 'tecnico medio')->sum('total') : 0;
                            $ts  = in_array('tecnico superior', $gradosSeleccionadosNombres)
                                       ? $itemsCarrera->where('grado_academico', 'tecnico superior')->sum('total') : 0;
                            $lic = in_array('licenciatura', $gradosSeleccionadosNombres)
                                       ? $itemsCarrera->where('grado_academico', 'licenciatura')->sum('total') : 0;
                            $tot = $tm + $ts + $lic;
                        @endphp
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ strtolower($carrera) }}</td>
                            @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $tm }}</td>
                            @endif
                            @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $ts }}</td>
                            @endif
                            @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $lic }}</td>
                            @endif
                            <td class="col-numerica">{{ $tot }}</td>
                        </tr>
                    @endforeach

                    @php
                        $totalGeneral['tecnico medio']    += $stm;
                        $totalGeneral['tecnico superior'] += $sts;
                        $totalGeneral['licenciatura']     += $slic;
                        $totalGeneral['total']            += $stotal;
                    @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['tecnico medio'] }}</td>
                    @endif
                    @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['tecnico superior'] }}</td>
                    @endif
                    @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['licenciatura'] }}</td>
                    @endif
                    <td class="col-numerica">{{ $totalGeneral['total'] }}</td>
                </tr>
            </tbody>
        </table>

    {{-- Detalle por Carrera --}}
    @elseif ($tipo == 'carrera')
        <div class="titulo-seccion">Detalle por Carrera</div>
        <table class="datos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Carrera</th>
                    <th>Sede</th>
                    @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Técnico Medio</th>
                    @endif
                    @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Técnico Superior</th>
                    @endif
                    @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                        <th class="col-numerica">Licenciatura</th>
                    @endif
                    <th class="col-numerica">Total</th>
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
                        // Pre-calcular totales del grupo
                        $stm  = in_array('tecnico medio', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'tecnico medio')->sum('total') : 0;
                        $sts  = in_array('tecnico superior', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'tecnico superior')->sum('total') : 0;
                        $slic = in_array('licenciatura', $gradosSeleccionadosNombres)
                                    ? $registros->where('grado_academico', 'licenciatura')->sum('total') : 0;
                        $stotal = $stm + $sts + $slic;
                    @endphp

                    {{-- Fila de grupo con totales incluidos --}}
                    <tr class="grupo-row">
                        <td>{{ $num++ }}</td>
                        <td colspan="2">{{ strtoupper($carrera) }}</td>
                        @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $stm }}</td>
                        @endif
                        @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $sts }}</td>
                        @endif
                        @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                            <td class="col-numerica">{{ $slic }}</td>
                        @endif
                        <td class="col-numerica">{{ $stotal }}</td>
                    </tr>

                    {{-- Detalle por sede --}}
                    @foreach ($registros->groupBy('sede') as $sede => $itemsSede)
                        @php
                            $tm  = in_array('tecnico medio', $gradosSeleccionadosNombres)
                                       ? $itemsSede->where('grado_academico', 'tecnico medio')->sum('total') : 0;
                            $ts  = in_array('tecnico superior', $gradosSeleccionadosNombres)
                                       ? $itemsSede->where('grado_academico', 'tecnico superior')->sum('total') : 0;
                            $lic = in_array('licenciatura', $gradosSeleccionadosNombres)
                                       ? $itemsSede->where('grado_academico', 'licenciatura')->sum('total') : 0;
                            $tot = $tm + $ts + $lic;
                        @endphp
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ strtolower($sede) }}</td>
                            @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $tm }}</td>
                            @endif
                            @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $ts }}</td>
                            @endif
                            @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                                <td class="col-numerica">{{ $lic }}</td>
                            @endif
                            <td class="col-numerica">{{ $tot }}</td>
                        </tr>
                    @endforeach

                    @php
                        $totalGeneral['tecnico medio']    += $stm;
                        $totalGeneral['tecnico superior'] += $sts;
                        $totalGeneral['licenciatura']     += $slic;
                        $totalGeneral['total']            += $stotal;
                    @endphp
                @endforeach

                <tr class="total-general-row">
                    <td colspan="3">TOTALES GENERALES</td>
                    @if (in_array('tecnico medio', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['tecnico medio'] }}</td>
                    @endif
                    @if (in_array('tecnico superior', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['tecnico superior'] }}</td>
                    @endif
                    @if (in_array('licenciatura', $gradosSeleccionadosNombres))
                        <td class="col-numerica">{{ $totalGeneral['licenciatura'] }}</td>
                    @endif
                    <td class="col-numerica">{{ $totalGeneral['total'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif

@endsection
