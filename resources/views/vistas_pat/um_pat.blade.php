<!--Creado por Jose Luis Moreno Arcos luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Unidades de Medida | SIVyC Icatech')
    <!--seccion-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
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

            input[type=text],
            select,
            textarea {
                text-transform: none !important;
            }
    </style>
@endsection

@section('content')
    <div class="card-header py-2">
        <h3>Unidades de medida</h3>
    </div>

    {{-- Card como contenedor --}}
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
                    {{-- @can('convenios.create') --}}
                        <div class="pull-right">
                            <a class="btn btn-success py-1 px-2" data-toggle="tooltip"
                                data-placement="top" title="NUEVO REGISTRO" href="#" id="btnNuevoReg">
                                <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                            </a>
                        </div>

                    {{-- @endcan --}}
                </div>
            </div>

            <div class="form-row">
                <div class="col">
                    {{-- Formulario para busqueda --}}
                    <form class="form-inline {{ isset($unidad) ? 'd-none' : '' }}" action="{{ route('pat.unidadesmedida.mostrar') }}" method="get" id="formBusqueda">
                        <input type="text" class="form-control mr-sm-2" name="busqueda_unidad" id="busqueda_unidad" placeholder="Unidad de Medida">
                        <button type="submit" name="botonBuscar" id="botonBuscar" class="btn">BUSCAR</button>
                    </form>
                    {{-- Formulario para agregar nuevo registro --}}
                    <form class="form-inline d-none" action="" method="post" id="formNuevoReg">
                        @csrf
                        <input type="number" class="form-control mr-sm-2" name="numero_unidad" id="numero_unidad" placeholder="Numero">
                        <input type="text" class="form-control mr-sm-2" name="nombre_unidad" id="nombre_unidad" placeholder="Unidad de Medida">
                        <input type="text" name="tipo_unidad" id="tipo_unidad" class="form-control mr-sm-2" placeholder="TIPO DE UNIDAD">
                        {{-- select para agregar la clasificacion --}}
                        <select name="sel_clasif" id="sel_clasif" class="form-control mr-2">
                            <option value="">Clasificación</option>
                            <option value="federal">Federal</option>
                            <option value="estatal">Estatal</option>
                        </select>
                        <button type="button" name="botonGuardar" id="botonGuardar" class="btn">GUARDAR</button>
                        <button type="button" name="botonCancelar" id="botonCancelar" class="btn btn-danger">CANCELAR</button>
                    </form>
                    {{-- formulario para editar registro --}}
                    <form class="form-inline d-none" action="" method="post" id="formEditReg">
                        @csrf
                        <input type="number" value="" class="form-control mr-sm-2" name="numero_unidad_edit" id="numero_unidad_edit" placeholder="Numero">
                        <input type="text" value="" class="form-control mr-sm-2" name="nombre_unidad_edit" id="nombre_unidad_edit" placeholder="Unidad de Medida">
                        <input type="text" name="tipo_unidad_edit" id="tipo_unidad_edit" class="form-control mr-sm-2" placeholder="TIPO DE UNIDAD">
                        <input type="hidden" name="id_user_edit" id="id_user_edit" value="">
                        {{-- select para agregar la clasificacion --}}
                        <select name="sel_clasif_upd" id="sel_clasif_upd" class="form-control mr-2">
                            <option value="">Clasificación</option>
                            <option value="federal">Federal</option>
                            <option value="estatal">Estatal</option>
                        </select>
                        <button type="button" name="botonGuardarEdit" id="botonGuardarEdit" class="btn">GUARDAR</button>
                        <button type="button" name="botonCancelarEdit" id="botonCancelarEdit" class="btn btn-danger">CANCELAR</button>
                    </form>
                </div>
            </div>
            @if (count($data) != 0)
                <table id="table-instructor" class="table table-bordered table-striped mt-5">
                    <thead>
                        <tr>
                            <th scope="col">UM</th>
                            <th scope="col" class="col-7 v-center">UNIDAD DE MEDIDA</th>
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
                                        <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="EDITAR" onclick="editRegistro({{$itemData->id}})">
                                            <i class="fas fa-pencil-alt fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
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

    </div>
    {{-- fin del contenedor card --}}


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



        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                /*Deshabilitamos la prate de convertir a mayusculas*/
                $("input[type=text], textarea, select").off("keyup");

                $("#botonGuardar" ).click(function(){
                    if ($("#numero_unidad").val().trim() != '' && $("#nombre_unidad").val().trim() != '' && $("#tipo_unidad").val().trim() != '' && $("#sel_clasif").val().trim() != '') {
                        $('#formNuevoReg').attr('action', "{{ route('unidadesm.guardar') }}");
                        $("#formNuevoReg").attr("target", '_self');
                        $('#formNuevoReg').submit();
                    } else {
                        alert('CAMPOS OBLIGATORIOS. POR FAVOR, INGRESE UN VALOR VÁLIDO');
                    }
                });
                    //Editar datos
                $("#botonGuardarEdit" ).click(function(){
                    if ($("#numero_unidad_edit").val().trim() != '' && $("#nombre_unidad_edit").val().trim() != '' && $("#tipo_unidad_edit").val().trim() != '' && $("#sel_clasif_upd").val().trim() != '') {
                        $('#formEditReg').attr('action', "{{ route('unidadesm.update')}}");
                        $("#formEditReg").attr("target", '_self');
                        $('#formEditReg').submit();
                    } else {
                        alert('CAMPOS OBLIGATORIOS. POR FAVOR, INGRESE UN VALOR VÁLIDO');
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
                        status = 'activo';
                        status_g = status;
                        colorModal='success';
                        icono = 'fa-check';
                        mensajeModal = '¿Desea activar el estatus de registro?';
                        $('.switch').switchClass('btn-outline-danger', 'btn-outline-success');
                    }
                    else {
                        status = 'inactivo';
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
                    url: "{{ route('unidadesm.mod.status') }}",
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
                            $("#id_user_edit").val(response.datos.id);
                            $("#sel_clasif_upd").val(response.datos.clasific_unidadm);


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
