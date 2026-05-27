@extends('administrador.reporte.baseReporte')

@section('titulo', 'Ficha de Seguimiento — ' . strtoupper($estudiante->nombre_completo))

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
        margin-bottom: 20px;
        letter-spacing: 1px;
    }

    /* Sección */
    .seccion-titulo {
        background-color: #851a1a;
        color: #ffffff;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 10px;
        margin-top: 16px;
        margin-bottom: 0;
    }

    /* Tabla de datos personales (clave-valor) */
    .tabla-ficha {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .tabla-ficha td {
        padding: 6px 10px;
        border: 1px solid #ddd;
        vertical-align: top;
    }

    .tabla-ficha .celda-label {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
        width: 22%;
        white-space: nowrap;
    }

    .tabla-ficha .celda-valor {
        color: #2c3e50;
        width: 28%;
        text-transform: capitalize;
    }

    /* Tabla de listas (formularios, requisitos) */
    .tabla-lista {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-top: 0;
    }

    .tabla-lista th {
        background-color: #851a1a;
        color: #fff;
        padding: 6px 10px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        border: 1px solid #6b1414;
        text-align: left;
    }

    .tabla-lista td {
        padding: 6px 10px;
        border: 1px solid #ddd;
        color: #2c3e50;
    }

    .tabla-lista tbody tr:nth-child(even) td {
        background-color: #fdf5f5;
    }

    .tabla-lista tr { page-break-inside: avoid; }

    /* Documentos estado */
    .doc-si  { color: #27ae60; font-weight: bold; }
    .doc-no  { color: #c0392b; font-weight: bold; }

    /* Observación */
    .observacion-box {
        border: 1px solid #ddd;
        padding: 8px 10px;
        font-size: 11px;
        color: #555;
        background-color: #fafafa;
        margin-top: 0;
        min-height: 30px;
    }

    .sin-datos {
        color: #999;
        font-style: italic;
        padding: 8px 10px;
        border: 1px solid #ddd;
        font-size: 11px;
    }
</style>

<div class="titulo-documento">Ficha de Seguimiento de Titulación</div>

{{-- ===== DATOS PERSONALES ===== --}}
<div class="seccion-titulo">Datos del Estudiante</div>
<table class="tabla-ficha">
    <tr>
        <td class="celda-label">Nombre completo</td>
        <td class="celda-valor" colspan="3" style="text-transform:uppercase">{{ $estudiante->nombre_completo }}</td>
    </tr>
    <tr>
        <td class="celda-label">Matrícula</td>
        <td class="celda-valor">{{ $estudiante->matricula }}</td>
        <td class="celda-label">Gestión</td>
        <td class="celda-valor">{{ $estudiante->gestion }}</td>
    </tr>
    <tr>
        <td class="celda-label">Género</td>
        <td class="celda-valor">{{ ucfirst($estudiante->genero) }}</td>
        <td class="celda-label">Tipo documento</td>
        <td class="celda-valor">{{ $estudiante->tipo_documento }}</td>
    </tr>
    <tr>
        <td class="celda-label">N° Documento</td>
        <td class="celda-valor">{{ strtoupper($estudiante->numero_documento) }}</td>
        <td class="celda-label">Sede</td>
        <td class="celda-valor" style="text-transform:uppercase">{{ $estudiante->sede->nombre ?? '—' }}</td>
    </tr>
    <tr>
        <td class="celda-label">Carrera</td>
        <td class="celda-valor" colspan="3" style="text-transform:uppercase">{{ $estudiante->carrera->nombre ?? '—' }}</td>
    </tr>
</table>

{{-- ===== ESTADO DE DOCUMENTOS ===== --}}
<div class="seccion-titulo">Estado de Documentos</div>
<table class="tabla-lista">
    <thead>
        <tr>
            <th style="width:60%">Documento</th>
            <th style="width:40%; text-align:center">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach([
            'certificado_habilitacion' => 'Certificado de Habilitación',
            'copia_titulo_tec_medio'   => 'Copia del Título — Técnico Medio',
            'copia_titulo_tec_superior'=> 'Copia del Título — Técnico Superior',
            'copia_titulo_licenciatura'=> 'Copia del Título — Licenciatura',
        ] as $campo => $etiqueta)
        <tr>
            <td>{{ $etiqueta }}</td>
            <td style="text-align:center">
                @if($estudiante->$campo)
                    <span class="doc-si">&#10003; Entregado</span>
                @else
                    <span class="doc-no">&#10007; Pendiente</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ===== FORMULARIOS DE INSCRIPCIÓN ===== --}}
<div class="seccion-titulo">Formularios de Inscripción</div>
@if($estudiante->formulariosInscripcion->isNotEmpty())
    <table class="tabla-lista">
        <thead>
            <tr>
                <th style="width:10%">#</th>
                <th>Fecha de Recepción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiante->formulariosInscripcion as $i => $formulario)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($formulario->fecha_recepcion)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="sin-datos">Sin formularios de inscripción registrados.</div>
@endif

{{-- ===== REQUISITOS DE DEFENSA ===== --}}
<div class="seccion-titulo">Requisitos de Defensa</div>
@if($estudiante->requisitosDefensa->isNotEmpty())
    <table class="tabla-lista">
        <thead>
            <tr>
                <th style="width:10%">#</th>
                <th>Nombre del Requisito</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiante->requisitosDefensa as $i => $requisito)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-transform:capitalize">{{ $requisito->nombre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="sin-datos">Sin requisitos de defensa registrados.</div>
@endif

{{-- ===== OBSERVACIONES ===== --}}
<div class="seccion-titulo">Observaciones</div>
<div class="observacion-box">
    {{ $estudiante->observacion ? ucfirst($estudiante->observacion) : 'Sin observaciones.' }}
</div>

@endsection
