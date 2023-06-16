<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Fechas Pat | SIVyC Icatech')
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

    </style>

    <div class="card-header py-2">
        <h3>Fechas Metas y Avances</h3>
    </div>

    {{-- card para el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div id="alertasHead"></div>
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
                                data-placement="top" title="AGREGAR FECHAS" href="#" id="btnNuevoReg">
                                <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                            </a>
                        </div>
                    {{-- @endcan --}}

                    <form class="form-inline" action="" method="get" id="formBusqueda">
                        <select class="form-control mr-sm-2" name="" id="selectTabla" onchange="cambiarTabla()">
                            <option value="meta">FECHAS META</option>
                            <option value="avance" {{$mes_avance_get != null ? 'selected' : ''}}>FECHAS AVANCE</option>
                        </select>
                        <select name="meses_index" id="meses_index" class="form-control {{$mes_avance_get != null ? '' : 'd-none'}}" onchange="buscarMesAvance()">
                            <option value="">SELECCIONAR MES</option>
                            <option value="enero">ENERO</option>
                            <option value="febrero">FEBRERO</option>
                            <option value="marzo">MARZO</option>
                            <option value="abril">ABRIL</option>
                            <option value="mayo">MAYO</option>
                            <option value="junio">JUNIO</option>
                            <option value="julio">JULIO</option>
                            <option value="agosto">AGOSTO</option>
                            <option value="septiembre">SEPTIEMBRE</option>
                            <option value="octubre">OCTUBRE</option>
                            <option value="noviembre">NOVIEMBRE</option>
                            <option value="diciembre">DICIEMBRE</option>
                        </select>
                    </form>
                </div>
            </div>
            {{-- Formulario para agregar las fechas de metas y avances --}}
            <div class="form-row">
                <div class="col">
                    {{-- Formulario para agregar nuevo registro --}}
                        {!! Form::open(['method' => 'post', 'id' => 'formNuevoReg', 'class' => 'form-inline d-none']) !!}
                        @csrf
                        {{-- SELECT OPCIONES AGREGAR --}}
                        <select class="form-control mr-sm-3" name="select_opcion" id="select_opcion" onchange="opcionAgregar()">
                            <option value="meta">Meta</option>
                            <option value="avance">Avance</option>
                        </select>
                        {{-- SELECT PARA ELEGIR EL MES --}}
                        <select class="form-control mr-sm-3 d-none" name="opciones_mes" id="opciones_mes">
                            <option value="enero">ENERO</option>
                            <option value="febrero">FEBRERO</option>
                            <option value="marzo">MARZO</option>
                            <option value="abril">ABRIL</option>
                            <option value="mayo">MAYO</option>
                            <option value="junio">JUNIO</option>
                            <option value="julio">JULIO</option>
                            <option value="agosto">AGOSTO</option>
                            <option value="septiembre">SEPTIEMBRE</option>
                            <option value="octubre">OCTUBRE</option>
                            <option value="noviembre">NOVIEMBRE</option>
                            <option value="diciembre">DICIEMBRE</option>
                        </select>
                        {{ Form::date('fecha1', null,['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-3', 'placeholder' => 'FECHA EMISIÓN', 'title' => 'FECHA EMISIÓN']) }}
                        {{ Form::date('fecha2', null, ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-3', 'placeholder' => 'FECHA LIMITE', 'title' => 'FECHA LIMITE']) }}
                        {{ Form::button('GUARDAR', ['id' => 'botonGuardar', 'name'=> 'botonGuardar', 'value' => 'GUARDAR', 'class' => 'btn']) }}
                        {{ Form::button('CANCELAR', ['id' => 'botonCancelar', 'name'=> 'botonCancelar', 'value' => 'CANCELAR', 'class' => 'btn btn-danger']) }}
                        {!! Form::close() !!}
                </div>
            </div>
                {{-- Tabla de avances --}}
                <table class="table table-bordered table-striped" id="tabla_meta">
                    <p class="h4 text-center font-weight-bold mt-5 mb-3">REGISTROS DE FECHAS {{$mes_avance_get != null ? 'AVANCE ('.strtoupper($mes_avance_get).')' : '(META)'}}</p>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="col-5 v-center">ORGANISMO/AREA</th>
                            <th scope="col" class="col-5 v-center">FECHA DE EMISIÓN</th>
                            <th scope="col" class="col-5 v-center">FECHA LIMITE</th>
                            <th scope="col" class="col-5 v-center">STATUS</th>
                            <th scope="col" class="text-center">MODIFICAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($data); $i++)
                            <tr>
                                <td scope="row" class="font-weight-bold">{{($i+1)}}</td>
                                <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]['nombre']}}</td>
                                <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">
                                    {{$mes_avance_get == null ? $data[$i]['fecha_meta']['fechaemi'] : ''}}
                                    {{$mes_avance_get != null ? $data[$i]['fechas_avance'][$mes_avance_get]['fechaemision'] : ''}}
                                </td>
                                <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">
                                    {{$mes_avance_get == null ? $data[$i]['fecha_meta']['fechalimit'] : ''}}
                                    {{$mes_avance_get != null ? $data[$i]['fechas_avance'][$mes_avance_get]['fechafin'] : ''}}
                                </td>
                                <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">
                                    {{$mes_avance_get == null && $data[$i]['status_meta']['validado'] == '1' ? 'Validado' : ''}}
                                    {{$mes_avance_get != null && $data[$i]['fechas_avance'][$mes_avance_get]['statusmes'] == 'autorizado' ? 'Autorizado' : ''}}
                                </td>
                                <td class="d-block text-center">
                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="EDITAR" id=""
                                            href="#" onclick="ModalUpdate({{$data[$i]['id']}}, '{{$mes_avance_get != null ? 'avance' : 'meta'}}' , '{{$mes_avance_get}}')">
                                            <i class="fa fa-pencil-square-o fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                {{-- Tabla de Avances --}}
                {{-- <table class="table table-bordered table-striped d-none" id="tabla_avance">
                    <p class="h4 text-center font-weight-bold mt-5 mb-3" id="tituloTabla">REGISTROS DE FECHAS (META)</p>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="col-5 v-center">ORGANISMO/AREA</th>
                            <th scope="col" class="col-5 v-center">FECHA DE EMISIÓN    ({{strtoupper($mesLetra)}})</th>
                            <th scope="col" class="col-5 v-center">FECHA LIMITE   ({{strtoupper($mesLetra)}})</th>
                            <th scope="col" class="text-center">MODIFICAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($data); $i++)
                            <tr>
                                <td scope="row">{{($i+1)}}</td>
                                <td>{{$data[$i]['nombre']}}</td>
                                <td>{{
                                    isset($data[$i]['fechas_avance'][$mesLetra]['fechaemision']) ? $data[$i]['fechas_avance'][$mesLetra]['fechaemision'] : ''
                                    }}
                                </td>
                                <td>{{
                                    isset($data[$i]['fechas_avance'][$mesLetra]['fechafin']) ? $data[$i]['fechas_avance'][$mesLetra]['fechafin'] : ''
                                    }}
                                </td>
                                <td class="d-block text-center">
                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                            data-placement="top" title="EDITAR" id=""
                                            href="#" onclick="ModalUpdate({{$data[$i]['id']}}, 'avance', '{{$mesLetra}}' )">
                                            <i class="fa fa-pencil-square-o fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table> --}}
        </div>
        {{-- Paginación --}}
        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{$data->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
    {{-- termino del card --}}

    {{-- Modal --}}
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        {{-- color en el modal --}}
        <div class="modal-dialog modal-sm modal-notify modal-success" id="" role="document">
        <!--Content-->
        <div class="modal-content text-center">
            <!--Header-->
            <div class="modal-header d-flex justify-content-center">
                {{-- Mensaje para el modal --}}
            <p class="heading font-weight-bold" id="mensajeModal"></p>
            </div>

            <!--Body-->
            <div class="modal-body">
                <input type="hidden" name="id_reg" id="id_reg">
                <input type="hidden" name="tipo_reg" id="tipo_reg">
                <input type="hidden" name="mes_reg" id="mes_reg">
                <label for="fechaUpdate1" class="d-block text-left">Fecha de Emisión</label>
                <input type="date" class="form-control datepicker  mr-sm-3 mb-3" name="fechaUpdate1" id="fechaUpdate1" value="" placeholder="FECHA EMISIÓN">
                <label for="fechaUpdate2" class="d-block text-left">Fecha Limite</label>
                <input type="date" class="form-control datepicker  mr-sm-3" name="fechaUpdate2" id="fechaUpdate2" value="" placeholder="FECHA LIMITE">
            </div>

            <!--Footer-->
            <div class="modal-footer flex-center">
            <button class="btn btn-outline-success" id="" onclick="AceptarModal()">GUARDAR</button>
            <a type="button" class="btn btn-success waves-effect" id="" data-dismiss="modal">CANCELAR</a>
            </div>
        </div>
        <!--/.Content-->
        </div>
    </div>



        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#botonGuardar" ).click(function(){
                    if ($("#fecha1").val().trim() != '' && $("#fecha2").val().trim() != '') {
                        $('#formNuevoReg').attr('action', "{{route('pat.fechaspat.guardar')}}");
                        $("#formNuevoReg").attr("target", '_self');
                        $('#formNuevoReg').submit();
                    }else{
                        alert("¡FALTA INFORMACIÓN!. POR FAVOR , INGRESE UNA FECHA EN CADA CAMPO.")
                    }

                });
            });

            //Al dar click en actualizar debe abrir el modal
            function ModalUpdate(id, text, mes) {
                let tipo = text;

                //CONDICION PARA SABER SI ES META O AVANCE
                if (tipo == 'meta') {
                    $('#mensajeModal').text('MODIFICAR FECHAS META')
                    $('#modalUpdate').modal('show');

                }else if (tipo == 'avance') {
                    $('#mensajeModal').text('FECHAS AVANCE ' + '(MES DE '+ mes.toUpperCase() +')')
                    $('#modalUpdate').modal('show');
                }

                //Recuperamos los datos de las fechas de dicho id
                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "id": id,
                        "tipo": tipo,
                        "mes": mes
                    }
                $.ajax({
                        type:"post",
                        url: "{{ route('pat.fechaspat.consulfech') }}",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            console.log(response.fecha_emi);
                            $('#fechaUpdate1').val(response.fecha_emi);
                            $('#fechaUpdate2').val(response.fecha_limit);
                            $('#id_reg').val(response.id);
                            $('#tipo_reg').val(response.tipo_reg);
                            $('#mes_reg').val(response.mes);
                            console.log(response);
                        }
                });

            }

            function AceptarModal() {
                //obtenermos los valores de los campos
                let fechaEmi = $('#fechaUpdate1').val();
                let fechaFin = $('#fechaUpdate2').val();
                let id_reg = $('#id_reg').val();
                let tipo_reg = $('#tipo_reg').val();
                let mes_reg = $('#mes_reg').val();

                // console.log(fechaEmi, fechaFin, id_reg, tipo_reg, mes_reg );

                if (fechaEmi.trim() != '' && fechaFin.trim() != '') {
                     //enviamos los valores de las fechas
                    let data = {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            "id": id_reg,
                            "fechaEmi": fechaEmi,
                            "fechaLimit": fechaFin,
                            "tipo": tipo_reg,
                            "mes": mes_reg
                        }
                        $.ajax({
                            type:"post",
                            url: "{{ route('pat.fechaspat.saveupdate') }}",
                            data: data,
                            dataType: "json",
                            success: function (response) {
                                $('#modalUpdate').modal('hide');
                                //console.log(response);
                                //Mensaje de actualizacion de datos
                                let alertHead = $('#alertasHead');

                                let nuevoElemento =
                                $(
                                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                        '<span aria-hidden="true">&times;</span>' +
                                        '</button>' +
                                        '<strong>Fechas actualizadas correctamente</strong>' +
                                    '</div>'
                                );

                                alertHead.append(nuevoElemento);

                                location.reload();
                            }
                        });
                }else{
                    alert('¡FALTA INFORMACIÓN!. POR FAVOR , INGRESE UNA FECHA EN CADA CAMPO.');
                }


            }

            $("#btnNuevoReg").click(function () {
                $("#formBusqueda").addClass('d-none');
                $("#formNuevoReg").removeClass('d-none');
            });

            $("#botonCancelar").click(function () {
                $("#formNuevoReg").addClass('d-none');
                $("#formBusqueda").removeClass('d-none');
            });


            function opcionAgregar() {
                let valSelect = $('#select_opcion').val();
                if (valSelect == 'avance') {
                    $("#opciones_mes").removeClass('d-none');
                }else{
                    $("#opciones_mes").addClass('d-none');
                }
            }

            //Realiza consulta segun la opción seleccionada
            function cambiarTabla() {
                let sel_meta_avance = $('#selectTabla').val();

                if (sel_meta_avance == 'avance') {
                    //Muestra el select con los meses
                    $("#meses_index").removeClass('d-none');

                }else if(sel_meta_avance == 'meta'){
                    $("#meses_index").addClass('d-none');
                    $('#formBusqueda').attr('action', "{{ route('pat.fechaspat.mostrar')}}");
                    $("#formBusqueda").attr("target", '_self');
                    $('#formBusqueda').submit();
                }

            }

            function buscarMesAvance() {
                let select_mes = $('#meses_index').val();
                if (select_mes != '') {
                    let url = "{{ route('pat.fechaspat.mostrar', [':tipo']) }}";
                    url = url.replace(':tipo', select_mes);

                    $('#formBusqueda').attr('action', url);
                    $("#formBusqueda").attr("target", '_self');
                    $('#formBusqueda').submit();
                }else{
                    alert("SELECCIONE UN MES")
                }
            }

        </script>
        @endsection
@endsection
