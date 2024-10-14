<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Funciones | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }
        .card-header{
                font-variant: small-caps;
                background-color: #621132;
                color: white;
                margin: 1.7% 1.7% 1% 1.7%;
                padding: 1.3% 39px 1.3% 39px;
                font-style: normal;
                font-size: 22px;
            }

            .card-body{
                margin: 1%;
                margin-left: 1.7%;
                margin-right: 1.7%;
                /* padding: 55px; */
                -webkit-box-shadow: 0 8px 6px -6px #999;
                -moz-box-shadow: 0 8px 6px -6px #999;
                box-shadow: 0 8px 6px -6px #999;
            }
            .card-body.card-msg{
                background-color: yellow;
                margin: .5% 1.7% .5% 1.7%;
                padding: .5% 5px .5% 25px;
            }

            body { background-color: #E6E6E6; }

            .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
            .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

            .form-control { height: 40px; }

            .color_fondo {
                background-color:  #e7e7e7;
            }
            /* Quitamos la parte de mayusculas de manera forzada */
            input[type=text],
            select,
            textarea {
                text-transform: none !important;
            }

    </style>

    <div class="card-header py-2">
        <h3>Funciones</h3>
    </div>

    {{-- card que contiene todo el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div class="container-fluid px-5 pt-3">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <select name="sel_organismos_id" id="sel_organismos_id" class="form-control" onchange="cambiar_org()">
                                {{-- <option value="">SELECCIONAR ORGANISMO</option> --}}
                                @foreach ($list_org as $item)
                                    <option {{$organismo == $item->id ? 'selected' : ''}} value="{{$item->id}}">{{$item->nombre}}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="d-flex">
                            <h4><strong>Dirección :</strong> {{$org->nombre}}</h4>
                        </div>

                    </div>
                    <div class="d-flex align-items-end">
                        <div class="ml-auto">
                            <h4><strong>Area/Depto :</strong>  {{$area_org->nombre}}</h4>
                        </div>
                    </div>
                </div>
            </div>



            <div class="form-row">
                <div class="col-8">
                    {{-- Formulario para busqueda --}}
                    <form class="form-inline {{ isset($funcion_desc) ? 'd-none' : '' }}"  action="{{route('pat.funciones.mostrar')}}" method="get" id="formBusqueda">
                        <input type="text" class="form-control mr-sm-2" name="busqueda_funcion" id="busqueda_funcion" placeholder="BUSCAR FUNCION">
                        <input type="hidden" name="id_orgbus" value="{{$organismo}}">
                        <button type="submit" name="botonBuscar" id="botonBuscar" class="btn">BUSCAR</button>
                    </form>

                    {{-- editar funcion --}}
                    <form class="form-inline {{ isset($funcion_desc) ? '' : 'd-none'}}"  action="" method="post" id="formEditFuncion">
                        @csrf
                        {{-- <input type="text" class="form-control mr-sm-2" name="nom_funcion" id="nom_funcion" placeholder="ESCRIBE LA FUNCION"> --}}
                        <textarea name="nom_funcion_edit" class="form-control mr-sm-2" id="nom_funcion_edit" cols="35" rows="4">{{isset($funcion_desc) ? $funcion_desc->fun_proc : ''}}</textarea>
                        <input type="hidden" name="idorgupd" value="{{$organismo}}">
                        <div class="d-flex flex-column">
                        <button type="button" name="btnCancelEdit" id="btnCancelEdit" class="btn btn-danger mb-1">CANCELAR</button>
                        <button type="button" name="btnEditFunc" id="btnEditFunc" class="btn mt-1">GUARDAR</button>
                        </div>
                    </form>

                </div>

                <div class="col-4">
                     {{-- boton de agregar nuevo --}}
                    <div class="pull-right">
                        <a class="btn btn-success py-1 px-2 mt-2" data-toggle="tooltip"
                            data-placement="top" title="NUEVO REGISTRO" href="#" id="btnNuevoReg">
                            {{-- <i class="fa fa-plus fa-2x" aria-hidden="true"></i> --}}
                            NUEVO
                        </a>
                    </div>

                </div>
            </div>
                <table id="tabla" class="table table-bordered table-striped mt-5 {{count($data) != 0 ? '' : 'd-none'}}">
                    <thead>
                        <tr>
                            <th scope=" col">#</th>
                            <th scope="col" class="col-7 v-center">LISTA DE FUNCIONES</th>
                            <th scope="col" class="text-center">AGREGAR PROCEDIMIENTO</th>
                            <th scope="col" class="text-center">MODIFICAR</th>
                            <th scope="col" class="text-center">DESACTIVAR/ACTIVAR</th>
                            {{-- @can('convenios.edit')
                                <th scope="col">MODIFICAR</th>
                            @endcan --}}
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="d-none color_fondo" id="filaOculta">
                            <td>#</td>
                            <td>
                                <div class="row col-12 mx-0 px-0">
                                    <form action="" method="post" class="row col-12" id="formAddFuncion">
                                        @csrf
                                        <div class="col-10 d-flex flex-wrap justify-content-center align-content-center">
                                            <textarea name="nom_funcion" class="form-control" id="nom_funcion" cols="35" rows="1" placeholder="Ingresa la función"></textarea>
                                        </div>
                                        {{-- valor de id --}}
                                        <input type="hidden" name="id_org" value="{{$organismo}}">
                                        <div class="col-2 mx-0 px-0 d-flex flex-wrap justify-content-center align-content-center">
                                            <a class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                                data-placement="top" title="GUARDAR REGISTRO" id="btnAddFunc">
                                                <i class="fa fa-check fa-2x mt-1" aria-hidden="true"></i>
                                            </a>
                                            <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                                data-placement="top" title="CANCELAR" id="btnCancelAdd">
                                                <i class="fa fa-times fa-2x mt-1" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </form>

                                </div>
                            </td>
                            <td class="text-center">
                                <a class="disabled btn-circle btn-circle-sm" data-toggle="tooltip" data-placement="top" title="AGREGAR" href="">
                                    <i class="fa fa-plus-square fa-2x mt-2" style="color: #00c851;" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a class="disabled btn-circle btn-circle-sm" data-toggle="tooltip" data-placement="top" title="EDITAR" id="btnMostrarFrmEdit" href="#">
                                    <i class="fas fa-pencil-alt fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="disabled custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input">
                                    <label class="custom-control-label" for="customSwitch"></label>
                                </div>
                            </td>
                        </tr>
                        @php $i=1; @endphp
                        @foreach ($data as $key=>$itemData)
                            <tr>
                                <td scope="row">{{($i+$key)}}</td>
                                <td>{{$itemData->fun_proc}}</td>
                                <td class="text-center">
                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="AGREGAR"
                                        href="{{route('pat.proced.mostrar',
                                        ['id' => $itemData->id, 'idorg' => $organismo])}}">
                                        <i class="fa fa-plus-square fa-2x mt-2" style="color: #00c851;" aria-hidden="true"></i>
                                    </a>
                                </td>

                                {{-- @can('convenios.edit') --}}
                                    <td class="text-center">
                                        <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="EDITAR" id="btnMostrarFrmEdit"
                                            href="{{ route('funciones.edit.show', ['id' => $itemData->id, 'idorg' => $organismo]) }}">
                                            <i class="fas fa-pencil-alt fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{-- <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="ELIMINAR"
                                            href="{{ route('funciones.destroy', ['id' => $itemData->id])}}">
                                            <i class="fa fa-times fa-2x mt-2" aria-hidden="true"></i>
                                        </a> --}}
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input"
                                                id="customSwitch{{$itemData->id}}"
                                                onclick="switchSel({{$itemData->id}})"
                                                {{$itemData->activo == 'true' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="customSwitch{{$itemData->id}}"></label>
                                        </div>
                                    </td>
                                {{-- @endcan --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            <div class="alert alert-success mt-4 {{count($data) == 0 ? '' : 'd-none'}}" role="alert" id="no_encontrado">
                <h4 class="alert-heading">Sin resultados!</h4>
                ¡No existen procedimientos en esta función!
            </div>


        </div>

        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{$data->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
    {{-- fin card --}}



    <!--Modal: modalConfirmDelete-->
    <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        {{-- color en el modal --}}
        <div class="modal-dialog modal-sm modal-notify modal-danger" id="colorModal" role="document">
        <!--Content-->
        <div class="modal-content text-center">
            <!--Header-->
            <div class="modal-header d-flex justify-content-center">
                {{-- Mensaje para el modal --}}
            <p class="heading" id="mensajeModal"></p>
            </div>

            <!--Body-->
            <div class="modal-body">
            <i class="fas fa-times fa-4x animated rotateIn" id="switchIcon"></i>
            </div>

            <!--Footer-->
            <div class="modal-footer flex-center">
            <button class="btn btn-outline-danger switch" id="btnConfirmModalSwitch">Si Quiero</button>
            <a type="button" class="btn btn-danger waves-effect" id="switchno" data-dismiss="modal">No Quiero</a>
            </div>
        </div>
        <!--/.Content-->
        </div>
    </div>
    <!--Modal: modalConfirmDelete-->

    {{-- Aqui termina el modal --}}

        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                /*Deshabilitamos la prate de convertir a mayusculas*/
                $("input[type=text], textarea, select").off("keyup");

                $("#btnAddFunc" ).click(function(e){
                    if ($("#nom_funcion").val().trim() != '') {
                        $('#formAddFuncion').attr('action', "{{ route('funciones.guardar')}}");
                        $("#formAddFuncion").attr("target", '_self');
                        $('#formAddFuncion').submit();
                    } else {
                        alert('ESTE CAMPO ES OBLIGATORIO. POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });

                $("#btnEditFunc" ).click(function(){
                    //let valor_area = $("#nom_funcion_edit").val();
                    if ( $("#nom_funcion_edit").val().trim() != '') {
                        $('#formEditFuncion').attr('action', "{{ isset($funcion_desc) ? route('funciones.update', $funcion_desc->id) : ''}}");
                        $("#formEditFuncion").attr("target", '_self');
                        $('#formEditFuncion').submit();
                    } else {
                        alert('ESTE CAMPO ES OBLIGATORIO. POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });


           });

           //Codigo para abrir modal y hacer todo el proceso
           var id_g = 0;
           var status_g = '';
            function switchSel(id) {
                id_g = id;
                let status = '';
                let colorModal = ''
                let icono = '';
                let mensajeModal = '';
                let statuscheck = 'noclicked';
                    if(document.getElementById("customSwitch"+id).checked==true) {
                        status = 'true';
                        status_g = status;
                        colorModal='success';
                        icono = 'fa-check';
                        mensajeModal = '¿Desea activar el estatus de registro?';
                        $('.switch').switchClass('btn-outline-danger', 'btn-outline-success');
                    }
                    else {
                        status = 'false';
                        status_g = status;
                        colorModal='danger';
                        icono = 'fa-times';
                        mensajeModal = '¿Desea desactivar el estatus de registro?';
                        $('.switch').switchClass('btn-outline-success', 'btn-outline-danger');
                    }

                    //Eliminamos clases del modal
                    $("#colorModal").removeClass();
                    $("#switchno").removeClass();
                    $("#switchIcon").removeClass();

                    //Color modal
                    $("#colorModal").addClass("modal-dialog modal-sm modal-notify modal-"+colorModal);
                    $("#switchno").addClass("btn btn-"+colorModal+" waves-effect");
                    $("#switchIcon").addClass("fas "+icono+" fa-4x animated rotateIn");
                    $("#mensajeModal").text(mensajeModal);

                    //Abrimos el modal
                    $('#modalConfirm').modal('show');

            }

            $('#btnConfirmModalSwitch').click(function(){
                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "id": id_g,
                        "status": status_g,
                    }
                    $.ajax({
                        type:"post",
                        url: "{{ route('pat.funciones.status') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                            $('#modalConfirm').modal('hide');
                        }
                    });
            });

            $('#switchno').click(function(){
                let miCheckbox = document.getElementById("customSwitch"+id_g);
                if (miCheckbox.checked){
                    miCheckbox.checked = false;
                }else{
                    miCheckbox.checked = true;
                }
            });


            //click en boton nuevo ocultamos formularios busqueda y mostramos de nuevo registro
            $("#btnNuevoReg").click(function () {
                $("#formBusqueda").addClass('d-none');
                $("#formEditFuncion").addClass('d-none');
                $("#tabla").removeClass('d-none');
                $("#filaOculta").removeClass('d-none');
                $("#no_encontrado").addClass('d-none');
            });

           $("#btnCancelAdd").click(function (e) {
               $("#formAddFuncion").addClass('d-none');
               $("#formEditFuncion").addClass('d-none');
               $("#formBusqueda").removeClass('d-none');
               $("#filaOculta").addClass('d-none'); //ocultamos el tr
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

            function cambiar_org() {
                let id_org = document.getElementById("sel_organismos_id").value;

                let url = "{{ route('pat.funciones.mostrar', [':idorg']) }}";
                url = url.replace(':idorg', id_org);
                window.open(url, "_self");
            }
        </script>
        @endsection
@endsection
