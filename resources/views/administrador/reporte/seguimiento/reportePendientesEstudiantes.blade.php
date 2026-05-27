@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Documentos Pendientes')

@section('contenido')

@php
$etiquetasCampo = [
    'certificado_habilitacion'    => 'Cert. Hab.',
    'fotocopia_titulo_tec_medio'  => 'Fot. T.M.',
    'copia_titulo_tec_medio'      => 'Cop. T.M.',
    'fotocopia_titulo_tec_superior' => 'Fot. T.S.',
    'copia_titulo_tec_superior'   => 'Cop. T.S.',
    'fotocopia_titulo_licenciatura' => 'Fot. Lic.',
    'copia_titulo_licenciatura'   => 'Cop. Lic.',
];

$etiquetasResumen = [
    'certificado_habilitacion'      => 'Cert. Habilitación faltante',
    'fotocopia_titulo_tec_medio'    => 'Fot. Tít. Téc. Medio faltante',
    'copia_titulo_tec_medio'        => 'Cop. Tít. Téc. Medio faltante',
    'fotocopia_titulo_tec_superior' => 'Fot. Tít. Téc. Superior faltante',
    'copia_titulo_tec_superior'     => 'Cop. Tít. Téc. Superior faltante',
    'fotocopia_titulo_licenciatura' => 'Fot. Tít. Licenciatura faltante',
    'copia_titulo_licenciatura'     => 'Cop. Tít. Licenciatura faltante',
];

// Columnas de documentos a mostrar: cert siempre + los de la modalidad
$docCols = array_merge(['certificado_habilitacion'], $camposModalidad);

// Anchos de columnas según cantidad de doc cols
$numDocCols = count($docCols);
if ($numDocCols <= 3) {
    $anchos = ['3%','24%','9%','5%','14%','20%','6%'];
    $anchoDoc = round(19 / $numDocCols, 1) . '%';
} else {
    $anchos = ['3%','19%','8%','5%','11%','16%','5%'];
    $anchoDoc = round(33 / $numDocCols, 1) . '%';
}
$colspanTotal = 7 + $numDocCols - 1;
@endphp

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
        width: 18%;
        white-space: nowrap;
    }

    .tabla-resumen {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 0;
    }

    .tabla-resumen td {
        padding: 5px 10px;
        border: 1px solid #ddd;
        text-align: center;
        font-weight: bold;
    }

    .tabla-resumen .resumen-label {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
        text-align: left;
    }

    .resumen-valor {
        background-color: #fff3f3;
        color: #851a1a;
        font-size: 12px;
        width: 6%;
    }

    .tabla-lista {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-top: 0;
    }

    .tabla-lista th {
        background-color: #851a1a;
        color: #fff;
        padding: 5px 6px;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        border: 1px solid #6b1414;
        text-align: center;
    }

    .tabla-lista td {
        padding: 4px 6px;
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
        font-size: 9px;
        padding: 5px 6px;
        border: 1px solid #6b1414;
    }

    .doc-ok {
        background-color: #d4edda;
        color: #155724;
        font-weight: bold;
        text-align: center;
    }

    .doc-falta {
        background-color: #f8d7da;
        color: #721c24;
        font-weight: bold;
        text-align: center;
    }

    .sin-datos {
        text-align: center;
        color: #999;
        font-style: italic;
        padding: 20px;
        font-size: 11px;
    }
</style>

<div class="titulo-documento">Reporte de Estudiantes con Documentos Pendientes</div>

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
    <tr>
        <td class="celda-label">Modalidad</td>
        <td colspan="3">{{ $filtros['modalidad'] }}</td>
    </tr>
</table>

{{-- ===== RESUMEN ===== --}}
<div class="seccion-titulo">Resumen de Pendientes</div>
<table class="tabla-resumen">
    @php
        $resumenItems = array_merge(
            ['certificado_habilitacion' => $resumen['certificado']],
            collect($camposModalidad)->mapWithKeys(fn($c) => [$c => $resumen[$c]])->toArray()
        );
        $chunks = array_chunk(array_keys($resumenItems), 3, true);
    @endphp
    <tr>
        <td class="resumen-label">Total con al menos un pendiente</td>
        <td class="resumen-valor">{{ $estudiantes->count() }}</td>
        @foreach($chunks[0] as $campo)
        <td class="resumen-label">{{ $etiquetasResumen[$campo] }}</td>
        <td class="resumen-valor">{{ $resumenItems[$campo] }}</td>
        @endforeach
    </tr>
    @if(isset($chunks[1]))
    <tr>
        <td class="resumen-label" colspan="2"></td>
        @foreach($chunks[1] as $campo)
        <td class="resumen-label">{{ $etiquetasResumen[$campo] }}</td>
        <td class="resumen-valor">{{ $resumenItems[$campo] }}</td>
        @endforeach
    </tr>
    @endif
</table>

{{-- ===== AVISO DE LÍMITE ===== --}}
@if($limitado)
<div style="background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:6px 10px;font-size:10px;margin-top:10px;border-radius:4px;">
    <strong>&#9888; Aviso:</strong> Se encontraron {{ $totalSinLimite }} estudiantes. Este reporte muestra los primeros 300.
    Aplique filtros más específicos para obtener un resultado completo.
</div>
@endif

{{-- ===== LISTADO ===== --}}
<div class="seccion-titulo">Listado de Estudiantes con Documentos Pendientes — {{ $filtros['modalidad'] }}</div>

@if($estudiantes->isNotEmpty())
    <table class="tabla-lista">
        <thead>
            <tr>
                <th style="width:{{ $anchos[0] }}">#</th>
                <th style="width:{{ $anchos[1] }};text-align:left">Nombre Completo</th>
                <th style="width:{{ $anchos[2] }}">Matrícula</th>
                <th style="width:{{ $anchos[3] }}">Gén.</th>
                <th style="width:{{ $anchos[4] }};text-align:left">Sede</th>
                <th style="width:{{ $anchos[5] }};text-align:left">Carrera</th>
                <th style="width:{{ $anchos[6] }}">Gest.</th>
                @foreach($docCols as $campo)
                <th style="width:{{ $anchoDoc }}">{{ $etiquetasCampo[$campo] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantes as $i => $est)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td style="text-transform:uppercase">{{ $est->nombre_completo }}</td>
                <td style="text-align:center">{{ $est->matricula }}</td>
                <td style="text-align:center;text-transform:capitalize">{{ mb_substr($est->genero,0,1) }}</td>
                <td style="text-transform:uppercase">{{ $est->sede->nombre ?? '—' }}</td>
                <td style="text-transform:uppercase">{{ $est->carrera->nombre ?? '—' }}</td>
                <td style="text-align:center">{{ $est->gestion }}</td>
                @foreach($docCols as $campo)
                <td class="{{ $est->$campo ? 'doc-ok' : 'doc-falta' }}">
                    {{ $est->$campo ? '&#10003;' : '&#10007;' }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ $colspanTotal }}" style="text-align:right">Total de estudiantes con pendientes:</td>
                <td style="text-align:center">{{ $estudiantes->count() }}</td>
            </tr>
        </tfoot>
    </table>
@else
    <div class="sin-datos">No se encontraron estudiantes con documentos pendientes para los filtros seleccionados.</div>
@endif

@endsection
