<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'ASIGNAR PERMISO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
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
                        <h3 class="mb-0">GESTOR DE PERMISOS A ROL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('gestor.permisos.roles.create') }}">
                    @csrf
                      <h6 class="heading-small text-muted mb-4">Información del rol</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                            @foreach ($permisos as $itemPermisos)
                                <div class="col-lg-8">
                                    <div class="custom-control custom-control-alternative custom-checkbox">


                                            <input class="custom-control-input" id="{{ $itemPermisos->id }}" name="permisos[]"
                                            type="checkbox"
                                            @foreach ($itemPermisos->roles as $item)
                                                {{( $item->pivot->permiso_id == $itemPermisos->id && $item->pivot->rol_id == $idRol ) ? 'checked': ''}}
                                            @endforeach
                                            value="{{ $itemPermisos->id }}">
                                        <label class="custom-control-label" for="{{ $itemPermisos->id }}">
                                        <span class="text-muted">{{ $itemPermisos->nombre }}</span>

                                        </label>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                      </div>
                      <br>
                      <input type="submit" value="Asignar" class="btn btn-sm btn-success">
                      <input type="hidden" name="idrole" id="idrole" value="{{ $idRol }}">
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
