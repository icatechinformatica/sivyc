<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Apertura | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('fullCalendar/core/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/daygrid/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/list/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/timegrid/main.css') }}">
    <style>
        table th, table td{font-size: 11px; padding:0px; margin:0px;}
    </style>
@endsection
@section('content')
    <div class="card-header">
        Solicitud / Clave de Apertura
    </div>
    <div class="card card-body" style=" min-height:450px;">
        <?php
            $modalidad = $valor = $munidad = $mov = $disabled = $hini = $hfin = $inco = $folio_pago = $fecha_pago = NULL;
            $activar = true;
            if(isset($grupo)){
                $inco = $grupo->inicio;
                $valor = $grupo->folio_grupo;
                $modalidad = $grupo->mod;
                $hfin = substr($grupo->horario, 8, 5);
                $hini = substr($grupo->horario, 0, 5);
                if(isset($grupo->munidad)) $munidad = $grupo->munidad;
                if($grupo->tcapacitacion=='PRESENCIAL'){
                    $disabled = 'disabled';
                    $grupo->medio_virtual='';
                    $grupo->link_virtual='';
                }  else {
                    if ($exonerado) {
                        $disabled = 'readonly';
                    }
                }
            }
            if(isset($alumnos[0]->mov))$mov = $alumnos[0]->mov;
            if($recibo){
                $comprobante = env('APP_URL')."/storage/".$recibo->file_pdf;
                $folio_pago = $recibo->folio_recibo;
                $fecha_pago = $recibo->fecha_expedicion;
            }elseif(isset($grupo)) {
                $folio_pago = $grupo->folio_pago;
                $fecha_pago = $grupo->fecha_pago;
            }
        ?>
        {{ Form::open(['route' => 'solicitud.apertura', 'method' => 'post', 'id'=>'frm']) }}
            @csrf
            <div class="row">
                <div class="form-group col-md-3">
                        {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. GRUPO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 25]) }}
                </div>
                <div class="form-group col-md-2">
                        {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
                </div>

        </div>
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        {{-- Mensaje de error - Jose Luis Moreno Arcos --}}
        @if($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
        @endif

        @if(isset($grupo))
            <h5><b>DEL CURSO</b></h5>
            @if($grupo->clave)
                <div class="row bg-light form-inline" style="padding:15px 10px 15px 0; text-indent:4em; line-height: 2.1em;">
                        @if($grupo->clave)<span>CLAVE:&nbsp;&nbsp;<strong>{{$grupo->clave}}</strong></span>@endif
                        @if($grupo->arc)<span>ARC:&nbsp;&nbsp;<strong>{{$grupo->arc}}</strong></span>@endif
                        @if($grupo->status_curso)<span>ESTATUS ARC:&nbsp;&nbsp;<strong>{{$grupo->status_curso}}</strong></span>@endif
                        @if($grupo->status)<span>ESTATUS FORMATOT:&nbsp;&nbsp;<strong>{{$grupo->status}}</strong></span>@endif
                </div>
            @endif
            <div class="row bg-light form-inline" style="padding:15px 10px 15px 0px; text-indent:2em; line-height: 3em;">
                <span>UNIDAD/ACCI&Oacute;N M&Oacute;VIL:&nbsp;&nbsp;<strong>{{ $grupo->unidad }}</strong></span>
                <span>CURSO:&nbsp;&nbsp;<strong>@if($grupo->clave){{ $grupo->id }}@endif {{ $grupo->curso }}</strong></span>
                <span>ESPECIALIDAD: &nbsp;&nbsp;<strong>{{ $grupo->clave_especialidad }} &nbsp{{ $grupo->espe }}</strong></span>
                <span>&Aacute;REA: &nbsp;&nbsp;<strong>{{ $grupo->area }}</strong></span>
                <span>MODALIDAD: &nbsp;&nbsp;<strong>{{ $grupo->mod}}</strong></span>
                <span>TIPO CAPACITACI&Oacute;N: &nbsp;&nbsp;<strong>{{ $grupo->tcapacitacion}}</strong></span>
                <span>DURACI&Oacute;N: &nbsp;&nbsp;<strong>
                @if ($grupo->dura)
                    {{ $grupo->dura }}
                @else
                    {{ $grupo->horas }}
                @endif hrs.</strong></span>
                <input type="hidden" name="hini" id="hini" value="{{$hini}}">

                <div id="hora">HORARIO: <b>{{ $grupo->horario }}</b></div> <input type="hidden" name="hfin" id="hfin" value="{{$hfin}}">
                <span>COSTO ALUMNO: &nbsp;&nbsp;<strong>{{ $grupo->costo_individual }}</strong></span>
                <span>HOMBRES: &nbsp;&nbsp;<strong>{{ $grupo->hombre }}</strong></span>
                <span>MUJERES: &nbsp;&nbsp;<strong>{{ $grupo->mujer }}</strong></span>
                <span>FECHA INICIO:  &nbsp;&nbsp;<strong>{{ $grupo->inicio }}</strong></span> <input type="hidden" name="inicio" id="inicio" value="{{$grupo->inicio}}">
                <span>FECHA TERMINO:  &nbsp;&nbsp;<strong>{{ $grupo->termino }}</strong></span> <input type="hidden" name="termino" id="termino" value="{{$grupo->termino}}">
                @if ($grupo->tdias)
                    <span>TOTAL DIAS: &nbsp;&nbsp;<strong>{{ $grupo->tdias }}</strong></span>
                    <span>DIAS: &nbsp;&nbsp;<strong>{{ $grupo->dia }}</strong></span>
                @endif
                    <span>MUNICIPIO: &nbsp;&nbsp;<strong>{{ $muni }}</strong></span>
                    <span>LOCALIDAD: &nbsp;&nbsp;<strong>{{ $localidad }}</strong></span>
                    <span>ORGANISMO PUBLICO: &nbsp;&nbsp;<strong>{{ $grupo->organismo_publico }}</strong></span>

            </div>
            <h5><b>DE LA APERTURA</b></h5>
            <hr />

            <div class="row bg-light form-inline" style="padding:15px 10px 15px 0; text-indent:4em; line-height: 3em;">
                @if($munidad)
                    <span>CUOTA TOTAL: &nbsp;&nbsp;<strong>{{ $grupo->costo }}</strong></span>
                    <span>TIPO CUOTA: &nbsp;&nbsp;<strong>{{ $tcuota }}</strong></span>
                @endif
                @if(isset($instructor->tipo_honorario))
                    <span>RÉGIMEN DEL INSTRUCTOR :&nbsp;&nbsp;<strong>{{$instructor->tipo_honorario}}</strong></span>
                @endif
                <span>MEMORANDUM DE VALIDACION DEL INSTRUCTOR:&nbsp;&nbsp;<strong>{{ $grupo->instructor_mespecialidad }}</strong></span>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>INSTRUCTOR:</label>
                    <select name="instructor" id="instructor" class="form-control mr-sm--2" @if ($exonerado) style="background-color: lightGray;" @endif>
                        @if ($instructor)
                            <option value="{{$instructor->id}}">{{$instructor->instructor}}</option>
                        @else
                            <option value="">- SELECCIONAR -</option>
                        @endif

                        @foreach ($instructores as $item)
                            <option value="{{$item->id}}">{{$item->instructor}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>MEMOR&Aacute;NDUM DE APERTURA:</label>
                    <input name='munidad' id='munidad' type="text" class="form-control" aria-required="true" value="@if($munidad){{$munidad}}@else{{old('nombre')}}@endif"/>
                </div>
                <div class="form-group col-md-3">
                    <label>PLANTEL:</label>
                    {{ Form::select('plantel', $plantel, $grupo->plantel, ['id'=>'plantel','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>
            </div>
            <div class="form-row" >
                <div class="form-group col-md-4">
                    <label>PROGRAMA ESTRAT&Eacute;GICO:</label>
                    {{ Form::select('programa', $programa, $grupo->programa, ['id'=>'programa','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>
                <div class="form-group col-md-2">
                    <label>CONVENIO GENERAL:</label>
                    <input name='cgeneral' id='cgeneral' type="text" class="form-control" aria-required="true" value="{{$convenio['no_convenio']}}" readonly/>
                </div>
                <div class="form-group col-md-2">
                    <label>FECHA CONVENIO GENERAL:</label>
                   <input type="date" id="fcgen" name="fcgen" class="form-control"  aria-required="true" value="{{$convenio['fecha_firma']}}" readonly/ >
                </div>
                <div class="form-group col-md-3">
                    <label>SECTOR:</label>
                    <input name='sector' id='sector' type="text" class="form-control" aria-required="true" value="{{$sector}}" readonly/>
                </div>
            </div>
            <div class="form-row" >
                <div class="form-group col-md-4">
                    <label>CONVENIO ESPEC&Iacute;FICO:</label>
                    <input name='cespecifico' id='cespecifico' type="text" class="form-control" aria-required="true" value="{{ $grupo->cespecifico}}" readonly/>
                </div>
                <div class="form-group col-md-3">
                    <label>FECHA CONVENIO ESPEC&Iacute;FICO:</label>
                    <input type="date" id="fcespe" name="fcespe" aria-required="true" class="form-control" value="{{$grupo->fcespe}}" readonly>
                </div>
                <div class="form-group col-md-4">
                     <label>MEMOR&Aacute;NDUM DE EXONERACI&Oacute;N:</label>
                     <input name='mexoneracion' id='mexoneracion' type="text" class="form-control" aria-required="true" value="{{$grupo->mexoneracion}}" readonly/>
                </div>
                </div>
            <div class="form-row" >
                <div class="form-group col-md-12">
                    <label>DOMICILIO, LUGAR O ESPACIO F&Iacute;SICO:</label>
                    <input type="text" id="efisico" name="efisico" class="form-control" value="{{$grupo->efisico}}" readonly>
                </div>
            </div>
            <div class="form-row" >
                <div class="form-group col-md-2">
                    <label>TIPO DE CAPACITACI&Oacute;N:</label>
                    <input type="text" id="efisico" name="efisico" class="form-control" value="{{$grupo->tipo_curso}}" readonly>
                </div>
                <div class="form-group col-md-2">
                     <label>MEDIO VIRTUAL:</label>
                     {{ Form::select('medio_virtual', $medio_virtual, $grupo->medio_virtual, ['id'=>'medio_virtual','class' => 'form-control mr-sm-2','disabled'=>$disabled] ) }}
                </div>
                <div class="form-group col-md-8">
                     <label>LINK VIRTUAL:</label>
                     <input name='link_virtual' id='link_virtual' type="url" class="form-control" value="{{$grupo->link_virtual}}" {{$disabled}} />
                </div>
            </div>
            <div class="form-row" >
                <div class="form-group col-md-12">
                    <label>OBSERVACIONES DE VINCULACI&Oacute;N:</label>
                    <textarea name='obs_vincu' id='obs_vincu'  class="form-control" rows="5" readonly>{{$grupo->nota_vincu}}</textarea>
                </div>
                <div class="form-group col-md-12">
                    <label>OBSERVACIONES:</label>
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="5" >{{$grupo->nota}}</textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="">NO. RECIBO DE PAGO:</label>
                    <input type="text" name="folio_pago" id="folio_pago" class="form-control" placeholder="FOLIO PAGO" value="{{$folio_pago}}"  @if($recibo) disabled @else readonly @endif>
                </div>
                <div class="form-group col-md-2">
                    <label for="">EMISI&Oacute;N DEL RECIBO:</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" placeholder="FECHA PAGO" value="{{$fecha_pago}}"  @if($recibo) disabled @else readonly @endif>
                </div>
                <div class="form-group col-md-4">
                    <br/>
                    @if($comprobante)
                        <a class="nav-link" href="{{$comprobante}}" target="_blank">
                            <i  class="far fa-file-pdf  fa-3x text-danger"></i>
                        </a>
                    @else
                        <i  class="far fa-file-pdf  fa-3x text-muted"  title='ARCHIVO NO DISPONIBLE.'></i>
                    @endif
                </div>
            </div>
            <hr/>

                <h4><b>ALUMNOS</b></h4>
                <div class="row">
                    @include('solicitud.apertura.table')
                </div>

            @endif
        {!! Form::close() !!}
    </div>
@if (isset($grupo))
<!-- modal para mostrar el calendario -->
<div class="modal fade" id="modalCalendar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-notify modal-info" role="document">
        <!--Content-->
        <div class="modal-content text-center">
            <!--Header-->
            <div class="modal-header d-flex justify-content-center bg-primary" id="cabezaModal">
                <p id="titleCalendar" class="heading font-weight-bold h4 text-white text-center"></p>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body">
                <div aria-live="assertive" aria-atomic="true" style="position: relative; top: 0; left: 0;"
                    role="alert" class="toast mt-1 mr-1" data-autohide="false">
                    <div class="toast-header bg-primary">
                        <strong id="titleToast" class="mr-auto text-white"></strong>
                        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="msgVolumen" class="toast-body">
                    </div>
                </div>

                {{-- action="javascript:void(0)" --}}
                <div class="row d-flex align-items-center">
                    <div class="col-12 col-md-6">
                        <form id="formCalendario">
                            <div class="row">
                                <input type="text" name="txtId" id="txtId" class="d-none">
                                <div class="col">
                                    <!-- fecha inicial -->
                                    <div class="form-group">
                                        <label for="fecha_firma" class="control-label">FECHA DE INCIO</label>
                                        <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly"
                                            name="fecha_firma" class="form-control datepicker" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <!-- Fecha conclusion -->
                                    <div class="form-group">
                                        <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                                        <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly"
                                            name="fecha_termino" class="form-control datepicker" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">HORA INICIO</label>
                                        <input type="time" class="form-control"
                                            name="txtHora" id="txtHora" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">HORA TERMINO</label>
                                        <input type="time" class="form-control"
                                            name="txtHoraTermino" id="txtHoraTermino" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col">
                                    <label for="observaciones_mod" class="control-label">OBSERVACIONES</label>
                                    <textarea name="observaciones_mod" class="form-control" id="observaciones_mod" rows="6"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col">
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer flex-center py-2">
                <div class="col d-flex justify-content-start">
                    <button id="btnClean" type="button" class="btn btn-info">Limpiar campos</button>
                </div>
                <div class="row">
                    <div class="col">
                        @if ($grupo->turnado != 'DTA' AND (!$grupo->status_solicitud OR $grupo->status_solicitud=='RETORNO') AND !$exonerado)
                        <button id="btnAgregar" type="button" class="btn btn-success disabled">Agregar</button>
                        {{--<button id="btnModificar" class="btn btn-warning">Modificar</button>--}}
                        <button id="btnBorrar" class="btn btn-danger disabled">Borrar</button>
                        <button type="button" data-dismiss="modal" class="btn btn-outline-danger">Cancelar</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
@endif
    @section('script_content_js')
        <script src="{{ asset('js/solicitud/apertura.js') }}"></script>
        <script src="{{ asset('edit-select/jquery-editable-select.min.js') }}"></script>

        <script src="{{ asset('fullCalendar/core/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/core/locales-all.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/interaction/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/daygrid/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/list/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/timegrid/main.js') }}" defer></script>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script language="javascript">
            $(document).ready(function(){
                $('#medio_virtual').editableSelect();
                //Generar pdf soporte / Made by Jose Luis Moreno Arcos
                $("#genpdf_soporte").click(function(){
                    if ($("#num_oficio").val().trim() != "") {
                        $('#frm').attr('action', "{{route('solicitud.genpdf.soporte')}}");
                        $('#frm').attr('target', '_blank');
                        $('#frm').submit();
                    }else alert("El campo 'NUMERO DE OFICIO' es requerido para generar el pdf");
                });

                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.apertura')}}");$('#frm').attr('target', '_self'); $('#frm').submit();});
                $("#regresar" ).click(function(){if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.regresar')}}");$('#frm').attr('target', '_self'); $('#frm').submit();}});
                $("#guardar" ).click(function(){
                    validaCERT();
                    if(confirm("Esta seguro de ejecutar la acción?")==true){
                        $('#frm').attr('action', "{{route('solicitud.apertura.guardar')}}");
                        $('#frm').attr('enctype', "multipart/form-data");
                        $('#frm').attr('target', '_self');
                        $('#frm').submit();
                    }
                });
                $("#inscribir" ).click(function(){if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.aceptar')}}");$('#frm').attr('target', '_self'); $('#frm').submit();}});

                $('#dia').keyup(function (){
                    this.value = (this.value + '').replace(/[^LUNES A VIERNES MARTES MIERCOLES JUEVES SABADO Y DOMINGO]/g, '');
                });


                $("#tcurso").change(function(){
                   validaCERT();
                });

                function validaCERT(){
                    if($("#tcurso").val()=='CERTIFICACION'){
                        var hini = $("#hini").val();
                        var hfin = $("#hfin").val();
                        var inicio = $("#inicio").val();
                        var termino = $("#termino").val();
                        if(inicio==termino){
                            var d1= "2014-02-14 "+hfin+":00";
                            var d2 = "2014-02-14 "+hini+":00";
                            var a = new Date(d1);
                            var b = new Date(d2);
                            var c = ((a-b)/1000);
                            if(c!=36000){
                                 alert("Para el servicio de CERTIFICACI\u00D3N, se deben cubrir 10 horas. ");
                                 exit;
                            }
                        }else{
                            alert('Fechas incorrectas, para las CERTIFICACIONES deben coincidir la fecha inicio y fecha termino.');
                            exit;
                        }
                    }
                }

            });
        </script>
        <script type="text/javascript">
            var calendarEl = document.getElementById('calendar');
            var calendar, idEvento, objEvento;
            setTimeout(popup,1000);
            $('#btnShowCalendar').click(function(e) {
                if (document.getElementById('instructor').value) {
                    limpiarFormulario();
                    inicializarCalendar();
                    let hora = document.getElementById("txtHora");
                    let horaTermino = document.getElementById("txtHoraTermino");
                    hora.disabled = false;
                    horaTermino.disabled = false;
                    $('#titleCalendar').html(document.getElementById('instructor').value.split(',')[1]);
                    $("#modalCalendar").modal("show");
                } else {
                    console.log('no se selecciono instructor');
                }
            });
            $('#btnShowCalendarFlex').click(function(e) {
                if (document.getElementById('instructor').value) {
                    limpiarFormulario();
                    inicializarCalendar();
                    let hora = document.getElementById("txtHora");
                    let horaTermino = document.getElementById("txtHoraTermino");
                    hora.disabled = false;
                    horaTermino.disabled = false;
                    $('#titleCalendar').html(document.getElementById('instructor').value.split(',')[1]);
                    $("#modalCalendar").modal("show");
                } else {
                    console.log('no se selecciono instructor');
                }
            });
            function limpiarFormulario() {
                $('#formCalendario')[0].reset();
                idEvento = null;
                objEvento = null;
                horahini= document.getElementById("hini").value;
                horafin= document.getElementById("hfin").value;
                $('#txtHora').val(horahini);
                $('#txtHoraTermino').val(horafin);

                $('#btnAgregar').prop('disabled', false);
                $('#btnModificar').prop('disabled', true);
                $('#btnBorrar').prop('disabled', true);
            }
            function inicializarCalendar() {
                objEvento = [];
                nuevoEvento = [];
                if (calendar) {
                    calendar.destroy();
                }
                calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['dayGrid', 'interaction', 'timeGrid', 'list'],

                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth, timeGridWeek, timeGridDay',
                    },
                    defaultDate: '<?php echo $inco; ?>',
                    eventClick: function(info) {
                        $('#btnAgregar').prop('disabled', true);
                        $('#btnModificar').prop('disabled', false);
                        $('#btnBorrar').prop('disabled', false);

                        // $('#txtTitulo').val(info.event.title);

                        // fecha y hora inicial
                        mes = (info.event.start.getMonth() + 1);
                        dia = (info.event.start.getDate());
                        anio = (info.event.start.getFullYear());
                        mes = (mes < 10) ? '0' + mes : mes;
                        dia = (dia < 10) ? '0' + dia : dia;
                        minutos = info.event.start.getMinutes();
                        hora = info.event.start.getHours();
                        minutos = (minutos < 10) ? '0' + minutos : minutos;
                        hora = (hora < 10) ? '0' + hora : hora;
                        horario = (hora + ':' + minutos);

                        // fecha y hora final
                        mes2 = (info.event.end.getMonth() + 1);
                        dia2 = (info.event.end.getDate());
                        anio2 = (info.event.end.getFullYear());
                        mes2 = (mes2 < 10) ? '0' + mes2 : mes2;
                        dia2 = (dia2 < 10) ? '0' + dia2 : dia2;
                        minutos2 = info.event.end.getMinutes();
                        hora2 = info.event.end.getHours();
                        minutos2 = (minutos2 < 10) ? '0' + minutos2 : minutos2;
                        hora2 = (hora2 < 10) ? '0' + hora2 : hora2;
                        horario2 = (hora2 + ':' + minutos2);

                        idEvento = info.event.id;
                        $('#txtId').val(info.event.id);
                        $('#fecha_firma').val(dia + '-' + mes + '-' + anio);
                        $('#txtHora').val(horario);
                        $('#fecha_termino').val(dia2 + '-' + mes2 + '-' + anio2);
                        $('#txtHoraTermino').val(horario2);
                        $('#observaciones_mod').val(info.event.extendedProps.observaciones);
                    },
                    events: ("{{ url('/calendario/show') }}" + ('/' + document.getElementById('instructor').value.split(',')[0])),
                });
                calendar.setOption('locale', 'es');
                calendar.render();
            }
            // formato fechas
            var dateFormat = "dd-mm-yy",
                from = $("#fecha_firma").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    minDate: (document.getElementById("inicio").value).split('-').reverse().join('-'),
                    maxDate: (document.getElementById("termino").value).split('-').reverse().join('-'),
                    numberOfMonths: 1,
                    dateFormat: 'dd-mm-yy',
                    beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 35, left: rect.left + 0 });
                        }, 0);
                    }
                }).on("change", function() {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#fecha_termino").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    minDate: (document.getElementById("inicio").value).split('-').reverse().join('-'),
                    maxDate: (document.getElementById("termino").value).split('-').reverse().join('-'),
                    numberOfMonths: 1,
                    dateFormat: 'dd-mm-yy',
                    beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 35, left: rect.left + 0 });
                        }, 0);
                    }
                })
                .on("change", function() {
                    // from.datepicker("option", "maxDate", getDate(this));
                });
            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }
                return date;
            }
            $('#btnClean').click(function() {
                limpiarFormulario();
            });
            $('#btnAgregar').click(function() {
                isEquals = false;
                isEquals2 = false;
                objEvento = null;
                objEvento = recolectarDatos("POST");
                if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == ''
                    || $('#txtHoraTermino').val() == '') {
                        objEvento = null;
                        $('#titleToast').html('Campos vacios');
                        $("#msgVolumen").html("Todos los campos son requeridos");
                        $(".toast").toast("show");
                } else {
                    if ($('#txtHora').val() < $('#txtHoraTermino').val()){
                        EnviarInformacion("", objEvento, 'insert');
                    }else{
                        $('#titleToast').html('Hora incorrecta');
                        $("#msgVolumen").html("La hora de inicio debe ser menor a la hora de termino");
                        $(".toast").toast("show");
                    }

                }
            });
            function recolectarDatos(method) {
                nuevoEvento = []
                nuevoEvento = {
                    title: document.getElementById('curso').value,
                    start: $('#fecha_firma').val() + ' ' + $('#txtHora').val(),
                    end: $('#fecha_termino').val() + ' ' + $('#txtHoraTermino').val(),
                    textColor: '#000000',
                    observaciones: $('#observaciones_mod').val(),
                    id_instructor: document.getElementById('instructor').value.split(',')[0],
                    /*id_curso: document.getElementById('id_grupo').value,
                    unidad_grupo: document.getElementById('unidad').value,
                    id_municipio: document.getElementById('id_municipio').value,
                    tipo_curso: document.getElementById('tipo_curso').value,*/
                    '_token': $("meta[name='csrf-token']").attr("content"),
                    '_method': method
                }
                return nuevoEvento;
            }
            function EnviarInformacion(accion, objEvento, tipo) {
                link = ((tipo == 'insert') ? "{{ url('/calendario/guardar') }}" : (tipo == 'update') ?
                    "{{ url('/calendario/update') }}" + accion : "{{ url('/calendario') }}" + accion);
                tipo2 = (tipo == 'insert') ? 'POST' : (tipo == 'update') ? 'POST' : 'GET';

                $.ajax({
                    type: tipo2,
                    url: link,
                    data: objEvento,
                    success: function(msg) {
                        console.log(msg);
                        if (tipo == 'insert' || tipo == 'update') {
                            if (msg) { //hay registro con la fecha y hora
                                $('#titleToast').html('Registro no valido');
                                $("#msgVolumen").html(msg);
                                $(".toast").toast("show");
                            } else {
                                calendar.refetchEvents();
                                limpiarFormulario();
                            }
                        } else {
                            calendar.refetchEvents();
                            limpiarFormulario();
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        // console.log(textStatus);
                        console.log(jqXHR);
                        alert( "Hubo un error: " + jqXHR.status );
                    }
                });
            }
            // boton eliminar
            $('#btnBorrar').click(function() {
                objEvento = [];
                objEvento = recolectarDatos("DELETE");
                EnviarInformacion('/' + $('#txtId').val(), objEvento, 'delete');
            });
            $('#btnModificar').click(function() {
                isEquals = false;
                isEquals2 = false;
                objEvento = null;
                objEvento = recolectarDatos("POST");

                if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == ''
                    || $('#txtHoraTermino').val() == '' || $('#observaciones').val() == '') {
                    $('#titleToast').html('Campos vacios');
                    $("#msgVolumen").html("Todos los campos son requeridos");
                    $(".toast").toast("show");
                } else {
                    if ($('#txtHora').val() < $('#txtHoraTermino').val()) {
                        EnviarInformacion('/' + $('#txtId').val(), objEvento, 'update');
                    } else {
                        $('#titleToast').html('Hora incorrecta');
                        $("#msgVolumen").html("La hora de inicio debe ser menor a la hora de termino");
                        $(".toast").toast("show");
                    }
                }
            });
            function popup(){
                if (document.getElementById('instructor').value) {
                    limpiarFormulario();
                    inicializarCalendar();
                    let hora = document.getElementById("txtHora");
                    let horaTermino = document.getElementById("txtHoraTermino");
                    hora.disabled = false;
                    horaTermino.disabled = false;
                    $('#titleCalendar').html(document.getElementById('instructor').value.split(',')[1]);
                    $("#modalCalendar").modal("show");
                } else {
                    console.log('no se selecciono instructor');
                }
            }

            //pdf soporte Made by Jose Luis Moreno Arcos
            //Funcion de mostrar si el archivo esta cargado
            function checkIcon(idIcon, inputPdfId) {
                let iconIndic = document.getElementById(idIcon);
                let pdfInput = document.getElementById(inputPdfId);
                if (pdfInput.files.length > 0) {
                    iconIndic.style.display = 'inline-block';
                } else {
                    iconIndic.style.display = 'none';
                }
            }

            //Ocultar y mostrar los iconos de ver pdf para cada documento
            function selUploadPDF() {
                let valSelect = document.getElementById('subirPDF25').value;
                var showPDF = document.getElementById("verPdfLink25");
                var partes = valSelect.split('?');
                switch (partes[0]) {
                    case '0':
                        $("#verPdfLink25").addClass('d-none');
                        break;
                    case '1':
                        if (partes[1] != '') {
                            showPDF.href = partes[1];
                            $("#verPdfLink25").removeClass('d-none');
                            // Ocultamos la parte de subir pdf
                            $("#formUpPdf25").addClass('d-none');
                        }else{
                            $("#verPdfLink25").addClass('d-none');
                            // Mostramos la parte de subir pdf
                            $("#formUpPdf25").removeClass('d-none');
                        }
                        break;
                    default:
                        break;
                }
            }

            //Enviar los archivos al backend
            function UploadPDF(event) {
                event.preventDefault();
                 //Obtenermos el valor del input archivo
                let valSelect = document.getElementById('subirPDF25').value;
                let inputFile = document.getElementById('pdfInputDoc25');
                let partes = valSelect.split('?');

                if (partes[0] == '0'){ alert('SELECCIONA UNA OPCIÓN'); return;}
                // if (partes[1] !== '') {
                //     alert('YA HAS REALIZADO ESTA ACCIÓN ANTERIORMENTE');
                //     return;
                // }

                if (inputFile.files.length === 0) { //Realizamos la validacion si esta el archivo
                    alert("POR FAVOR, SELECCIONA UN ARCHIVO PDF.");
                    return;
                }

                let archivo = inputFile.files[0];
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('archivoPDF', archivo);
                formData.append('opcion', partes[0]);
                formData.append('urlImg', partes[1]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('solicitud.soporte.upload') }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        alert(response.mensaje);
                        if (response.status == 200 || response.status == 500) {
                            location.reload();
                        }
                        // setTimeout(function() { location.reload(); }, 3000);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("Error al enviar el archivo.");
                    }
                });
            }

        </script>
    @endsection
@endsection
