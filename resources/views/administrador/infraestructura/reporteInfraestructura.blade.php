@extends('administrador.reporte.baseReporte')

@section('titulo', 'Reporte de Usuarios')
@section('nombreUniversidad', 'Universidad Pública de El Alto')
@section('titulo_header', 'Reporte de Usuarios')

@section('contenido')
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f8f9fa;
            margin: 0px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #00539C;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #00539C;
            color: white;
            text-transform: uppercase;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
    </style>
    <h1>PLANOS</h1>
    <table style="width: 98%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>

            <th style="background-color: #d42316">Estado del Inmueble:
            <td>Todo correcto</td>
            </th>
            <th style="background-color: #d42316">Estado del Tramite:
            <td>Todo correcto</td>
            </th>
        </tr>
        <tr>

            <th>Observacion:
            <td>Todo correcto</td>
            </th>
            <th>Creacion:
            <td>Todo correcto</td>
            </th>
        </tr>

    </table>



    <table>
        <tr>
            <th>Propiedad de</th>
            <td>Municipalidad de Viacha</td>
        </tr>
        <tr>
            <th>Uso Asignado</th>
            <td>Cancha Deportiva (Santa Bárbara)</td>
        </tr>
        <tr>
            <th>Calles</th>
            <td>S/N</td>
        </tr>
        <tr>
            <th>Distrito</th>
            <td>2</td>
        </tr>
        <tr>
            <th>Ubicación</th>
            <td>Positos Chuquiaguillo</td>
        </tr>
        <tr>
            <th>Sup. S/Test</th>
            <td>---</td>
        </tr>
        <tr>
            <th>Sup. S/Lev.</th>
            <td>6245.10 m²</td>
        </tr>
        <tr>
            <th>Sup. Adju.</th>
            <td>---</td>
        </tr>
        <tr>
            <th>Sup. Útil</th>
            <td>6245.10 m²</td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td>Agosto/2010</td>
        </tr>
        <tr>
            <th>Urb.</th>
            <td>S/N</td>
        </tr>
        <tr>
            <th>Mzn.</th>
            <td>S/N</td>
        </tr>
        <tr>
            <th>Lote Nº</th>
            <td>S/N</td>
        </tr>
    </table>

 


    @foreach ($planos as $plano)
    @if ($plano->base64)
        <div style="width: 100%; height: 90%; text-align:center; padding-top:120px">
           
            <img src="{{ $plano->base64 }}" alt="Plano" style="width: 70%">
        </div>
         <footer>
                Página <span class="pagenum"></span>
        </footer>
    @endif
@endforeach

@endsection
