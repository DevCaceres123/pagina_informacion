@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte General de Estudiantes')

@section('contenido')

<style>
    .titulo-documento {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        color: #2c3e50;
        border-bottom: 2px solid #851a1a;
        padding-bottom: 8px;
        margin-bottom: 16px;
        letter-spacing: 1px;
    }

    .seccion-titulo {
        background-color: #851a1a;
        color: #ffffff;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 10px;
        margin-top: 14px;
        margin-bottom: 0;
    }

    .tabla-filtros {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 0;
    }

    .tabla-filtros td {
        padding: 5px 10px;
        border: 1px solid #ddd;
        color: #2c3e50;
    }

    .tabla-filtros .celda-label {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
        width: 20%;
        white-space: nowrap;
    }

    /* Tabla listado */
    .tabla-lista {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 0;
    }

    .tabla-lista th {
        background-color: #851a1a;
        color: #fff;
        padding: 6px 8px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        border: 1px solid #6b1414;
        text-align: left;
    }

    .tabla-lista td {
        padding: 5px 8px;
        border: 1px solid #ddd;
        color: #2c3e50;
        vertical-align: middle;
    }

    .tabla-lista tbody tr:nth-child(even) td {
        background-color: #fdf5f5;
    }

    .tabla-lista tr { page-break-inside: avoid; }

    .total-row td {
        background-color: #851a1a;
        color: #fff;
        font-weight: bold;
        font-size: 10px;
        padding: 6px 8px;
        border: 1px solid #6b1414;
    }

    /* Tabla totales */
    .tabla-totales {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 0;
    }

    .tabla-totales th {
        background-color: #851a1a;
        color: #fff;
        padding: 6px 10px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        border: 1px solid #6b1414;
        text-align: center;
    }

    .tabla-totales th.th-left { text-align: left; }

    .tabla-totales td {
        padding: 5px 10px;
        border: 1px solid #ddd;
        color: #2c3e50;
        vertical-align: middle;
    }

    .tabla-totales .col-num { text-align: center; font-weight: bold; }

    .grupo-row td {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
    }

    .sub-row td {
        padding-left: 24px;
        color: #34495e;
    }

    .total-general-row td {
        background-color: #851a1a;
        color: #fff;
        font-weight: bold;
        font-size: 10px;
        padding: 6px 10px;
        border: 1px solid #6b1414;
        text-align: center;
    }

    .total-general-row td:first-child { text-align: left; }

    .masculino-col { color: #1a5276; }
    .femenino-col  { color: #7b241c; }

    .sin-datos {
        text-align: center;
        color: #999;
        font-style: italic;
        padding: 20px;
        font-size: 11px;
    }
</style>

<div class="titulo-documento">Reporte General de Estudiantes</div>

{{-- ===== FILTROS APLICADOS ===== --}}
<div class="seccion-titulo">Filtros Aplicados</div>
<table class="tabla-filtros">
    <tr>
        <td class="celda-label">Sede(s)</td>
        <td>{{ $filtros['sedes'] }}</td>
        <td class="celda-label">Carrera(s)</td>
        <td>{{ $filtros['carreras'] }}</td>
    </tr>
    <tr>
        <td class="celda-label">Género</td>
        <td>{{ $filtros['genero'] }}</td>
        <td class="celda-label">Gestión</td>
        <td>{{ $filtros['gestion'] }}</td>
    </tr>
</table>

{{-- ===== AVISO DE LÍMITE ===== --}}
@if($limitado)
<div style="background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:6px 10px;font-size:10px;margin-top:10px;border-radius:4px;">
    <strong>&#9888; Aviso:</strong> Se encontraron {{ $totalSinLimite }} estudiantes. Este reporte muestra los primeros 300.
    Aplique filtros más específicos para obtener un resultado completo.
</div>
@endif

{{-- ===== LISTADO DETALLADO ===== --}}
@if($vista === 'listado')

<div class="seccion-titulo">Listado de Estudiantes</div>

@if($estudiantes->isNotEmpty())
    <table class="tabla-lista">
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:22%">Nombre Completo</th>
                <th style="width:10%">Matrícula</th>
                <th style="width:7%">Género</th>
                <th style="width:6%">Tipo Doc.</th>
                <th style="width:9%">N° Documento</th>
                <th style="width:13%">Sede</th>
                <th style="width:23%">Carrera</th>
                <th style="width:7%">Gestión</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantes as $i => $est)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td style="text-transform:uppercase">{{ $est->nombre_completo }}</td>
                <td>{{ $est->matricula }}</td>
                <td style="text-transform:capitalize">{{ $est->genero }}</td>
                <td>{{ $est->tipo_documento }}</td>
                <td style="text-transform:uppercase">{{ $est->numero_documento }}</td>
                <td style="text-transform:uppercase">{{ $est->sede->nombre ?? '—' }}</td>
                <td style="text-transform:uppercase">{{ $est->carrera->nombre ?? '—' }}</td>
                <td style="text-align:center">{{ $est->gestion }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" style="text-align:right">Total de estudiantes:</td>
                <td style="text-align:center">{{ $estudiantes->count() }}</td>
            </tr>
        </tfoot>
    </table>
@else
    <div class="sin-datos">No se encontraron estudiantes con los filtros seleccionados.</div>
@endif

{{-- ===== SOLO TOTALES ===== --}}
@elseif($vista === 'totales')

@php
    $labelPrincipal   = $agruparPor === 'carrera' ? 'Carrera'  : 'Sede';
    $labelSecundario  = $agruparPor === 'carrera' ? 'Sede'     : 'Carrera';
    $groupKey         = $agruparPor === 'carrera' ? 'carrera_nombre' : 'sede_nombre';
    $subKey           = $agruparPor === 'carrera' ? 'sede_nombre'    : 'carrera_nombre';
    $grupos           = $totales->groupBy($groupKey);
    $totalMascGlobal  = 0;
    $totalFemGlobal   = 0;
    $totalGlobal      = 0;
@endphp

<div class="seccion-titulo">Totales por {{ $labelPrincipal }}</div>

@if($grupos->isNotEmpty())
<table class="tabla-totales">
    <thead>
        <tr>
            <th class="th-left" style="width:5%">#</th>
            <th class="th-left" style="width:28%">{{ $labelPrincipal }}</th>
            <th class="th-left" style="width:27%">{{ $labelSecundario }}</th>
            <th style="width:13%">Masculino</th>
            <th style="width:13%">Femenino</th>
            <th style="width:14%">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $idx = 1; @endphp
        @foreach($grupos as $grupoNombre => $subItems)
            @php
                $grupoMasc  = $subItems->sum('masculino');
                $grupoFem   = $subItems->sum('femenino');
                $grupoTotal = $subItems->sum('total');
                $totalMascGlobal += $grupoMasc;
                $totalFemGlobal  += $grupoFem;
                $totalGlobal     += $grupoTotal;
            @endphp
            <tr class="grupo-row">
                <td>{{ $idx++ }}</td>
                <td colspan="2" style="text-transform:uppercase">{{ $grupoNombre }}</td>
                <td class="col-num masculino-col">{{ $grupoMasc }}</td>
                <td class="col-num femenino-col">{{ $grupoFem }}</td>
                <td class="col-num">{{ $grupoTotal }}</td>
            </tr>
            @foreach($subItems as $sub)
            <tr class="sub-row">
                <td></td>
                <td></td>
                <td style="text-transform:uppercase">{{ $sub->$subKey }}</td>
                <td class="col-num masculino-col">{{ $sub->masculino }}</td>
                <td class="col-num femenino-col">{{ $sub->femenino }}</td>
                <td class="col-num">{{ $sub->total }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-general-row">
            <td colspan="3">TOTALES GENERALES</td>
            <td>{{ $totalMascGlobal }}</td>
            <td>{{ $totalFemGlobal }}</td>
            <td>{{ $totalGlobal }}</td>
        </tr>
    </tfoot>
</table>
@else
    <div class="sin-datos">No se encontraron datos con los filtros seleccionados.</div>
@endif

@endif

@endsection
