@extends('principal')
@section('titulo', 'NOTICIAS SEDES')
@section('contenido')
   <div class="row">
          <div class="col-12">
            <div class="card">
               <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                               <i class="fas fa-newspaper  me-2"></i> Lista de Convocatorias
                            </h4>
                        </div>
                        <div class="col-auto">
                            @can('convocatoria.crear')
                                <a href="{{ route('convocatoria.nuevaConvocatoria') }}" target="_blank" class="btn btn-primary"> 
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </a>
                            @endcan
                            
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table table-bordered table-md table-hover" id="tabla_listar_convocatorias">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>TÍTULO</th>
                                    <th>TIPO</th>
                                    <th>SEDE</th>
                                    <th>CREACIÓN</th>                                                            
                                    <th>PUBLICAR</th>                                                                   
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/noticias/convocatoria.js') }}" type="module"></script>
@endsection
