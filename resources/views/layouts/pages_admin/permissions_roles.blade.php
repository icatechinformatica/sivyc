<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <div style="text-align: right;width:60%">
            <label><h1>Registro de Pago</h1></label>
        </div>
        <br>
            <div class="form-row">
                <h2> Confirmación de Datos </h2>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" >NUEVO ROL</button>
                    <ul class="list-group">
                        <li class="list-group-item disabled">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Morbi leo risus</li>
                        <li class="list-group-item">Porta ac consectetur ac</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>
                </div>
                <div class="form-gorup col-md-6">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPermission">NUEVO PERMISO</button>
                    <ul class="list-group">
                        @foreach ($permisos as $itemPermisos)
                            <li class="list-group-item disabled">{{ $itemPermisos->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">NUEVO ROL</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                      <label for="recipient-name" class="col-form-label">NOMBRE DEL ROL:</label>
                      <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                      <label for="message-text" class="col-form-label">SLUG:</label>
                      <input type="text" class="form-control" id="slug" name="slug">
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">ESPCECIAL:</label>
                        <select class="form-control" id="special" name="special">
                            <option value="">--SELECCIONAR--</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                  <button type="button" class="btn btn-success">GUARDAR</button>
                </div>
              </div>
            </div>
        </div>


        <div class="modal fade" id="modalPermission" tabindex="-1" role="dialog" aria-labelledby="modalPermissionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalPermissionLabel">NUEVO PERMISO</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                      <label for="recipient-name" class="col-form-label">NOMBRE DEL PERMISO:</label>
                      <input type="text" class="form-control" id="name_permiso" name="name_permiso">
                    </div>
                    <div class="form-group">
                      <label for="message-text" class="col-form-label">SLUG PERMISO:</label>
                      <input type="text" class="form-control" id="slug_permiso" name="slug_permiso">
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">DESCRIPCIÓN:</label>
                        <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                  <button type="button" class="btn btn-success">GUARDAR</button>
                </div>
              </div>
            </div>
        </div>

    </section>
    <br>
@stop
