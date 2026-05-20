@extends('administrador.reporte.baseReporte')

@section('titulo', 'Ficha de Infraestructura')
@section('nombreUniversidad', 'Universidad Pública de El Alto')

@section('contenido')
    <style>
        /* ── Título principal ── */
        .doc-titulo {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #ffffff;
            background-color: #851a1a;
            padding: 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0;
        }

        .doc-subtitulo {
            text-align: center;
            font-size: 12px;
            color: #851a1a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #851a1a;
            border-top: none;
            padding: 6px 0;
            margin-bottom: 16px;
        }

        /* ── Tarjetas de estado ── */
        .estado-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .estado-table td {
            width: 25%;
            border: 1px solid #c8a0a0;
            padding: 0;
            vertical-align: top;
        }

        .estado-label {
            background-color: #851a1a;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 8px;
        }

        .estado-valor {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            padding: 7px 8px;
            text-transform: capitalize;
        }

        /* ── Observación ── */
        .obs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .obs-table td {
            border: 1px solid #c8a0a0;
            padding: 0;
            vertical-align: top;
        }

        .obs-valor {
            font-size: 10px;
            color: #2c3e50;
            padding: 7px 10px;
            text-transform: capitalize;
        }

        /* ── Encabezado de sección ── */
        .seccion-header {
            background-color: #6b1414;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 5px 10px;
            margin-top: 14px;
            margin-bottom: 0;
        }

        /* ── Tabla de detalle (etiqueta / valor) ── */
        .detalle-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .detalle-table tr:nth-child(even) td {
            background-color: #fdf5f5;
        }

        .detalle-table .lbl {
            width: 32%;
            background-color: #f2e8e8;
            color: #5a1010;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 10px;
            border: 1px solid #dcc8c8;
            vertical-align: middle;
        }

        .detalle-table .val {
            font-size: 10px;
            color: #2c3e50;
            padding: 7px 10px;
            border: 1px solid #dcc8c8;
            text-transform: capitalize;
            vertical-align: middle;
        }

        /* ── Tabla de superficies (4 cajas) ── */
        .sup-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .sup-table td {
            width: 25%;
            border: 1px solid #dcc8c8;
            padding: 0;
            text-align: center;
            vertical-align: top;
        }

        .sup-label {
            background-color: #f2e8e8;
            color: #5a1010;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 4px;
            border-bottom: 1px solid #dcc8c8;
        }

        .sup-valor {
            font-size: 13px;
            font-weight: bold;
            color: #851a1a;
            padding: 8px 4px 2px;
        }

        .sup-unidad {
            font-size: 8px;
            color: #7f8c8d;
            padding-bottom: 8px;
        }

        /* ── Planos ── */
        .plano-pagina {
            page-break-before: always;
            text-align: center;
            padding-top: 20px;
        }

        .plano-titulo {
            background-color: #851a1a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 8px;
            margin-bottom: 20px;
        }
    </style>

    {{-- Título del documento --}}
    <div class="doc-titulo">Ficha de Infraestructura</div>
    <div class="doc-subtitulo">{{ strtoupper($sede->nombre) }}</div>

    {{-- Tarjetas de estado --}}
    <table class="estado-table">
        <tr>
            <td>
                <div class="estado-label">Estado del Inmueble</div>
                <div class="estado-valor">{{ $infraestructura->estado_inmueble ?? 'N/A' }}</div>
            </td>
            <td>
                <div class="estado-label">Estado del Trámite</div>
                <div class="estado-valor">{{ $infraestructura->estado_tramite ?? 'N/A' }}</div>
            </td>
            <td>
                <div class="estado-label">Tiempo de Trámite</div>
                <div class="estado-valor">{{ $infraestructura->tiempo_tramite ?? 'N/A' }}</div>
            </td>
            <td>
                <div class="estado-label">Fecha de Registro</div>
                <div class="estado-valor">{{ $infraestructura->created_at_formateado ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    {{-- Observación --}}
    <table class="obs-table">
        <tr>
            <td style="width: 18%">
                <div class="estado-label">Observación</div>
            </td>
            <td>
                <div class="obs-valor">{{ $infraestructura->observacion_estado ?? '—' }}</div>
            </td>
        </tr>
    </table>

    {{-- Sección: Datos de Identificación --}}
    <div class="seccion-header">Datos de Identificación</div>
    <table class="detalle-table">
        <tr>
            <td class="lbl">Escala</td>
            <td class="val">{{ $ubicacion->escala ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Propiedad de</td>
            <td class="val">{{ $infraestructura->propiedad ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Uso Asignado</td>
            <td class="val">{{ $infraestructura->uso_asignado ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">N° de Nota</td>
            <td class="val">{{ $infraestructura->numero_nota ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Sección: Datos de Ubicación --}}
    <div class="seccion-header">Datos de Ubicación</div>
    <table class="detalle-table">
        <tr>
            <td class="lbl">Distrito</td>
            <td class="val">{{ $ubicacion->distrito ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Ubicación</td>
            <td class="val">{{ $ubicacion->ubicacion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Urbanización</td>
            <td class="val">{{ $ubicacion->urb ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Manzano</td>
            <td class="val">{{ $ubicacion->manzano ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Lote N°</td>
            <td class="val">{{ $ubicacion->lote ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Sección: Superficies --}}
    <div class="seccion-header">Superficies</div>
    <table class="sup-table">
        <tr>
            <td>
                <div class="sup-label">S/ Testimonio</div>
                <div class="sup-valor">{{ $ubicacion->sup_test ?? 'N/A' }}</div>
                <div class="sup-unidad">m²</div>
            </td>
            <td>
                <div class="sup-label">S/ Levantamiento</div>
                <div class="sup-valor">{{ $ubicacion->sup_lev ?? 'N/A' }}</div>
                <div class="sup-unidad">m²</div>
            </td>
            <td>
                <div class="sup-label">S/ Adjudicación</div>
                <div class="sup-valor">{{ $ubicacion->sup_adju ?? 'N/A' }}</div>
                <div class="sup-unidad">m²</div>
            </td>
            <td>
                <div class="sup-label">Superficie Útil</div>
                <div class="sup-valor">{{ $ubicacion->sup_util ?? 'N/A' }}</div>
                <div class="sup-unidad">m²</div>
            </td>
        </tr>
    </table>

    {{-- Planos: cada uno en su propia página --}}
    @foreach ($planos as $index => $plano)
        @if ($plano->base64)
            <div class="plano-pagina">
                <div class="plano-titulo">Plano {{ $index + 1 }}</div>
                <img src="{{ $plano->base64 }}" alt="Plano {{ $index + 1 }}" style="max-width: 90%; max-height: 600px;">
            </div>
        @endif
    @endforeach

@endsection
