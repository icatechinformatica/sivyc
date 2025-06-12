<!--ELABORO ORLANDO CHAVEZ - orlando@sidmac.com.com-->
@extends('theme.sivyc.layout')
@section('title', 'Prevalidacion | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    #unidad-select {
        max-width: 300px;
        margin-bottom: 0 !important;
        /* Remove extra margin if any */
        border-radius: 0.5rem;
        border: 1px solid #000000;
        font-size: 1.1rem;
        color: #000000;
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(179,0,92,0.08);
        transition: border-color 0.2s;
    }
    #unidad-select:focus {
        border-color: #000000;
        box-shadow: 0 0 0 0.2rem rgba(179,0,92,0.15);
        outline: none;
    }
    /* Custom slider switch style */
    .form-switch .form-check-input {
        width: 50px;
        height: 26px;
        background-color: #e9ecef;
        border-radius: 26px;
        position: relative;
        transition: background-color 0.3s;
        box-shadow: none;
        border: 1px solid #b3005c;
    }

    .form-switch .form-check-input:checked {
        background-color: #b3005c;
        border-color: #b3005c;
    }

    .form-switch .form-check-input:before {
        content: "";
        position: absolute;
        top: 3px;
        left: 4px;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }

    .form-switch .form-check-input:checked:before {
        transform: translateX(24px);
    }

    /* Center the label text with the switch */
    .form-switch .form-check-label {
        margin-bottom: 0;
        font-weight: bold;
        color: #000000;
        cursor: pointer;
        display: flex;
        align-items: center;
        height: 26px;
        min-width: 170px; /* or adjust as needed */
    }
</style>
<div class="card-header" style="color: white;">
    Prevalidacion de Aspirante a Instructor
</div>
<div class="card card-body" style=" min-height:450px;">
    @if (Session::has('success'))
        <div class="alert alert-info alert-block">
            <strong>{{ Session::get('success') }}</strong>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger alert-block">
            <strong>{{ Session::get('error') }}</strong>
        </div>
    @endif
    <form method="GET" action="{{ route('aspirante.instructor.index') }}" class="mb-3">
        <div class="row mb-3">
            <div class="col-md-6 d-flex align-items-center">
                <label for="unidad-select" class="mb-0 me-3" style="color:#000000;font-weight:bold; white-space:nowrap;">
                    Filtrar por Unidad:
                </label>
                <select name="unidad" id="unidad-select" class="form-select" style="max-width: 200px;">
                    <option value="">-- Todas las Unidades --</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad }}" {{ request('unidad') == $unidad ? 'selected' : '' }}>
                            {{ $unidad }}
                        </option>
                    @endforeach
                </select>
            </div>
    </form>
            <div class="col-md-6 d-flex justify-content-end">
                <form id="export-form" method="GET" action="{{ route('aspirante.instructor.export') }}">
                    <input type="hidden" name="unidad" id="export-unidad" value="{{ request('unidad') }}">
                    <input type="hidden" name="status" id="export-status" value="ENVIADO">
                    <input type="hidden" name="showRechazados" id="export-showRechazados" value="0">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-file-excel"></i> Exportar Excel
                    </button>
                </form>
            </div>
        </div>
        @can('admins.conteo.aspirantes.instructor')
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <p class="mb-0 me-3" style="color:#000000; white-space:nowrap;">
                        <b>Corte del día:</b> Se han registrado <b>{{ $total_aspirantes }} </b> aspirantes, de los cuales, <b>{{ $total_enviados }}</b> han enviado su informacion a DTA.
                    </p>
                </div>
            </div>
        @endcan
    <!-- Nav Tabs Start -->
    <div id="tabs-container">
        @include('solicitudes.instructorAspirante.partials.tabs', ['data' => $data, 'especialidades' => $especialidades])
    </div>
    <!-- Nav Tabs End -->
</div>

<!-- Modal -->
<div class="modal fade" id="prevalidarModal" tabindex="-1" aria-labelledby="prevalidarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.prevalidar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="prevalidarModalLabel">Prevalidar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea prevalidar este registro?
          <input type="hidden" id="prevalidar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-prevalidar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-danger">Prevalidar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Cotejar Modal -->
