<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR ROL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('roles.update', ['id' => base64_encode($rol->id) ]) }}" name="formRolEdit" id="formRolEdit">
                    @csrf
                    @method('PUT')
                      <h6 class="heading-small text-muted mb-4">Información del rol</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rolNameUpdate">NOMBRE</label>
                              <input type="text" id="rolNameUpdate" name="rolNameUpdate" class="form-control" value="{{ $rol->nombre }}" >
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rolSlugUpdate">RUTA CORTA</label>
                              <input type="text" id="rolSlugUpdate" name="rolSlugUpdate" class="form-control" readonly value="{{ $rol->ruta_corta }}" >
                            </div>
                          </div>

                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del contacto</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-control-label" for="rolDescripcionUpdate">DESCRIPCIÓN</label>
                              <textarea rows="4" class="form-control" name="rolDescripcionUpdate" name="rolDescripcionUpdate">{{ $rol->descripcion }}</textarea>
                            </div>
                          </div>
                          <input type="submit" value="Modificar" class="btn btn-sm btn-warning">
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

            $('#formRolEdit').validate({
                rules: {
                    rolNameUpdate: {
                        required: true,
                    }
                },
                messages: {
                    rolNameUpdate: {
                        required: 'Por favor ingrese el nombre del rol',
                    }
                }
            });

        });
    </script>
@endsection
