<style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10px;
        color: #2c3e50;
        margin: 0;
        padding: 0;
    }

    /* ── Barra de info (gestión / generador) ── */
    .info-generador {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
        font-size: 10px;
        color: #555;
    }

    .info-generador td {
        padding: 5px 0;
        border: none;
        border-bottom: 1px solid #dcc8c8;
    }

    .info-derecha {
        text-align: right;
    }

    .info-generador strong {
        color: #2c3e50;
    }

    /* ── Encabezado de sección (Detalle por Carrera / Sede) ── */
    .titulo-seccion {
        background-color: #851a1a;
        color: #ffffff;
        padding: 7px 10px;
        font-weight: bold;
        text-align: left;
        margin-top: 18px;
        margin-bottom: 0;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 1px;
    }

    /* ── Tabla de datos ── */
    table.datos {
        width: 100%;
        border-collapse: collapse;
    }

    table.datos th {
        background-color: #851a1a;
        color: #ffffff;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 7px 10px;
        border: 1px solid #6b1414;
        vertical-align: middle;
    }

    table.datos td {
        font-size: 10px;
        padding: 6px 10px;
        border-bottom: 1px solid #eeeeee;
        color: #2c3e50;
        vertical-align: middle;
    }

    table.datos tbody tr:nth-child(even) td {
        background-color: #fdf5f5;
    }

    table.datos tr {
        page-break-inside: avoid;
    }

    /* ── Alineación numérica ── */
    .col-numerica {
        text-align: right;
    }

    /* ── Fila de grupo: nombre de carrera o sede (cabecera del bloque) ── */
    .grupo-row td {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
        border-top: 2px solid #c8a0a0;
        border-bottom: 1px solid #dcc8c8;
    }

    /* ── Fila de subtotal por grupo ── */
    .subtotal-row td {
        background-color: #f2e8e8;
        color: #5a1010;
        font-weight: bold;
        font-style: italic;
        border-top: 1px solid #dcc8c8;
    }

    /* ── Fila de total general ── */
    .total-general-row td {
        background-color: #851a1a;
        color: #ffffff;
        font-weight: bold;
        font-size: 11px;
        border: 1px solid #6b1414;
    }
</style>