<div class="modal fade" id="cotejarModal" tabindex="-1" aria-labelledby="cotejarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.cotejar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="cotejarModalLabel">Cotejar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea cotejar este registro?
          <input type="hidden" id="cotejar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-cotejar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Cotejar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Aprobar Modal -->
<div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.aprobar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="aprobarModalLabel">Aprobar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea aprobar este registro?
          <input type="hidden" id="aprobar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-aprobar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Aprobar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Rechazo Modal (shared for both statuses) -->
<div class="modal fade" id="rechazoModal" tabindex="-1" aria-labelledby="rechazoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.rechazar') }}">
        @csrf
        <input type="hidden" id="rechazar-context" name="context" value="ENVIADO">
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="rechazoModalLabel">Rechazar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <input type="hidden" id="rechazar-id" name="id" value="">
          <div class="mb-3">
            Aspirante seleccionado: <b><span id="show-rechazar-name"></span></b>
          </div>
          <div class="mb-3">
            <label for="observacion-rechazo" class="form-label">Observación de rechazo:</label>
            <textarea class="form-control" id="observacion-rechazo" name="observacion" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    $(document).on('click', '.prevalidar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#prevalidar-id').val(id);
        $('#show-prevalidar-name').text(name);
        $('#prevalidarModal').modal('show');
    });
    $(document).on('click', '.cotejar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#cotejar-id').val(id);
        $('#show-cotejar-name').text(name);
        $('#cotejarModal').modal('show');
    });
    $(document).on('click', '.aprobar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#aprobar-id').val(id);
        $('#show-aprobar-name').text(name);
        $('#aprobarModal').modal('show');
    });
    // For ENVIADO
    $(document).on('click', '.rechazar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#rechazar-id').val(id);
        $('#show-rechazar-name').text(name);
        $('#observacion-rechazo').val('');
        $('#rechazar-context').val('ENVIADO');
        $('#rechazoModal').modal('show');
    });
    // For PREVALIDADO
    $(document).on('click', '.rechazar-prevalidado-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#rechazar-id').val(id);
        $('#show-rechazar-name').text(name);
        $('#observacion-rechazo').val('');
        $('#rechazar-context').val('PREVALIDADO');
        $('#rechazoModal').modal('show');
    });
    // For CONVOCADO
    $(document).on('click', '.rechazar-convocado-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#rechazar-id').val(id);
        $('#show-rechazar-name').text(name);
        $('#observacion-rechazo').val('');
        $('#rechazar-context').val('CONVOCADO');
        $('#rechazoModal').modal('show');
    });
    function attachRechazadosSwitchHandler() {
    $('#showRechazadosSwitch, #unidad-select').off('change').on('change', function() {
        var activeTabId = $('#myTab .nav-link.active').attr('id');
        var unidad = $('#unidad-select').val();
        var showRechazados = $('#showRechazadosSwitch').is(':checked') ? 1 : 0;
        // Get current status from active tab
        var status = 'ENVIADO';
        if ($('#prevalidado-tab').hasClass('active')) status = 'PREVALIDADO';
        if ($('#convocado-tab').hasClass('active')) status = 'CONVOCADO';

        $.get("{{ route('aspirante.instructor.filter') }}", { unidad: unidad, showRechazados: showRechazados, status: status }, function(response) {
            $('#tabs-container').html(response.html);
            $('#showRechazadosSwitch').prop('checked', showRechazados === 1);
            attachRechazadosSwitchHandler();
            if (activeTabId) {
                var newActiveTab = $('#' + activeTabId);
                if (newActiveTab.length) {
                    newActiveTab.tab('show');
                }
            }
        });
    });
}

// Attach on first load
$(document).ready(function() {
    attachRechazadosSwitchHandler();
});
</script>
<script>
    function updateExportForm() {
        $('#export-unidad').val($('#unidad-select').val());
        $('#export-showRechazados').val($('#showRechazadosSwitch').is(':checked') ? 1 : 0);
        // Get active tab status
        let status = 'ENVIADO';
        if ($('#prevalidado-tab').hasClass('active')) status = 'PREVALIDADO';
        if ($('#convocado-tab').hasClass('active')) status = 'CONVOCADO';
        $('#export-status').val(status);
    }

    $('#export-form').on('submit', function() {
        updateExportForm();
    });

    // Also update on tab change
    $('#myTab .nav-link').on('shown.bs.tab', function() {
        updateExportForm();
    });
</script>
@endsection
