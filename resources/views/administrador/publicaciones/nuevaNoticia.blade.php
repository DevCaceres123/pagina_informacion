@extends('principal')
@section('titulo', 'NUEVA NOTICIA')
@section('contenido')
    <div class="container py-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            {{-- Encabezado --}}
            <div class="card-header  bg-dark  text-white text-center p-4">
                <h4 class="card-title mb-0 fw-bold fs-3">
                    <i class="fas fa-newspaper me-2"></i> Publicar Nueva Noticia <span class="ms-3 fs-16">Campos obligatorios
                        <strong class="text-danger">(*)</strong>
                </h4>
            </div>

            <div class="card-body p-5">
                <form enctype="multipart/form-data" id="formNuevaNoticia">
                    <div class="row g-5">
                        {{-- Columna Izquierda: Información --}}
                        <div class="col-md-7 border-end">
                            {{-- TÍTULO --}}
                            <div class="mb-4">
                                <label for="titulo" class="form-label fw-semibold fs-5">
                                    <i class="fas fa-heading text-primary me-2"></i> Título <strong
                                        class="text-danger">(*)</strong>
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-pen"></i>
                                    </span>
                                    <input type="text" name="titulo" id="titulo" class="form-control form-control-lg"
                                        placeholder="Título de la noticia..." required value="{{ $noticia->titulo ?? '' }}">
                                </div>
                                <div id="_titulo"></div>
                            </div>

                            {{-- TIPO DE NOTICIA --}}
                            <div class="mb-4">
                                <label for="tipo" class="form-label fw-semibold fs-5">
                                    <i class="fas fa-tags text-success me-2"></i> Tipo de Noticia <strong
                                        class="text-danger">(*)</strong>
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <select name="tipo" id="tipo" class="form-select form-select-lg" required>
                                        <option value="" disabled {{ empty($noticia) ? 'selected' : '' }}>Seleccione tipo de
                                            noticia</option>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id }}"
                                                {{ old('tipo', $noticia->categoria_id  ?? '') == $tipo->id ? 'selected' : '' }}>
                                                {{ strtoupper($tipo->nombre) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="_tipo"></div>
                            </div>

                            {{-- SEDE --}}
                            <div class="mb-4">
                                <label for="sede" class="form-label fw-semibold fs-5">
                                    <i class="fas fa-university text-warning me-2"></i> Seleccionar Sede <strong
                                        class="text-danger">(*)</strong>
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <select name="sede" id="sede" class="form-select form-select-lg" required>
                                        <option value="" disabled {{ empty($noticia) ? 'selected' : '' }}>Seleccione
                                            la sede</option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}"
                                                {{ old('sede', $noticia->sede_id ?? '') == $sede->id ? 'selected' : '' }}>
                                                {{ strtoupper($sede->nombre) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="_sede"></div>

                            </div>

                            {{-- CONTENIDO --}}
                            <div class="mb-4">
                                <label for="contenido" class="form-label fw-semibold fs-5">
                                    <i class="fas fa-align-left text-danger me-2"></i> Contenido <strong
                                        class="text-danger">(*)</strong>
                                </label>
                                <textarea name="contenido" id="contenido" class="form-control shadow-sm" rows="12"
                                    placeholder="Escriba aquí el contenido de la noticia...">{{ old('contenido', $noticia->contenido ?? '') }}</textarea>

                                <div id="_contenido"></div>
                            </div>


                        </div>

                        {{-- Columna Derecha: Fotos --}}
                        <div class="col-md-5">
                            <div class="sticky-top" style="top: 2rem;">
                                {{-- PORTADA --}}
                                <div class="mb-4">
                                    <label for="portada" class="form-label fw-semibold fs-5">
                                        <i class="fas fa-camera text-primary me-2"></i> Foto de Portada <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" name="portada" id="portada"
                                        class="form-control form-control-lg shadow-sm" accept="image/*" {{ empty($noticia) ? 'required' : '' }}>

                                    <div id="_portada"></div>
                                </div>

                                {{-- OTRAS FOTOS --}}
                                <div class="mb-4">
                                    <label for="fotos" class="form-label fw-semibold fs-5">
                                        <i class="fas fa-images text-secondary me-2"></i> Otras Fotos
                                    </label>
                                    <input type="file" name="fotos[]" id="fotos"
                                        class="form-control form-control-lg shadow-sm" accept="image/*" multiple>

                                    <div id="_fotos"></div>
                                </div>

                                <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    La foto de portada es obligatoria. Las fotos adicionales son opcionales.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="youtube_url" class="form-label fw-semibold fs-5">
                                    <i class="fab fa-youtube text-danger me-2"></i> Video de YouTube
                                </label>

                                {{-- Input para URL --}}
                                <input type="url" name="youtube_url" id="youtube_url"
                                    class="form-control form-control-lg shadow-sm"
                                    placeholder="https://www.youtube.com/watch?v=XXXXX"
                                    value="{{ $noticia->url_video ?? '' }}">

                                {{-- Vista previa del video --}}
                                <div id="youtube_preview" class="mt-3 d-none">
                                    <div class="ratio ratio-16x9 rounded shadow-sm border">
                                        <iframe id="youtube_iframe" src="" title="Vista previa de YouTube"
                                            allowfullscreen></iframe>
                                    </div>
                                    <div id="_youtube_url"></div>
                                </div>
                                <div class="form-text">
                                    Pega el enlace completo del video de YouTube y se mostrará la vista previa.
                                </div>


                                {{-- Mostrar fotos actuales --}}
                                @if (!empty($imagenes) && $imagenes->count())
                                    <div class="mb-4 mt-3">
                                        <label class="form-label fw-semibold fs-5">Fotos Actuales</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($imagenes as $foto)
                                                <div class="position-relative text-center"
                                                    style="width: 160px; height: 170px;">
                                                    {{-- Imagen --}}
                                                    <img src="{{ asset('storage/imagenes_noticias/' . $foto->imagen) }}"
                                                        alt="Foto noticia" class="rounded border shadow-sm mb-1"
                                                        style="width: 100%; height: 100%; object-fit: contain;">

                                                    {{-- Botón eliminar --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-foto"
                                                        data-id="{{ $foto->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    {{-- Nombre de la imagen debajo --}}

                                                    <span
                                                        class="badge bg-secondary position-absolute bottom-0 start-0 m-1">
                                                        {{ \Illuminate\Support\Str::before($foto->imagen, '_') }}
                                                    </span>

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif


                            </div>

                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="d-flex justify-content-end mt-5 pt-4 border-top">
                        <button type="reset" class="btn btn-outline-secondary btn-lg fw-bold me-3 shadow-sm">
                            <i class="fas fa-undo me-2"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" id="btnGuardarNoticia"
                            data-tipo="{{ isset($noticia->id) ? 'editar' : 'nuevo' }}"
                            data-id="{{ $noticia->id ?? '' }}">
                            <i class="fas fa-save me-2"></i> Guardar Noticia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/noticias/nueva_noticia.js') }}" type="module"></script>
@endsection
