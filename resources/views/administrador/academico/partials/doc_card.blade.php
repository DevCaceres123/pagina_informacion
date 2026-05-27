<div class="card h-100 border">
    <div class="card-header bg-{{ $color }} bg-opacity-10 border-bottom">
        <h6 class="mb-0 fw-semibold text-{{ $color }}">
            <i class="fas fa-file-pdf me-1"></i> {{ $label }}
        </h6>
    </div>
    <div class="card-body text-center">
        <div id="estado_{{ $campo }}" class="mb-3 text-muted small">—</div>
        <div class="mb-3">
            <a id="link_{{ $campo }}" href="#" target="_blank"
               class="btn btn-sm btn-outline-{{ $color }} d-none">
                <i class="fas fa-eye me-1"></i> Ver documento
            </a>
        </div>
        <small class="text-muted d-block mb-1">Solo PDF · máx 5 MB</small>
        <input type="file" class="form-control form-control-sm doc-input mb-2"
            data-tipo="{{ $campo }}" accept=".pdf">
        <button class="btn btn-sm btn-{{ $color }} w-100 btn-subir-doc"
            data-tipo="{{ $campo }}">
            <i class="fas fa-upload me-1"></i> Subir
        </button>
    </div>
</div>
