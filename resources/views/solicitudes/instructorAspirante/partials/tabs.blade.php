<div class="d-flex justify-content-between align-items-center mb-3">
    <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-bottom:0;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="recepcion-tab" data-bs-toggle="tab" data-bs-target="#recepcion" type="button" role="tab" aria-controls="recepcion" aria-selected="true">
                RECEPCION
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prevalidado-tab" data-bs-toggle="tab" data-bs-target="#prevalidado" type="button" role="tab" aria-controls="prevalidado" aria-selected="false">
                PREVALIDADO
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="convocado-tab" data-bs-toggle="tab" data-bs-target="#convocado" type="button" role="tab" aria-controls="convocado" aria-selected="false">
                CONVOCADO
            </button>
        </li>
    </ul>
    <div class="form-check form-switch ms-3 d-flex align-items-center">
        <label class="form-check-label me-2" for="showRechazadosSwitch">
            Mostrar Rechazados
        </label>
        <input class="form-check-input" type="checkbox" id="showRechazadosSwitch">
    </div>
</div>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="recepcion" role="tabpanel" aria-labelledby="recepcion-tab">
        @include('solicitudes.instructorAspirante.partials.table', [
            'data' => $data,
            'especialidades' => $especialidades,
            'status' => 'ENVIADO',
            'showRechazados' => $showRechazados ?? false
        ])
    </div>
    <div class="tab-pane fade" id="prevalidado" role="tabpanel" aria-labelledby="prevalidado-tab">
        @include('solicitudes.instructorAspirante.partials.table', [
            'data' => $data,
            'especialidades' => $especialidades,
            'status' => 'PREVALIDADO',
            'showRechazados' => $showRechazados ?? false
        ])
    </div>
</div>
