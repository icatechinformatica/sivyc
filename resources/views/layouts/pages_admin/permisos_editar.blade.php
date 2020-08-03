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
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form>
                      <h6 class="heading-small text-muted mb-4">Información del permiso</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rol-name">nombre</label>
                              <input type="text" id="permiso-name" class="form-control" value="{{ $permiso->name }}">
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="rol-slug">slug</label>
                              <input type="text" id="permiso-slug" class="form-control" value="{{ $permiso->slug }}">
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
                              <label class="form-control-label" for="rol-descripcion">descripcion</label>
                              <textarea rows="4" class="form-control" name="permiso-descripcion">{{ $permiso->description}}</textarea>
                            </div>
                          </div>
                          <input type="submit" value="Actualizar" class="btn btn-sm btn-info">
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
