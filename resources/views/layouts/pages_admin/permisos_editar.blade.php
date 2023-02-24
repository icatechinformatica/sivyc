<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'CREAR PERMISO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR PERMISO</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('permiso.update', ['id' => base64_encode($permiso->id) ]) }}" name="formPermisoEdit" id="formPermisoEdit">
                    @csrf
                    @method('PUT')
                      <h6 class="heading-small text-muted mb-4">Información del permiso</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="permisoNameEdit">NOMBRE</label>
                              <input type="text" id="permisoNameEdit" name="permisoNameEdit" class="form-control" value="{{ $permiso->name }}">
                            </div>
                          </div>
{{--  --}}
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="permisoSlugEdit">SLUG</label>
                              <input type="text" id="permisoSlugEdit" name="permisoSlugEdit" class="form-control" value="{{ $permiso->slug }}" readonly>
                            </div>
                          </div>

                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del permiso</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-control-label" for="permisoDescripcionEdit">DESCRIPCIÓN</label>
                              <textarea rows="4" class="form-control" name="permisoDescripcionEdit" id="permisoDescripcionEdit">{{ $permiso->description}}</textarea>
                            </div>
                          </div>
                          <input type="submit" value="Actualizar" class="btn btn-sm btn-warning">
                        </div>
                      </div>
                    </form>
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
    <script type="text/javascript">
        $(function(){

            $('#formPermisoEdit').validate({
                rules: {
                    permisoNameEdit: {
                        required: true,
                    },
                    permisoSlugEdit: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    permisoNameEdit: {
                        required: 'Por favor ingrese su nombre del permiso',
                    },
                    permisoSlugEdit: {
                        required: 'Por favor ingrese el slug del permiso'
                    }
                }
            });

        });
    </script>
@endsection
