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
                        <h3 class="mb-0">CREAR ROL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('roles.store') }}" id="formRol">
                    @csrf
                      <h6 class="heading-small text-muted mb-4">Información del rol</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rolName">NOMBRE</label>
                              <input type="text" id="rolName" name="rolName" class="form-control" >
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rolSlug">SLUG</label>
                              <input type="text" id="rolSlug" name="rolSlug" class="form-control" >
                            </div>
                          </div>

                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del rol</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-control-label" for="rolDescripcion">DESCRIPCIÓN</label>
                              <textarea rows="4" class="form-control" id="rolDescripcion" name="rolDescripcion"></textarea>
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

            $('#formRol').validate({
                rules: {
                    rolName: {
                        required: true,
                    },
                    rolSlug: {
                        required: true,
                    }
                },
                messages: {
                    rolName: {
                        required: 'Por favor ingrese el nombre del rol',
                    },
                    rolSlug: {
                        required: 'Por favor ingrese el slug'
                    }
                }
            });

        });
    </script>
@endsection
