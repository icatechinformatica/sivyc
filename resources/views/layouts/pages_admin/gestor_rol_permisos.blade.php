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
                        <h3 class="mb-0">GESTOR DE PERMISOS A ROL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form>
                      <h6 class="heading-small text-muted mb-4">Información del rol</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                            @foreach ($permisos as $itemPermisos)
                                <div class="col-lg-8">
                                    <div class="custom-control custom-control-alternative custom-checkbox">
                                        <input class="custom-control-input" id="{{ $itemPermisos->id }}" name="permisos_{{ $itemPermisos->id }}" type="checkbox">
                                        <label class="custom-control-label" for="{{ $itemPermisos->id }}">
                                          <span class="text-muted">{{ $itemPermisos->name }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <input type="submit" value="crear" class="btn btn-sm btn-success">
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
