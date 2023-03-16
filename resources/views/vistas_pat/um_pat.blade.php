<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Unidades de Medida | SIVyC Icatech')
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
                    <h1>Unidades de Medida</h1>
                </div>
                {{-- @can('convenios.create') --}}
                    <div class="pull-right">
                        <button class="btn btn-success btn-sm" id="btnNuevoReg"><i class="fa fa-plus fa-2x"></i></button>
                    </div>
                {{-- @endcan --}}
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                {{-- Formulario para busqueda --}}
                <form class="form-inline {{ isset($unidad) ? 'd-none' : '' }}" action="{{ route('pat.unidadesmedida.mostrar') }}" method="get" id="formBusqueda">
                    <input type="text" class="form-control mr-sm-2" name="busqueda_unidad" id="busqueda_unidad" placeholder="Unidad de Medida">
                    <button type="submit" name="botonBuscar" id="botonBuscar" class="btn btn-outline-primary">BUSCAR</button>
                </form>
                {{-- Formulario para agregar nuevo registro --}}
                <form class="form-inline d-none" action="" method="post" id="formNuevoReg">
                    @csrf
                    <input type="number" class="form-control mr-sm-2" name="numero_unidad" id="numero_unidad" placeholder="Numero">
                    <input type="text" class="form-control mr-sm-2" name="nombre_unidad" id="nombre_unidad" placeholder="Unidad de Medida">
                    <input type="text" name="tipo_unidad" id="tipo_unidad" class="form-control mr-sm-2" placeholder="TIPO DE UNIDAD">
                    <button type="button" name="botonGuardar" id="botonGuardar" class="btn btn-outline-success">GUARDAR</button>
                    <button type="button" name="botonCancelar" id="botonCancelar" class="btn btn-outline-danger">CANCELAR</button>
                </form>
                {{-- formulario para editar registro --}}
                <form class="form-inline d-none" action="" method="post" id="formEditReg">
                    @csrf
                    <input type="number" value="" class="form-control mr-sm-2" name="numero_unidad_edit" id="numero_unidad_edit" placeholder="Numero">
                    <input type="text" value="" class="form-control mr-sm-2" name="nombre_unidad_edit" id="nombre_unidad_edit" placeholder="Unidad de Medida">
                    <input type="text" name="tipo_unidad_edit" id="tipo_unidad_edit" class="form-control mr-sm-2" placeholder="TIPO DE UNIDAD">
                    <input type="hidden" name="id_user_edit">
                    <button type="button" name="botonGuardarEdit" id="botonGuardarEdit" class="btn btn-outline-success">GUARDAR</button>
                    <button type="button" name="botonCancelarEdit" id="botonCancelarEdit" class="btn btn-outline-danger">CANCELAR</button>
                </form>
            </div>
        </div>
        @if (count($data) != 0)
            <table id="table-instructor" class="table table-bordered table-striped mt-5">
                <thead>
                    <tr>
                        <th scope="col">UM</th>
                        <th scope="col" class="col-7 v-center">UNIDAD DE MEDIDA</th>
                        {{-- <th width="150px">FECHA DE ACTUALIZACIÓN</th> --}}
                        <th scope="col" class="text-center">TIPO DE UM</th>
                        <th scope="col" class="text-center">MODIFICAR</th>
                        <th scope="col" class="text-center">DESACTIVAR/ACTIVAR</th>
                        {{-- @can('convenios.edit')
                            <th scope="col">MODIFICAR</th>
                        @endcan --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $itemData)
                        <tr>
                            <td scope="row">{{$itemData->numero}}</td>
                            <td>{{$itemData->unidadm}}</td>
                            <td class="text-center">{{$itemData->tipo_unidadm}}</td>
                            {{-- @can('convenios.edit') --}}
                                <td class="text-center">
                                    {{-- <a class="btn btn-warning btn-circle m-1 btn-circle-sm hola" data-toggle="tooltip"
                                        data-placement="top" title="EDITAR" id="btnMostrarFrmEdit"
                                        href="{{ route('unidadesm.edit.show', ['id' => $itemData->id]) }}"
                                        >
                                        <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                    </a> --}}
                                    <button class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                        onclick="editRegistro({{$itemData->id}})">
                                        <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    {{-- <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="ELIMINAR"
                                        href="{{ route('unidadesm.destroy', ['id' => $itemData->id])}}">
                                        <i class="fa fa-times fa-2x mt-2" aria-hidden="true"></i>
                                    </a> --}}
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="customSwitch{{$itemData->id}}"
                                            onchange="switchSel({{$itemData->id}})"
                                            {{$itemData->status == 'activo' ? 'checked' : ''}}>
                                        <label class="custom-control-label" for="customSwitch{{$itemData->id}}"></label>
                                    </div>
                                </td>

                            {{-- @endcan --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-success mt-5 mb-5" role="alert">
                <h4 class="alert-heading">Sin resultados!</h4>
                ¡No existe la unidad de medida!
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
                $("#botonGuardar" ).click(function(){
                    if ($("#numero_unidad").val().trim() != '' && $("#nombre_unidad").val().trim() != '' && $("#tipo_unidad").val().trim() != '') {
                        $('#formNuevoReg').attr('action', "{{ route('unidadesm.guardar') }}");
                        $("#formNuevoReg").attr("target", '_self');
                        $('#formNuevoReg').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }
                });
                    //Editar datos
                $("#botonGuardarEdit" ).click(function(){
                    if ($("#numero_unidad_edit").val().trim() != '' && $("#nombre_unidad_edit").val().trim() != '' && $("#tipo_unidad_edit").val().trim() != '' ) {
                        $('#formEditReg').attr('action', "{{ isset($unidad) ? route('unidadesm.update', $unidad->id) : ''}}");
                        $("#formEditReg").attr("target", '_self');
                        $('#formEditReg').submit();
                    } else {
                        alert('TERMINA DE LLENAR LOS CAMPOS')
                    }

                });
                //Obtener el href de editar con metodo get
                // $(".hola").click(function(event) {
                //     var href = $('.hola').attr('href');

                //     let data = {
                //         // "_token": $("meta[name='csrf-token']").attr("content"),
                //         "id": id,
                //     }
                //     event.preventDefault();
                //     $.ajax({
                //         type:"get",
                //         url: href,
                //         data: data,
                //         dataType: "json",
                //         success: function (response) {
                //             console.log(response);
                //         }
                //     });
                // });



            });
            function switchSel(id) {
                let status = '';
                if(document.getElementById("customSwitch"+id).checked==true) status = 'activo';
                else status = 'inactivo';

                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "id": id,
                        "status": status,
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('unidadesm.mod.status') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                        }
                    });
            }

            function editRegistro(id) {
                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "id": id,
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('unidadesm.edit.show') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            //console.log(response);
                            //console.log('Status: '+response.datos.status);
                            $("#formNuevoReg").addClass('d-none');
                            $("#formBusqueda").addClass('d-none');
                            $("#formEditReg").removeClass('d-none');
                            $("#numero_unidad_edit").val(response.datos.numero);
                            $("#nombre_unidad_edit").val(response.datos.unidadm);
                            $("#tipo_unidad_edit").val(response.datos.tipo_unidadm);
                        }
                    });
            }

            //click en boton nuevo ocultamos formularios busqueda y mostramos de nuevo registro
            $("#btnNuevoReg").click(function () {
                $("#formBusqueda").addClass('d-none');
                $("#formEditReg").addClass('d-none');

                $("#formNuevoReg").removeClass('d-none');
            });
            $("#botonCancelar").click(function () {
                $("#formNuevoReg").addClass('d-none');
                $("#formEditReg").addClass('d-none');
                $("#formBusqueda").removeClass('d-none');

            });

            // $("#btnMostrarFrmEdit").click(function () {
            //     $("#formNuevoReg").addClass('d-none');
            //     $("#formBusqueda").addClass('d-none');

            // });

            $("#botonCancelarEdit").click(function () {
                $("#formNuevoReg").addClass('d-none');
                $("#formEditReg").addClass('d-none');
                $("#formBusqueda").removeClass('d-none');
            });
        </script>
        @endsection
@endsection
