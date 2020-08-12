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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">CREAR PERMISO</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('permission.store')}}" id="formPermisosCreate" name="formPermisosCreate">
                    @csrf
                      <h6 class="heading-small text-muted mb-4">Información del permiso</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="permisoName">NOMBRE</label>
                              <input type="text" id="permisoName" name="permisoName" class="form-control" >
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="permisoSlug">SLUG</label>
                              <input type="text" id="permisoSlug" name="permisoSlug" class="form-control" >
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
                              <label class="form-control-label" for="permisoDescripcion">DESCRIPCIÓN</label>
                              <textarea rows="4" class="form-control" name="permisoDescripcion" id="permisoDescripcion"></textarea>
                            </div>
                          </div>
                          <input type="submit" value="crear" class="btn btn-sm btn-success">
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

            $('#formPermisosCreate').validate({
                rules: {
                    permisoName: {
                        required: true,
                    },
                    permisoSlug: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    permisoName: {
                        required: 'Por favor ingrese su nombre del permiso',
                    },
                    permisoSlug: {
                        required: 'Por favor ingrese el slug del permiso',
                        minlength: 'Minimo se necesita una longitud de 3 caracteres'
                    }
                }
            });

        });
    </script>
@endsection
