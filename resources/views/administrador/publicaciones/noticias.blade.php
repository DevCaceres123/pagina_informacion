@extends('principal')
@section('titulo', 'NOTICIAS SEDES')
@section('contenido')
   <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Lista de Noticias</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('noticia.nuevaNoticia') }}" target="_blank" class="btn btn-primary"> 
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_listar_noticias">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>TITULO</th>
                                    <th>TIPO</th>
                                    <th>CREACION</th>
                                    <th>NOT.DESTACADO</th>                                    
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
