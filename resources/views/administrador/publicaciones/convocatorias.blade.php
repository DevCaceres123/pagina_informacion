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
                                <i class="fas   me-2"></i> LISTA DE CONVOCATORIAS
                            </h4>
                        </div>
                        <div class="col-auto">
                            
                                <button type="button" class="btn btn-primary nuevo_vehiculo" data-bs-toggle="modal"
                                    data-bs-target="#modal_color">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_convocatorias">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>TITULO</th>
                                    <th>TIPO</th>
                                    <th>CREACION</th>                                                            
                                    <th>PUBLICAR</th>                                                                   
                                    <th>ACCION</th>
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
    <script src="{{ asset('js/modulos/noticias/noticia.js') }}" type="module"></script>
@endsection
