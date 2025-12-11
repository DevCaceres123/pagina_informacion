@extends('principal')
@section('titulo', 'NUEVA CONVOCATORIA')
@section('contenido')
    <div class="container py-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            {{-- Encabezado --}}
            <div class="card-header  bg-dark text-white text-center p-4">
                <h4 class="card-title mb-0 fw-bold fs-3">
                    <i class="fas fa-newspaper me-2"></i> Publicar Nueva Convocatoria <span class="ms-3 fs-16">Campos
                        obligatorios
                        <strong class="text-danger">(*)</strong>
                </h4>
            </div>

            <div class="card-body p-5">
                <form enctype="multipart/form-data" id="formNuevaConvocatoria">
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
                                    <i class="fas fa-tags text-success me-2"></i> Tipo <strong
                                        class="text-danger">(*)</strong>
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <select name="tipo" id="tipo" class="form-select form-select-lg" required>
                                        <option value="" disabled {{ empty($noticia) ? 'selected' : '' }}>Seleccione
                                            tipo</option>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id }}"
                                                {{ old('tipo', $noticia->categoria_id ?? '') == $tipo->id ? 'selected' : '' }}>
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
                                    placeholder="Escriba aquí el contenido de la noticia...">{{ old('contenido', $noticia->descripcion ?? '') }}</textarea>

                                <div id="_contenido"></div>
                            </div>


                        </div>

                        {{-- Columna Derecha: Fotos --}}
                        <div class="col-md-5">
                            <div class="sticky-top" style="top: 2rem;">
                                {{-- PORTADA --}}
                                <div class="mb-4">
                                    <label for="portada" class="form-label fw-semibold fs-5">
                                        <i class="fas fa-file text-primary me-2"></i>Convocatoria (pdf) <strong
                                            class="text-danger">(*)</strong>
                                    </label>
                                    <input type="file" name="convocatoria" id="convocatoria"
                                        class="form-control form-control-lg shadow-sm" accept=".pdf"
                                        {{ empty($noticia) ? 'required' : '' }}>

                                    <div id="_convocatoria"></div>
                                    <div id="documento_convocatoria" class="mt-3 ">
                                        @if (!empty($noticia) && $noticia->archivo)
                                            <div class="alert alert-secondary d-flex align-items-center mb-2"
                                                role="alert">
                                                <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                                                <div>
                                                    <strong>Convocatoria actual:</strong> vista previa del documento
                                                    cargado.
                                                </div>
                                            </div>

                                            <iframe src="{{ asset('storage/' . $noticia->archivo) }}"
                                                class="w-100 border rounded shadow-sm" style="height: 300px;">
                                            </iframe>
                                        @endif
                                    </div>

                                </div>

                                {{-- OTRAS FOTOS --}}
                                <div class="mb-4">
                                    <label for="fotos" class="form-label fw-semibold fs-5">
                                        <i class="fas fa-images text-secondary me-2"></i>Fotos
                                    </label>
                                    <input type="file" name="fotos[]" id="fotos"
                                        class="form-control form-control-lg shadow-sm" accept="image/*" multiple>

                                    <div id="_fotos"></div>
                                </div>

                                <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    La convocatoria es obligatoria. Las fotos adicionales son opcionales.
                                </div>
                            </div>

                            <div class="mb-4">
                                {{-- Mostrar fotos actuales --}}
                                @if (!empty($imagenes) && $imagenes->count())
                                    <div class="mb-4 mt-3">
                                        <label class="form-label fw-semibold fs-5">Fotos Actuales</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($imagenes as $foto)
                                                <div class="position-relative text-center"
                                                    style="width: 160px; height: 170px;">
                                                    {{-- Imagen --}}
                                                    <img src="{{ asset('storage/' . $foto->imagen) }}" alt="Foto noticia"
                                                        class="rounded border shadow-sm mb-1"
                                                        style="width: 100%; height: 100%; object-fit: contain;">

                                                    {{-- Botón eliminar --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-foto"
                                                        data-id="{{ $foto->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    {{-- Nombre de la imagen debajo --}}

                                                    {{-- <span
                                                        class="badge bg-secondary position-absolute bottom-0 start-0 m-1">
                                                        {{ \Illuminate\Support\Str::before($foto->imagen, '_') }}
                                                    </span> --}}

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
    <script src="{{ asset('js/modulos/noticias/nueva_convocatoria.js') }}" type="module"></script>
@endsection
