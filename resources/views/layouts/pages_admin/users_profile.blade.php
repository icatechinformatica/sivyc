<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
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

                        </div>
                      </div>
                    </div>
                    <div class="text-center">
                      <h5 class="h3">
                        {{ $usuario->name }}
                      </h5>
                      <div class="h5 font-weight-300">
                        <i class="ni location_pin mr-2"></i>{{ $usuario->email }}
                      </div>
                      <div class="h5 mt-4">
                        <i class="ni business_briefcase-24 mr-2"></i>CARGO:
                      </div>
                      <div>
                        <i class="ni education_hat mr-2"></i>{{ $usuario->puesto }}
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="col-xl-8 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR PERFIL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form>
                      <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="input-email">Correo Electrónico</label>
                              <input type="email" id="input-email" class="form-control" readonly value="{{ $usuario->email }}">
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="input-first-name">Nombre</label>
                              <input type="text" id="input-first-name" class="form-control" value="{{ $usuario->name }}">
                            </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="input-first-name">Contraseña</label>
                              <input type="password" id="input-first-name" class="form-control">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="input-last-name">Repetir Contraseña</label>
                              <input type="password" id="input-last-name" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del contacto</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-8">
                            <div class="form-group">
                              <label class="form-control-label" for="input-address">PUESTO</label>
                              <input id="input-address" class="form-control" value="{{ $usuario->puesto }}" type="text">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="form-control-label" for="input-city">UNIDAD</label>
                              <input type="text" id="input-city" class="form-control" placeholder="City" value="New York">
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
