<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Fechas Pat | SIVyC Icatech')
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

    </style>
@endsection

@section('content')
    <div class="card-header py-2">
        <h3>Fechas Metas y Avances</h3>
    </div>

    {{-- card para el contenido --}}
    <div class="card card-body" style="min-height:450px;">
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
                @if (count($ejercicio) > 1)
                    <div class="col-3 mb-2 ml-4">
                        <form action="" id="form_eje">
                            <select name="sel_ejercicio" id="" class="form-control-sm" onchange="cambioEjercicio()">
                                @foreach ($ejercicio as $anioeje)
                                    <option {{$anioeje == $anio ? 'selected' : '' }} value="{{$anioeje}}">{{$anioeje}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif

                <div class="col-md-12">
                    <div class="col-md-12">
                        <form class="form-inline" action="" method="get" id="formBusqueda">
                            <select class="form-control-sm mr-sm-2" name="" id="selectTabla" onchange="cambiarTabla()">
                                <option value="meta">FECHAS META</option>
                                <option value="avance" {{$mes_avance_get != null ? 'selected' : ''}}>FECHAS AVANCE</option>
                            </select>
                            <select name="meses_index" id="meses_index" class="form-control-sm {{$mes_avance_get != null ? '' : 'd-none'}}" onchange="buscarMesAvance()">
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
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="">
                            <a class="btn btn-danger py-1 px-2" data-toggle="tooltip"
                                data-placement="top" title="DESHACER VALIDACIÓN" href="#" id="btnAbrirModal">
                                <i class="fa fa-history fa-2x" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="">
                            <a class="btn btn-success py-1 px-2" data-toggle="tooltip"
                                data-placement="top" title="AGREGAR FECHAS" href="#" id="btnNuevoReg">
                                <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>


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
                                        <i class="fas fa-pencil-alt fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        {{-- Paginación --}}
        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{$data->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
    {{-- termino del card --}}

    {{-- Modal Fechas Meta / Avance --}}
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

    {{-- Modal Deshacer Validación --}}
    <div class="modal fade" id="modalDeleteValid" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        {{-- color en el modal --}}
        <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">
        <!--Content-->
        <div class="modal-content text-center">
            <!--Header-->
            <div class="modal-header d-flex justify-content-center">
                {{-- Mensaje para el modal --}}
            <p class="heading font-weight-bold">DESHACER VALIDACIÓN</p>
            </div>

            <!--Body-->
            <div class="modal-body">
                <form action="" method="post" id="frmreturn_valid">
                    <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Alerta! </strong><span>Al reiniciar PAT se eliminaran los datos del organismo de acuerdo a lo que usted seleccione.</span>
                    </div>
                    <div class="form-group">
                        <select name="" id="ejer_valid" class="form-control">
                            <option value="">Ejercicio</option>
                            @for ($i = 2023; $i <= intval(date('Y')); $i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>

                        <select name="" id="list_dptos" class="form-control mt-3">
                            <option value="">Departamentos</option>
                            @foreach ($dptos_activos as $item_dpto)
                                <option value="{{$item_dpto->id}}">{{$item_dpto->nom_dpto}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-between d-row mx-0 px-0">
                        <div class="col-7 ml-0 pl-0">
                            <select name="" id="asunto_dpto" class="form-control">
                                <option value="">Asunto</option>
                                <option value="meta">Meta Anual</option>
                                <option value="avance">Avance Mensual</option>
                            </select>
                        </div>
                        <div class="col-5 mr-0 pr-0">
                            <select name="" id="mes_valid" class="form-control">
                                <option value="">Mes</option>
                                @foreach ($meses as $mes_item)
                                    <option value="{{$mes_item}}">{{$mes_item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="fechaIniV" class="d-block text-left">Fecha de Inicio</label>
                        <input type="date" class="form-control datepicker mb-3" name="fechaIniV" id="fechaIniV" value="" placeholder="FECHA DE INICIO">
                        <label for="fechaFinV" class="d-block text-left">Fecha Limite</label>
                        <input type="date" class="form-control datepicker" name="fechaFinV" id="fechaFinV" value="" placeholder="FECHA LIMITE">
                    </div>
                </form>
            </div>

            <!--Footer-->
            <div class="modal-footer flex-center">
                <button class="btn btn-danger" id="btnReturnValid" onclick="deshacerValid()">Activar</button>
                <a type="button" class="btn btn-outline-danger waves-effect" id="" data-dismiss="modal">Cancelar</a>
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

            // Cambiar de ejercicio
            function cambioEjercicio() {
                let url = "{{ route('pat.fechaspat.mostrar') }}";
                $('#form_eje').attr('action', url);
                $("#form_eje").attr("target", '_self');
                $('#form_eje').submit();
            }

            //Deshacer Validación
            $("#btnAbrirModal").click(function () {
                $('#modalDeleteValid').modal('show');
            });

            function deshacerValid() {
                let ejercicio = $("#ejer_valid").val();
                let departamento = $("#list_dptos").val();
                let asunto = $("#asunto_dpto").val();
                let fechaini = $("#fechaIniV").val().trim();
                let fechafin = $("#fechaFinV").val().trim();
                let mes = $("#mes_valid").val();

                if (ejercicio != '' && departamento != '' &&  asunto != '' &&
                    fechaini != '' && fechafin != '') {

                    if (asunto == 'avance' && mes == '') {
                        alert("SELECCIONE UN MES VALIDO");
                    }else{
                        let data = {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            "ejercicio": ejercicio,
                            "departamento": departamento,
                            "fechaini": fechaini,
                            "fechafin": fechafin,
                            "asunto": asunto,
                            "mes": mes
                        }
                        $.ajax({
                                type:"post",
                                url: "{{ route('pat.fechaspat.deshacer') }}",
                                data: data,
                                dataType: "json",
                                success: function (response) {
                                    // console.log(response);
                                    alert(response.mensaje);
                                    if (response.status == 200) {location.reload();}
                                }
                        });



                    }
                }else{
                    alert("¡FALTA INFORMACIÓN! POR FAVOR, VERIFIQUE QUE LOS CAMPOS ESTEN SELECCIONADOS");
                }
            }

        </script>
        @endsection
@endsection
