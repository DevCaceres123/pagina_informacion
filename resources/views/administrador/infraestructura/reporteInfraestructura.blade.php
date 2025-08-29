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
            font-size: 24px;
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
    <h1>INFORMACION {{$sede->nombre}}</h1>
    <table style="width: 98%; margin-bottom: 40px; border-collapse: collapse;">
        <tr>

            <th style="background-color: #d42316">Estado del Inmueble:
            <td style="text-transform: capitalize">{{$infraestructura->estado_inmueble}}</td>
            </th>
            <th style="background-color: #d42316">Estado del Tramite:
            <td style="text-transform: capitalize">{{$infraestructura->estado_tramite}}</td>
            </th>
        </tr>
        <tr>

            <th>Observacion:
            <td style="text-transform: capitalize">{{$infraestructura->observacion_estado}}</td>
            </th>
            <th>Creacion:
              <td style="text-transform: capitalize">{{$infraestructura->created_at_formateado}}</td>
            </th>
        </tr>

    </table>



    <table>
        <tr>
            <th>Propiedad de</th>
            <td>{{$infraestructura->propiedad ?? 'N/A'}}</td>
        </tr>
        <tr>
            <th>Uso Asignado</th>
            <td>{{$infraestructura->uso_asignado ?? 'N/A'}}</td>
        </tr>     
        <tr>
            <th>Distrito</th>
            <td>{{$ubicacion->distrito ?? 'N/A'}}</td>
        </tr>
        
        <tr>
            <th>Ubicación</th>
           <td>{{$ubicacion->ubicacion ?? 'N/A'}}</td>
        </tr>

         <tr>
            <th>Urb.</th>
            <td>{{ $ubicacion->urb ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Mzn.</th>
            <td>{{ $ubicacion->manzano ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Lote Nº</th>
            <td>{{ $ubicacion->lote ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Sup. S/Test</th>
            <td>{{ $ubicacion->sup_test ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Sup. S/Lev.</th>
            <td>{{ $ubicacion->sup_lev ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Sup. Adju.</th>
            <td>{{ $ubicacion->sup_adju ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Sup. Útil</th>
            <td>{{ $ubicacion->sup_util ?? 'N/A' }}</td>
        </tr>        
       
    </table>


    <footer>
        Página <span class="pagenum"></span>
    </footer>

    @foreach ($planos as $plano)
        @if ($plano->base64)
            <div style="width: 100%; height: 90%; text-align:center; padding-top:150px">
                <img src="{{ $plano->base64 }}" alt="Plano" style="width: 70%">
            </div>
            <footer>
                Página <span class="pagenum"></span>
            </footer>
        @endif
    @endforeach

@endsection
