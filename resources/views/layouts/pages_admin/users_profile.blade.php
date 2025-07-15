<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
<style>
  .info-label {
    font-weight: 600;
    color: #8898aa;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
  }

  .info-value {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fe;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
  }

  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .status-active {
    background-color: #d4edda;
    color: #155724;
  }

  .status-inactive {
    background-color: #f8d7da;
    color: #721c24;
  }

  #status-info {
    cursor: pointer;
  }
</style>
<div class="container-fluid mt--6">
  <div class="row">

    <div class="col-xl-4 order-xl-2">
      <div class="card card-profile">
        <div class="row justify-content-center">
          <div class="col-lg-3 order-lg-2">
            <div class="card-profile-image">
              <a href="#">
                <img src="{{asset("img/blade_icons/nophoto.png")}}" class="rounded-circle">
              </a>
            </div>
          </div>
        </div>
        <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
          <div class="d-flex justify-content-between">
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="row">
            <div class="col">
              <div class="card-profile-stats d-flex justify-content-center">
                {{ $usuario->name }}
              </div>
            </div>
          </div>
          <div class="text-center">
            <div class="h5 font-weight-300">
              <i class="ni location_pin mr-2"></i>{{ $usuario->email }}
            </div>
            <div class="h5 mt-4"> <i class="ni business_briefcase-24 mr-2"></i>CARGO:</div>
            <div> <i class="ni education_hat mr-2"></i>{{ $usuario->puesto }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-8 order-xl-1">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-8">
              <h3 class="mb-0">PERFIL DE USUARIO</h3>
            </div>
            <div class="col-4 text-right">
              <a href="{{ route('usuario_permisos.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="info-label">Correo Electrónico</label>
                  <div class="info-value">{{ $usuario->email }}</div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label class="info-label">Nombre</label>
                  <div class="info-value">{{ $usuario->name }}</div>
                </div>
              </div>

            </div>
            {{-- teléfono y curp --}}
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="info-label">CURP</label>
                  <div class="info-value">{{ $usuario->curp ?: 'No especificado' }}</div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label class="info-label">Teléfono</label>
                  <div class="info-value">{{ $usuario->telefono ?: 'No especificado' }}</div>
                </div>
              </div>
            </div>

            <!-- Estatus -->
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="info-label">Estatus</label>
                  <div>
                    <span class="status-badge @if($usuario->activo) status-active @else status-inactive @endif"
                      id="status-info">
                      @if($usuario->activo) ACTIVO @else INACTIVO @endif
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr class="my-4" />
          <!-- Información del contacto -->
          <h6 class="heading-small text-muted mb-4">Información del contacto</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <label class="info-label">PUESTO</label>
                  <div class="info-value">{{ $usuario->puesto ?: 'No especificado' }}</div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="info-label">UNIDAD</label>
                  <div class="info-value">{{ $ubicacion->ubicacion ?? 'No especificado' }}</div>
                </div>
              </div>
              <!--información de las unidades-->
              <div class="col-md-6">
                <div class="form-group">
                  <label class="info-label">Unidades de capacitación</label>
                  <div class="info-value">{{ $ubicacion->unidad ?? 'No especificado' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER PORTAL DE GOBIERNO -->
  @include("theme.sivyc_admin.footer")
  <!-- FOOTER PORTAL DE GOBIERNO END-->
</div>
@endsection
@section('scripts_content')
<script>
  // Aquí puedes agregar cualquier script adicional si es necesario
  document.addEventListener('DOMContentLoaded', function() {
    $('#status-info').on('click', function() {
        $.ajax({
          url: '{{ route("usuario.toggle.activo") }}',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            usuario_id: '{{ $usuario->id }}',
          },
          success: function(response) {
            if (response.success) {
              location.reload();
            } else {
              alert('No se pudo cambiar el estatus.');
            }
          },
          error: function(e) {
            alert(e.responseJSON.message || 'Error al cambiar el estatus.');
          }
        });
      });
  });
</script>
@endsection