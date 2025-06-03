<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
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
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="recepcion" role="tabpanel" aria-labelledby="recepcion-tab">
        @include('solicitudes.instructorAspirante.partials.table', ['data' => $data, 'status' => 'ENVIADO'])
    </div>
    <div class="tab-pane fade" id="prevalidado" role="tabpanel" aria-labelledby="prevalidado-tab">
        @include('solicitudes.instructorAspirante.partials.table', ['data' => $data, 'status' => 'PREVALIDADO'])
    </div>
    <div class="tab-pane fade" id="convocado" role="tabpanel" aria-labelledby="convocado-tab">
        @include('solicitudes.instructorAspirante.partials.table', ['data' => $data, 'status' => 'CONVOCADO'])
    </div>
</div>
