<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Procedimientos | SIVyC Icatech')
    <!--seccion-->

@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        * {
            box-sizing: border-box;
        }

        .select {
            /* height: 50px; */
            width: 25%;
        }

        #text_buscar_unidadm{
            height: fit-content;
            width: auto;
        }
        .color_fondo {
           background-color:  #e7e7e7;
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

            input[type=text],
            select,
            textarea {
                text-transform: none !important;
            }

    </style>
@endsection
@section('content')
    <div class="card-header py-2">
        <h3>Procedimientos</h3>
    </div>

    {{-- cart que contiene todo --}}
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
            @if ($message = Session::get('danger'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ $message }}</strong>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="d-flex flex-column align-items-end">
                        <div class="pull-right">
                            <h4><strong>Dirección :</strong> {{isset($org->nombre) ? $org->nombre : ''}}</h4>
                        </div>
                        <div class="pull-right">
                            <h4><strong>Area/Depto :</strong> {{isset($area->nombre) ? $area->nombre : ''}}</h4>
                        </div>
                    </div>
                </div>
                 {{-- @can('convenios.create') --}}

            {{-- @endcan --}}
            </div>

            <div class="form-row">
                <div class="col-8">
                    {{-- editar procedimiento --}}
                    <form class="form-inline mb-4 {{isset($dataedit) ? '' : 'd-none'}}"  action="" method="post" id="formEditProced">
                        @csrf
                        <div class="d-flex flex-column">
                            <textarea name="nom_proced_edit" class="form-control" id="nom_proced_edit" cols="35" rows="2">{{isset($dataedit) ? $dataedit->fun_proc : ''}}</textarea>
                            <input type="text" class="form-control mt-1 text_buscar_unidadm" id="um_upd" name="um_upd" placeholder="UNIDAD DE MEDIDA" value="{{isset($dataedit) ? $dataedit->unidadm : ''}}">
                            <input type="hidden" name="idorg" value="{{$organismo}}">
                            {{-- <input type="hidden" name="id_unidadm" value="{{isset($dataedit) ? $dataedit->id_unidadm : ''}}"> --}}
                        </div>

                        <div class="d-flex flex-column">
                            <button type="button" name="btnCancelEdit" id="btnCancelEdit" class="btn btn-danger mb-1">CANCELAR</button>
                            <button type="button" name="btnEditProced" id="btnEditProced" class="btn mt-1">GUARDAR</button>
                        </div>
                    </form>

                </div>

                <div class="col-4">
                     {{-- boton de agregar nuevo --}}
                     <div class="pull-right">
                        <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" id="btn_regresar"
                            data-placement="top" title="VOLVER ATRAS">
                            <i class="fa fa-reply fa-2x mt-2" aria-hidden="true"></i>
                        </a>
                        {{-- <button class="btn btn-success btn-sm" id="btnNuevoProced"><i class="fa fa-plus fa-2x"></i></button> --}}
                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                            data-placement="top" title="NUEVO REGISTRO" id="btnNuevoProced">
                            <i class="fa fa-plus fa-2x mt-2" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>


            <div>
                <div class="col-7">
                    <h4></h4>
                    {{-- <p class="text-left">{{$data->fun_proc}}</p> --}}

                </div>
                <div class="card mb-1">
                    <div class="ml-3">
                      <h4 class="card-title mb-1"><span class="badge badge-dark">Función</span></h4>
                      <p class="mb-0">{{$data->fun_proc}}</p>
                    </div>
                  </div>

                {{-- en caso de que no haya registros no mostrar --}}
                {{-- @if (count($data2) != 0) --}}
                    <table class="table table-bordered table-striped mt-3 {{count($data2) != 0 ? '' : 'd-none'}}" id="tabla">
                        <thead>
                            <tr>
                                <th scope=" col">#</th>
                                <th scope="col" class="col-7 v-center">PROCEDIMIENTOS</th>
                                <th scope="col" class="col-3 v-center">UNIDADES DE MEDIDA</th>
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
                                        <form action="" method="post" class="row" id="formAddProced">
                                            @csrf
                                            <div class="col-6 d-flex flex-wrap justify-content-center align-content-center">
                                                <textarea name="nuevoReg" class="form-control" id="nuevoReg" cols="35" rows="1" placeholder="Descripción"></textarea>
                                            </div>
                                            <div class="col-4 pl-0 d-flex flex-wrap justify-content-center align-content-center">
                                                <input type="text" id="text_buscar_unidadm" class="form-control select text_buscar_unidadm" name="text_buscar_unidadm" placeholder="Unidad de Medida">
                                            </div>
                                            <input type="hidden" name="idorg" value="{{$organismo}}">
                                            <div class="col-2 mx-0 px-0 d-flex flex-wrap justify-content-center align-content-center">
                                                <a class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="GUARDAR REGISTRO" id="btnAddProced">
                                                    <i class="fa fa-check fa-2x mt-1" aria-hidden="true"></i>
                                                </a>
                                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="CANCELAR" id="btnCancel">
                                                    <i class="fa fa-times fa-2x mt-1" aria-hidden="true"></i>
                                                </a>
                                                {{-- <button class="btn-sm btn-outline-success" id="btnAddProced"><i class="fa fa-check fa-2x" aria-hidden="true"></i></button> --}}
                                                {{-- <button class="btn-sm btn-outline-danger" id="btnCancel"><i class="fa fa-times fa-2x" aria-hidden="true"></i></button> --}}
                                            </div>
                                        </form>

                                    </div>
                                </td>
                                <td class="text-center">
                                    {{-- vacio solo para ocupar espacio --}}
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
                            @foreach ($data2 as $key=>$itemData)
                                <tr>
                                    <td scope="row">{{$i+$key}}</td>
                                    <td>{{$itemData->fun_proc}}</td>
                                    <td>{{$itemData->unidadm}}</td>

                                    {{-- @can('convenios.edit') --}}
                                        <td class="text-center">
                                            <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                data-placement="top" title="EDITAR" id="btnMostrarFrmEdit"
                                                href="{{ route('pat.proced.edit.show', ['idedi' => $itemData->id, 'id' => $id, 'idorg' => $organismo])}}">
                                                <i class="fas fa-pencil-alt fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            {{-- <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                                data-placement="top" title="ELIMINAR"
                                                href="{{ route('proced.destroy', ['idd' => $itemData->id,'id' => $id])}}">
                                                <i class="fa fa-times fa-2x mt-2" aria-hidden="true"></i>
                                            </a> --}}
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="cusSwitch{{$itemData->id}}"
                                                    onchange="switchSel({{$itemData->id}})"
                                                    {{$itemData->activo == 'true' ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="cusSwitch{{$itemData->id}}"></label>
                                            </div>
                                        </td>
                                    {{-- @endcan --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="alert alert-success mt-4 {{count($data2) == 0 ? '' : 'd-none'}}" role="alert" id="no_encontrado">
                        <h4 class="alert-heading">Sin resultados!</h4>
                        ¡No existen procedimientos en esta función!
                    </div>

            </div>
        </div>
    </div>


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

    {{-- <div class="row py-4">
        <div class="col d-flex justify-content-center">
            {{$data2->appends(request()->query())->links()}}
        </div>
    </div> --}}

        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                /*Deshabilitamos la prate de convertir a mayusculas*/
                $("input[type=text], textarea, select").off("keyup");

                $("#btnAddProced" ).click(function(){
                    if ($("#nuevoReg").val().trim() != '' && $("#text_buscar_unidadm").val().trim() != '') {
                        $('#formAddProced').attr('action', "{{ route('proced.guardar', ['id' => $id])}}");
                        $("#formAddProced").attr("target", '_self');
                        $('#formAddProced').submit();
                    } else {
                        alert('ESTE CAMPO ES OBLIGATORIO. POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });

                $("#btnEditProced" ).click(function(){
                    if ($("#nom_proced_edit").val().trim() != '' && $("#um_upd").val().trim() != '') {
                        $('#formEditProced').attr('action', "{{ isset($dataedit) ? route('proced.update', ['idedi' => $dataedit->id, 'id' => $id]) : '' }}");
                        $("#formEditProced").attr("target", '_self');
                        $('#formEditProced').submit();
                    } else {
                        alert('ESTE CAMPO ES OBLIGATORIO. POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });

                //Regresar a otra pantalla
                $('#btn_regresar').click(function(e){
                    $('#btn_regresar').attr('href', "{{ route('pat.funciones.mostrar', ['idorg' => $organismo])}}");
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
                    if(document.getElementById("cusSwitch"+id).checked==true) {
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
                        url: "{{ route('pat.proced.status') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                            $('#modalConfirm').modal('hide');
                        }
                    });
            });

            $('#switchno').click(function(){
                let miCheckbox = document.getElementById("cusSwitch"+id_g);
                if (miCheckbox.checked){
                    miCheckbox.checked = false;
                }else{
                    miCheckbox.checked = true;
                }
            });


            $("#btnNuevoProced").click(function () {
                $("#tabla").removeClass('d-none');
                $("#filaOculta").removeClass('d-none');
                $("#no_encontrado").addClass('d-none');
                $("#formEditProced").addClass('d-none');
            });

           $("#btnCancel").click(function (e) {
            e.preventDefault();
              //$("#formAddProced").addClass('d-none');
               $("#formEditProced").addClass('d-none');
               $("#filaOculta").addClass('d-none');
           });
           $("#btnCancelEdit").click(function () {
               $("#formAddProced").addClass('d-none');
               $("#formEditProced").addClass('d-none');
           });

            /*Funcion Ajax para realizar un autocompletado*/
           $( ".text_buscar_unidadm" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('pat.proced.autocomp') }}",
                        method: 'POST',
                        dataType: "json",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            search: request.term,
                            // tipoCurso: $('#busqueda').val()
                        },
                        success: function( data ) {
                            response( data );
                            console.log(data);
                        }
                    });
                }
            });

        </script>
        @endsection
@endsection
