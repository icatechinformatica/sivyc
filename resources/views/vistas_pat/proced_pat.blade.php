<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Procedimientos | SIVyC Icatech')
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

    <div class="container-fluid px-5 g-pt-30">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h1>Procedimientos</h1>
                </div>
                <div class="d-flex flex-column align-items-end">
                    <div class="pull-right">
                        <h3>Dirección : {{isset($org->nombre) ? $org->nombre : ''}}</h3>
                    </div>
                    <div class="pull-right">
                        <h3>Area Depto : {{isset($area->nombre) ? $area->nombre : ''}}</h3>
                    </div>
                </div>
            </div>
             {{-- @can('convenios.create') --}}

        {{-- @endcan --}}
        </div>

        <div class="form-row">
            <div class="col-8">

                {{-- Formulario para agregar procedimiento --}}
                <form class="form-inline d-none"  action="" method="post" id="formAddProced">
                    @csrf
                    {{-- <input type="text" class="form-control mr-sm-2" name="nom_funcion" id="nom_funcion" placeholder="ESCRIBE LA FUNCION"> --}}
                    <textarea name="nom_proced" class="form-control mr-sm-2" id="nom_proced" cols="40" rows="6"></textarea>
                    <div class="d-flex flex-column">
                        <button type="button" name="btnCancel" id="btnCancel" class="btn btn-outline-danger">CANCELAR</button>
                    <button type="button" name="btnAddProced" id="btnAddProced" class="btn btn-outline-success">GUARDAR</button>
                    </div>
                </form>
                {{-- editar procedimiento --}}
                <form class="form-inline {{isset($dataedit) ? '' : 'd-none'}}"  action="" method="post" id="formEditProced">
                    @csrf
                    {{-- <input type="text" class="form-control mr-sm-2" name="nom_funcion" id="nom_funcion" placeholder="ESCRIBE LA FUNCION"> --}}
                    <textarea name="nom_proced_edit" class="form-control mr-sm-2" id="nom_proced_edit" cols="40" rows="6">{{isset($dataedit) ? $dataedit->fun_proc : ''}}</textarea>
                    <div class="d-flex flex-column">
                    <button type="button" name="btnCancelEdit" id="btnCancelEdit" class="btn btn-outline-danger">CANCELAR</button>
                    <button type="button" name="btnEditProced" id="btnEditProced" class="btn btn-outline-success">GUARDAR</button>
                    </div>
                </form>

            </div>

            <div class="col-4">
                 {{-- boton de agregar nuevo --}}
                 <div class="pull-right">
                    <button class="btn btn-success btn-sm" id="btnNuevoProced"><i class="fa fa-plus fa-2x"></i></button>
                </div>
            </div>
        </div>

        <div>
            <div class="col-7">
                <b>Función</b>
                <p class="text-left">{{$data->fun_proc}}</p>
            </div>
            {{-- en caso de que no haya registros no mostrar --}}
            @if (count($data2) != 0)
                <table id="table-instructor" class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th scope=" col">#</th>
                            <th scope="col" class="col-7 v-center">PROCEDIMIENTOS</th>
                            <th scope="col" class="text-center">MODIFICAR</th>
                            <th scope="col" class="text-center">ELIMINAR</th>
                            {{-- @can('convenios.edit')
                                <th scope="col">MODIFICAR</th>
                            @endcan --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach ($data2 as $key=>$itemData)
                            <tr>
                                <td scope="row">{{$i+$key}}</td>
                                <td>{{$itemData->fun_proc}}</td>

                                {{-- @can('convenios.edit') --}}
                                    <td class="text-center">
                                        <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="EDITAR" id="btnMostrarFrmEdit"
                                            href="{{ route('pat.proced.edit.show', ['idedi' => $itemData->id, 'id' => $id])}}">
                                            <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="ELIMINAR"
                                            href="{{ route('proced.destroy', ['idd' => $itemData->id,'id' => $id])}}">
                                            <i class="fa fa-times fa-2x mt-2" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                {{-- @endcan --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Sin resultados!</h4>
                    ¡No existen procedimientos en esta función!
                </div>
            @endif

            <div class="pull-left">
                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                    data-placement="top" title="REGRESAR" href="{{ route('pat.funciones.mostrar')}}">
                    <i class="fa fa-arrow-left fa-2x mt-2" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- <div class="row py-4">
        <div class="col d-flex justify-content-center">
            {{$data2->appends(request()->query())->links()}}
        </div>
    </div> --}}

        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                $("#btnAddProced" ).click(function(){
                    if ($("#nom_proced").val().trim() != '') {
                        $('#formAddProced').attr('action', "{{ route('proced.guardar', ['id' => $id])}}");
                        $("#formAddProced").attr("target", '_self');
                        $('#formAddProced').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }
                });

                $("#btnEditProced" ).click(function(){
                    if ($("#nom_proced_edit").val().trim() != '') {
                        $('#formEditProced').attr('action', "{{ isset($dataedit) ? route('proced.update', ['idedi' => $dataedit->id, 'id' => $id]) : '' }}");
                        $("#formEditProced").attr("target", '_self');
                        $('#formEditProced').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }
                });


           });

           $("#btnNuevoProced").click(function () {
               $("#formAddProced").removeClass('d-none');
               $("#formEditProced").addClass('d-none');
           });

           $("#btnCancel").click(function () {
               $("#formAddProced").addClass('d-none');
               $("#formEditProced").addClass('d-none');
           });
           $("#btnCancelEdit").click(function () {
               $("#formAddProced").addClass('d-none');
               $("#formEditProced").addClass('d-none');
           });

        </script>
        @endsection
@endsection
