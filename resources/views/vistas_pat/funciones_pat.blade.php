<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Funciones | SIVyC Icatech')
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
                    <h1>Funciones</h1>
                </div>
                <div class="d-flex flex-column align-items-end">
                    <div class="pull-right">
                        <h3>Dirección : {{$org->nombre}}</h3>
                    </div>
                    <div class="pull-right">
                        <h3>Area Depto :  {{$area_org->nombre}}</h3>
                    </div>

                </div>
            </div>
             {{-- @can('convenios.create') --}}

        {{-- @endcan --}}
        </div>

        <div class="form-row">
            <div class="col-8">
                {{-- Formulario para busqueda --}}
                <form class="form-inline {{ isset($funcion_desc) ? 'd-none' : '' }}"  action="{{route('pat.funciones.mostrar')}}" method="get" id="formBusqueda">
                    <input type="text" class="form-control mr-sm-2" name="busqueda_funcion" id="busqueda_funcion" placeholder="BUSCAR FUNCION">
                    <button type="submit" name="botonBuscar" id="botonBuscar" class="btn btn-outline-primary">BUSCAR</button>
                </form>
                {{-- Formulario para agregar funcion --}}
                <form class="form-inline d-none"  action="" method="post" id="formAddFuncion">
                    @csrf
                    {{-- <input type="text" class="form-control mr-sm-2" name="nom_funcion" id="nom_funcion" placeholder="ESCRIBE LA FUNCION"> --}}
                    <textarea name="nom_funcion" class="form-control mr-sm-2" id="nom_funcion" cols="40" rows="6"></textarea>
                    <div class="d-flex flex-column">
                        <button type="button" name="btnCancel" id="btnCancel" class="btn btn-outline-danger">CANCELAR</button>
                    <button type="button" name="btnAddFunc" id="btnAddFunc" class="btn btn-outline-success">GUARDAR</button>
                    </div>
                </form>
                {{-- editar funcion --}}
                <form class="form-inline {{ isset($funcion_desc) ? '' : 'd-none'}}"  action="" method="post" id="formEditFuncion">
                    @csrf
                    {{-- <input type="text" class="form-control mr-sm-2" name="nom_funcion" id="nom_funcion" placeholder="ESCRIBE LA FUNCION"> --}}
                    <textarea name="nom_funcion_edit" class="form-control mr-sm-2" id="nom_funcion_edit" cols="40" rows="6">{{isset($funcion_desc) ? $funcion_desc->fun_proc : ''}}</textarea>
                    <div class="d-flex flex-column">
                    <button type="button" name="btnCancelEdit" id="btnCancelEdit" class="btn btn-outline-danger">CANCELAR</button>
                    <button type="button" name="btnEditFunc" id="btnEditFunc" class="btn btn-outline-success">GUARDAR</button>
                    </div>
                </form>

            </div>

            <div class="col-4">
                 {{-- boton de agregar nuevo --}}
                 <div class="pull-right">
                    <button class="btn btn-success btn-sm" id="btnNuevoReg"><i class="fa fa-plus fa-2x"></i></button>
                </div>
            </div>
        </div>
        @if (count($data) != 0)
            <table id="table-instructor" class="table table-bordered table-striped mt-5">
                <thead>
                    <tr>
                        <th scope=" col">#</th>
                        <th scope="col" class="col-7 v-center">LISTA DE FUNCIONES</th>
                        <th scope="col" class="text-center">AGREGAR PROCEDIMIENTO</th>
                        <th scope="col" class="text-center">MODIFICAR</th>
                        <th scope="col" class="text-center">ELIMINAR</th>
                        {{-- @can('convenios.edit')
                            <th scope="col">MODIFICAR</th>
                        @endcan --}}
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach ($data as $key=>$itemData)
                        <tr>
                            <td scope="row">{{($i+$key)}}</td>
                            <td>{{$itemData->fun_proc}}</td>
                            <td class="text-center">
                                <a class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="AGREGAR"
                                    href="{{route('pat.proced.mostrar',
                                    ['id' => $itemData->id])}}">
                                    <i class="fa fa-plus-square fa-2x mt-2" aria-hidden="true"></i>
                                </a>
                            </td>

                            {{-- @can('convenios.edit') --}}
                                <td class="text-center">
                                    <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="EDITAR" id="btnMostrarFrmEdit"
                                        href="{{ route('funciones.edit.show', ['id' => $itemData->id]) }}">
                                        <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="ELIMINAR"
                                        href="{{ route('funciones.destroy', ['id' => $itemData->id])}}">
                                        <i class="fa fa-times fa-2x mt-2" aria-hidden="true"></i>
                                    </a>
                                </td>
                            {{-- @endcan --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-success mt-5 mb-5" role="alert">
                <h4 class="alert-heading">Sin resultados!</h4>
                ¡No existe la funcion!
            </div>
        @endif

    </div>

    <div class="row py-4">
        <div class="col d-flex justify-content-center">
            {{$data->appends(request()->query())->links()}}
        </div>
    </div>

        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#btnAddFunc" ).click(function(){
                    if ($("#nom_funcion").val().trim() != '') {
                        $('#formAddFuncion').attr('action', "{{ route('funciones.guardar')}}");
                        $("#formAddFuncion").attr("target", '_self');
                        $('#formAddFuncion').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }
                });

                $("#btnEditFunc" ).click(function(){
                    //let valor_area = $("#nom_funcion_edit").val();
                    if ( $("#nom_funcion_edit").val().trim() != '') {
                        $('#formEditFuncion').attr('action', "{{ isset($funcion_desc) ? route('funciones.update', $funcion_desc->id) : ''}}");
                        $("#formEditFuncion").attr("target", '_self');
                        $('#formEditFuncion').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }
                });


           });

           //click en boton nuevo ocultamos formularios busqueda y mostramos de nuevo registro
           $("#btnNuevoReg").click(function () {
               $("#formBusqueda").addClass('d-none');
               $("#formEditFuncion").addClass('d-none');
               $("#formAddFuncion").removeClass('d-none');
           });

           $("#btnCancel").click(function () {
               $("#formAddFuncion").addClass('d-none');
               $("#formEditFuncion").addClass('d-none');
               $("#formBusqueda").removeClass('d-none');
           });
           //al dar click en editar
           $("#btnMostrarFrmEdit").click(function () {
                //$("#formEditFuncion").removeClass('d-none');
                $("#formNuevoReg").addClass('d-none');
                $("#formBusqueda").addClass('d-none');
            });
            $("#btnCancelEdit").click(function () {
                $("#formEditFuncion").addClass('d-none');
                $("#formNuevoReg").addClass('d-none');
                $("#formBusqueda").removeClass('d-none');

            });
        </script>
        @endsection
@endsection
