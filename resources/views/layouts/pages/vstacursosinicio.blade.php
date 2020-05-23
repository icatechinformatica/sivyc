<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('Cursos', 'SUPRE | SIVyC Icatech')
<!--seccion-->
@section('content')
    <style>
        * {
        box-sizing: border-box;
        }

        #myInput {
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        }
    </style>
    <div class="container g-pt-50">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Catalogo de Cursos</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('frm-cursos')}}"> Nuevo</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered Datatables">
            <caption>Catalogo de Cursos</caption>
            <thead>
                <tr>
                    <th scope="col">Especialidad</th>
                    <th scope="col">Curso</th>
                    <th scope="col">Duración</th>
                    <th scope="col">Modalidad</th>
                    <th scope="col">Clasificación</th>
                    <th scope="col">Costo</th>
                    <th scope="col">Acciones</th>
                    <th scope="col">Registros</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->nombre}}</th>
                        <td>{{$itemData->nombre_curso}}</td>
                        <td>{{$itemData->duracion}}</td>
                        <td>{{$itemData->modalidad}}</td>
                        <td>{{$itemData->clasificacion}}</td>
                        <td>{{$itemData->costo}}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="Editar Registro">
                                <i class="fa fa-wrench" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                data-toggle="modal" data-placement="top"
                                title="Información del Registro"
                                data-target="#fullHeightModalRight"
                                data-id="{{$itemData->id}}">
                                <i class="fa fa-info"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <!-- Full Height Modal Right -->
<div class="modal fade right" id="fullHeightModalRight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

  <!-- Add class .modal-full-height and then add class .modal-right (or other classes from list above) to set a position to the modal -->
  <div class="modal-dialog modal-full-height modal-right" role="document">


    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title w-100" id="myModalLabel">Modal title</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Full Height Modal Right -->
    </div>
    <br>
@endsection
