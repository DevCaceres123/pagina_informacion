@extends('index')
@section('titulo', 'CONVOCATORIAS')
@section('contenido')
    <div class="container my-5">
        <h2 class="mb-4 text-center fw-bold">Convocatorias</h2>

        <div class="list-group">

            @foreach ($convocatorias as $convocatoria)
                @php
                    // Tomar la primera imagen o una por defecto
                    $imagen = $convocatoria->imgConvocatorias->first()->imagen ?? 'imagenes_convocatorias/default.webp';
                @endphp

                <div class="list-group-item list-group-item-action mb-4 shadow-sm rounded-3">
                    <div class="row g-0">
                        <!-- Imagen -->
                        <div class="col-md-4">
                            <img src="{{ asset('storage/' . $imagen) }}" class="img-fluid rounded-start object-fit-contain"
                                alt="Imagen de {{ $convocatoria->titulo }}" style="width: 100%;height: 250px;">
                        </div>

                        <!-- Texto -->
                        <div class="col-md-8 d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="fw-bold text-primary mb-0 text-uppercase">
                                    {{ $convocatoria->titulo }}
                                </h5>
                                <a href="{{ asset('storage/' . ($convocatoria->archivo ?? '')) }}"
                                    class="btn btn-info btn-sm rounded-pill ms-1" title="Descargar PDF" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>

                            <p class="text-muted mb-3">
                                {{ Str::limit($convocatoria->descripcion, 200, '...') }}
                            </p>
                            
                            <p class="small text-muted mb-3 text-capitalize">
                                <i class="far fa-calendar-alt me-1"></i> {{ $convocatoria->created_at->translatedFormat('d F Y') }} |
                                <i class="fas fa-user me-1"></i>
                                {{ $convocatoria->usuario
                                    ? strtoupper(substr($convocatoria->usuario->nombres, 0, 1)) .
                                        '. ' .
                                        strtok($convocatoria->usuario->apellidos, ' ')
                                    : 'Desconocido' }}
                                |
                                <i class="fas fa-tag me-1"></i> {{ $convocatoria->categoria->nombre }}
                            </p>

                            <div>
                                <a  href="{{ route('convocatoria.deatelleConvocatoria', encrypt($convocatoria->id)) }} "class="btn btn-outline-primary btn-md mt-2 "
                                    style="font-family: 'Inter', sans-serif;">Ver
                                    más</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach



            <!-- Paginación -->
            <div class="mb-2 mt-4">
                <p class="text-muted text-center">
                    Mostrando {{ $convocatorias->firstItem() }} al {{ $convocatorias->lastItem() }} de {{ $convocatorias->total() }}
                    resultados
                </p>
            </div>

            <div class="d-flex justify-content-center">
                {{ $convocatorias->links('pagination::simple-bootstrap-5') }}
            </div>

          <div class="text-center mb-3">
                <a href="https://admisionestudiantil.upea.bo/sie/WxXY8Eo2K4n6rIJ5ZJmwj1LRA" target="_black"
                class="btn btn-primary btn-md shadow-sm">
                    
                    Ver convocatorias de admisión
                </a>        
         </div>



        </div>
    </div>
@endsection
