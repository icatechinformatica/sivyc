<!--ELABORO ORLANDO CHAVEZ - orlando@sidmac.com.com-->
@extends('theme.sivyc.layout')
@section('title', 'Prevalidacion | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    #unidad-select {
        max-width: 300px;
        margin-bottom: 20px;
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
</style>
<div class="card-header">
    Prevalidacion de Aspirante a Instructor
</div>
<form method="GET" action="{{ route('aspirante.instructor.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <label for="unidad-select" class="form-label" style="color:#000000;font-weight:bold; margin: 0% 0% 0% 10%;">Filtrar por Unidad:</label>
            <select name="unidad" id="unidad-select" class="form-select">
                <option value="">-- Todas las Unidades --</option>
                @foreach($unidades as $unidad)
                    <option value="{{ $unidad }}" {{ request('unidad') == $unidad ? 'selected' : '' }}>
                        {{ $unidad }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>
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
    <!-- Nav Tabs Start -->
    <div id="tabs-container">
        @include('solicitudes.instructorAspirante.partials.tabs', ['data' => $data])
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
    $('#unidad-select').on('change', function() {
    var unidad = $(this).val();
    $.get("{{ route('aspirante.instructor.filter') }}", { unidad: unidad }, function(response) {
        $('#tabs-container').html(response.html);
    });
});
</script>
@endsection
