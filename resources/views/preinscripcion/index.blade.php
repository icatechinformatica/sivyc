<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Preinscripción | SIVyC Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
        <link rel="stylesheet" href="{{asset('css/preinscripcion/index.css') }}" />
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/tools/combox_edit.css') }}" />    
        
        <link rel="stylesheet" href="{{ asset('fullCalendar/core/main.css') }}">
        <link rel="stylesheet" href="{{ asset('fullCalendar/daygrid/main.css') }}">
        <link rel="stylesheet" href="{{ asset('fullCalendar/list/main.css') }}">
        <link rel="stylesheet" href="{{ asset('fullCalendar/timegrid/main.css') }}">
@endsection
@section('content')   
    <?php 
        $id_grupo = $folio = $tipo = $id_curso = $id_cerss = $horario = $turnado = $hini = $id_vulnerable = $servicio = $nombre_curso = $cespe = $fcespe = $nota = 
        $hfin = $termino = $inicio = $id_localidad = $id_muni = $organismo = $modalidad = $efisico = $mvirtual = $lvirtual = $memo = $repre = $tel = "";    $costo = null;   
        if($curso){
            $id_curso = $curso->id;
            $costo = $curso->costo;
            $nombre_curso =  $curso->nombre_curso;
        }
        if(count($alumnos)>0){ 
            $hfin = substr($alumnos[0]->horario, 8, 5);
            $hini = substr($alumnos[0]->horario, 0, 5);
            $id_cerss = $alumnos[0]->id_cerss;    
            $inicio = $alumnos[0]->inicio;
            $termino = $alumnos[0]->termino;
            $id_muni = $alumnos[0]->id_muni;
            $id_localidad = $alumnos[0]->clave_localidad;
            $organismo = $alumnos[0]->organismo_publico;
            $unidad = $alumnos[0]->unidad;
            $folio = $alumnos[0]->folio_grupo;
            $turnado = $alumnos[0]->turnado;   
            $id_vulnerable = $alumnos[0]->id_vulnerable;  
            $modalidad = $alumnos[0]->mod; 
            $tipo = $alumnos[0]->tipo_curso; 
            $efisico = $alumnos[0]->efisico;
            $mvirtual = $alumnos[0]->medio_virtual;
            $lvirtual = $alumnos[0]->link_virtual; 
            $servicio = $alumnos[0]->servicio; 
            $cespe = $alumnos[0]->cespecifico;
            $fcespe = $alumnos[0]->fcespe;  
            $nota = $alumnos[0]->observaciones;
            $memo = $alumnos[0]->mpreapertura; 
            $repre = $alumnos[0]->depen_repre;
            $tel = $alumnos[0]->depen_telrepre;      
        }
        if($turnado!='VINCULACION' AND !$message AND $turnado) $message = "Grupo turnado a  ".$turnado;
        $consec = 1;
    ?>  
    <div class="card-header">
        Preinscripci&oacute;n / Registro de Grupo 
    </div>
    <div class="card card-body">
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <div class="row"> 
            <div>
                <br />                    
            </div>            
            <form method="post" id="frm" enctype="multipart/form-data" style="width: 100%;" >
                @csrf   
                            
                <div>
                    <label><h4>DATOS DEL CURSO </h4></label>
                    <hr />                    
                </div>
                @if($folio)  
                    <div class="form-row">  
                        <div class="form-group col-md-12">
                            <h4 ><b>Grupo No. {{ $folio_grupo}}</b></h4>
                        </div>
                    </div>
                @endif 
                @if (isset($grupo))
                    <div class="row bg-light" style="padding:15px;">
                        <div class="form-group col-md-5">MEMORANDUM DE VALIDACION DEL INSTRUCTOR:&nbsp;&nbsp;<strong>{{ $grupo->instructor_mespecialidad }}</strong></div>
                        <div class="form-group col-md-2">TOTAL DIAS:&nbsp;&nbsp;<strong>{{$grupo->tdias}}</strong></div>
                        <div class="form-group col-md-5">DIAS:&nbsp;&nbsp;<strong>{{$grupo->dia}}</strong></div>
                        @if ($grupo->mexoneracion AND ($grupo->mexoneracion <> '0'))
                        <div class="form-group col-md-5">MEMORÁNDUM DE EXONERACIÓN/REDUCCIÓN:&nbsp;&nbsp;<strong>{{$grupo->mexoneracion}}</strong></div>
                        @endif
                        @if ($grupo->cgeneral!='0')
                        <div class="form-group col-md-3">CONVENIO GENERAL:&nbsp;&nbsp;<strong>{{$grupo->cgeneral}}</strong></div>
                        <div class="from-group col-md-3">FECHA CONVENIO GENERAL:&nbsp;&nbsp;<strong>{{$grupo->fcgen}}</strong></div>
                        @endif
                    </div>
                @endif            
                <div class="form-row">                    
                    <div class="form-group col-md-2">
                        <label>TIPO DE CURSO</label>
                        {{ Form::select('tipo', ['PRESENCIAL'=>'PRESENCIAL','A DISTANCIA'=>'A DISTANCIA'], $tipo, ['id'=>'tipo', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>SERVICIO:</label>
                        {{ Form::select('tcurso', ["CURSO"=>"CURSO","CERTIFICACION"=>"CERTIFICACION"], $servicio, ['id'=>'tcurso','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>UNIDAD/ACCI&Oacute;N M&Oacute;VIL</label>
                        {{ Form::select('unidad', $unidades, $unidad, ['id'=>'unidad','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>MUNICIPIO:</label>
                        {{ Form::select('id_municipio', $municipio, $id_muni, ['id'=>'id_municipio','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="localidad" class="control-label">LOCALIDAD</label>
                        {{ Form::select('localidad', $localidad, $id_localidad, ['id'=>'localidad','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>    
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>MODALIDAD</label>
                        {{ Form::select('modalidad', ['EXT'=>'EXTENSION','CAE'=>'CAE'], $modalidad, ['id'=>'modalidad', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-6">
                        <label>CURSO</label>
                        {{ Form::select('id_curso', $cursos, $id_curso, ['id'=>'id_curso','old'=>'curso', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>  
                    <div class="form-group col-md-2">
                        <label>FECHA INICIO:</label>
                        <input type="date" id="inicio" name="inicio" value="{{$inicio}}" class="form-control" >
                    </div>
                    <div class="form-group col-md-2">
                        <label>FECHA TERMINO:</label>
                        <input type="date" id="termino" name="termino" value="{{$termino}}" class="form-control" >
                    </div> 
                </div>
                <div class="form-row">
                    <div class="d-flex flex-row ">
                        <div class="p-2">HORARIO: <br /><input type="time" name='hini' id='hini' type="text" class="form-control" aria-required="true" value="{{$hini}}"/></div>
                        <div class="p-2"><br />A</div>
                        <div class="p-2"><br /><input type="time" name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{$hfin}}"/></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>ORGANISMO PUBLICO:</label>
                        {{ Form::select('dependencia', $dependencia,$organismo, ['id'=>'dependencia','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div> 
                    <div class="form-group col-md-3">
                        <label for="">NOMBRE DEL REPRESENTANTE:</label>
                        {!! Form::text('repre_depen', $repre, ['id'=>'repre_depen', 'class'=>'form-control']) !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">TELEFONO DEL REPRESENTANTE:</label>
                        {!! Form::text('repre_tel', $tel, ['id'=>'repre_tel', 'class'=>'form-control']) !!}
                    </div>              
                </div>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        @if ($es_vulnerable == 'true')
                        <label><input type="checkbox" value="vulnerable" id="vulnerable_ok" @if($id_vulnerable){{'checked'}}@endif>&nbsp;&nbsp;GRUPO VULNERABLE</label> 
                        @else
                        <label><input type="checkbox" value="vulnerable" id="vulnerable_ok" @if($id_vulnerable){{'checked'}}@endif disabled>&nbsp;&nbsp;GRUPO VULNERABLE</label> 
                        @endif     
                        {{ Form::select('grupo_vulnerable', $grupo_vulnerable, $id_vulnerable, ['id'=>'grupo_vulnerable','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR','disabled'=>'disabled'] ) }}                  
                    </div>  
                    <div class="form-group col-md-5">
                        <label>DOMICILIO, LUGAR O ESPACIO FÍSICO:</label>
                        <input type="text" id="efisico" name="efisico" class="form-control" value="{{$efisico}}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>MEDIO VIRTUAL:</label>
                        {{ Form::select('medio_virtual', $medio_virtual, $mvirtual, ['id'=>'medio_virtual','class' => 'form-control mr-sm-2'] ) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>LINK VIRTUAL:</label>
                        <input name='link_virtual' id='link_virtual' type="url" class="form-control" value="{{$lvirtual}}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">CONVENIO ESPECIFICO:</label>
                        <input type="text" name="cespecifico" id="cespecifico" class="form-control" value="{{$cespe}}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">FECHA CONVENIO ESPECIFICO:</label>
                        <input type="date" name="fcespe" id="fcespe" class="form-control" value="{{$fcespe}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label><input type="checkbox" value="cerss" id="cerss_ok" @if($id_cerss){{'checked'}}@endif>&nbsp;&nbsp;CERSS</label>      
                        {{ Form::select('cerss', $cerss, $id_cerss, ['id'=>'cerss','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR','disabled'=>'disabled'] ) }}                  
                    </div> 
                    <div class="form-group col-md-4">
                        <label>INSTRUCTOR DISPONIBLE:</label>
                        <select name="instructor" id="instructor" class="form-control mr-sm--2">
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
                </div>
                @if ($folio_grupo)
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="">NUMERO DE RECIBO DE PAGO:</label>
                            <input type="text" name="folio_pago" id="folio_pago" class="form-control" placeholder="NUMERO DE RECIBO DE PAGO" value="{{$folio_pago}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">FECHA DEL RECIBO:</label>
                            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" placeholder="FECHA DEL RECIBO" value="{{$fecha_pago}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">COMPROBANTE DE PAGO:</label>
                            <div class="custom-file text-center">
                                <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="fileValidationpdf()">
                                <label class="custom-file-label" for="customFile">PDF COMPROBANTE DE PAGO</label>
                            </div>
                        </div>
                        @if ($comprobante)
                            <div class="form-group col-md-1 mt-3">
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"  target="_blank" data-placement="top" title="COMPROBANTE DE PAGO"
                                    href="{{$comprobante}}">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                </a>
                            </div>
                        @endif
                        <div class="form-group col-md-2 mt-3">
                            <a class="btn btn-dark-green" href="https://www.ilovepdf.com/es/unir_pdf" target="blank">UNIR PDF´s</a>
                        </div>
                    </div>
                @endif
                <br />  
                @can('agenda.vinculacion')
                    @if ($folio_grupo)
                        <div>
                            <label><h4>DE LA APERTURA </h4></label>
                            <hr /> 
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="">MEMORÁNDUM DE SOLICITUD DE APERTURA:</label>
                                {!! Form::text('mapertura', $memo, ['id'=>'mapertura', 'class' => 'form-control', 'placeholder' => 'No. MEMORÁNDUM APERTURA', 'aria-label' => 'No. Memorándum']) !!}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="">OBSERVACIONES:</label>
                                <textarea name="observaciones" id="observaciones" rows="5" class="form-control">{{$nota}}</textarea>
                            </div>
                        </div>
                        <br>
                        <br>
                    @endif
                @endcan
                <div>
                    <label><h4>ALUMNOS</h4></label>
                    <hr />
                </div>
                @if($activar)
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <b><label id="etiqueta">CURP</label></b>
                        <input name='busqueda' id='busqueda' oninput="validarInput(this)" type="text" class="form-control" value="{{old('curp')}}"/>
                        <pre id="resultado"></pre>
                    </div>
                    <div class="col-md-2"><br />
                        <button type="button" id="agregar" class="btn btn-success">AGREGAR</button>                                                                  
                    </div>
                </div>
                @endif
                <div class="form-row">
                    @include('preinscripcion.tableAlumnos')
                </div>
                <br />        
            </form>
        </div>
        <!-- modal para mostrar el calendario -->
        <div class="modal fade" id="modalCalendar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
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
                        <div aria-live="assertive" aria-atomic="true" style="position: relative; top: 0; left: 0;" role="alert" class="toast mt-1 mr-1" data-autohide="false">
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
                                @can('agenda.vinculacion')
                                    @if ($activar AND $folio_grupo)
                                        <button id="btnAgregar" type="button" class="btn btn-success">Agregar</button>
                                        <button id="btnBorrar" class="btn btn-danger">Borrar</button>
                                    @endif
                                @endcan
                                <button type="button" data-dismiss="modal" class="btn btn-outline-danger">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.Content-->
            </div>
        </div>  
    </div>   
    @section('script_content_js') 
        <script src="{{asset('js/preinscripcion/grupo.js')}}"></script>        
        <script src="{{asset('js/preinscripcion/tableAlumnos.js')}}"></script>   
        <script src="{{ asset('fullCalendar/core/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/core/locales-all.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/interaction/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/daygrid/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/list/main.js') }}" defer></script>
        <script src="{{ asset('fullCalendar/timegrid/main.js') }}" defer></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>     	
        <script language="javascript">            
            $(document).ready(function(){    
                $("#agregar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.save')}}",'target':'_self'}); $('#frm').submit(); });
                $("#nuevo").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.nuevo')}}",'target':'_self'}); $('#frm').submit(); });
                $("#update").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.update')}}",'target':'_self'}); $('#frm').submit(); });
                $("#turnar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.turnar')}}",'target':'_self'}); $('#frm').submit(); });
                $("#comprobante").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.comprobante')}}"); $('#frm').submit(); });
                $("#btnremplazo").click(function(){if (confirm("Est\u00E1 seguro de ejecutar la acci\u00F3n?")==true) {$('#frm').attr({'action':"{{route('preinscripcion.grupo.remplazar')}}",'target':'_self'}); $('#frm').submit();}});
                $("#generar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.generar')}}", 'target':'_target'}); $('#frm').submit(); });
                $("#gape").click(function(){ 
                    if ($('#mapertura').val() == ''||$('#mapertura').val() == ' ') {
                        alert("Guarde el número de memorándum de la solicitud de apertura.."); 
                    } else if ($('#observaciones').val() == ''||$('#observaciones').val() == ' ') {
                        alert("Llenar el campo de observaciones..");
                    } else {
                        $('#frm').attr({'action':"{{route('preinscripcion.grupo.gape')}}", 'target':'_target'}); $('#frm').submit(); 
                    }
                });
            });
        </script>
        <script type="text/javascript">
            var calendarEl = document.getElementById('calendar');
            var calendar, idEvento, objEvento;
            $('#btnShowCalendar').click(function(e) {
                limpiarFormulario();
                inicializarCalendar();
                let hora = document.getElementById("txtHora");
                let horaTermino = document.getElementById("txtHoraTermino");
                hora.disabled = false;
                horaTermino.disabled = false;
                $('#titleCalendar').html('<?php echo $folio_grupo; ?>');
                $("#modalCalendar").modal("show");
            });
            $('#btnClean').click(function() {
                limpiarFormulario();
            });
            $('#btnAgregar').click(function() {
                objEvento = null;
                objEvento = recolectarDatos("POST");
                if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == '' || $('#txtHoraTermino').val() == '' || $('#instructor').val() == '') {
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
            $('#btnBorrar').click(function() {
                $.ajax({
                    type: 'POST',
                    url: "/preinscripcion/calendario/eliminar",
                    data: {
                        id:$('#txtId').val(),
                        '_token': $("meta[name='csrf-token']").attr("content")
                    },
                    success: function(msg) {
                        console.log(msg);
                        calendar.refetchEvents();
                        limpiarFormulario();
                    },
                    error: function(jqXHR, textStatus) {
                        // console.log(textStatus);
                        console.log(jqXHR);
                        alert( "Hubo un error: " + jqXHR.status );
                    }
                });
            });
            function limpiarFormulario() {
                $('#formCalendario')[0].reset();
                idEvento = null; 
                objEvento = null;
                horahini= '<?php echo $hini; ?>' ;
                horafin= '<?php echo $hfin; ?>';
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
                    defaultDate: '<?php echo $inicio; ?>',
                    events: ("{{ url('/preinscripcion/calendarioShow') }}" + ('/' + '<?php echo $folio_grupo; ?>')),
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
                });
                calendar.setOption('locale', 'es');
                calendar.render();
            }
            function recolectarDatos(method) {
                nuevoEvento = []
                nuevoEvento = {
                   // title: '<?php echo $nombre_curso; ?>',
                    title: '',
                    start: $('#fecha_firma').val() + ' ' + $('#txtHora').val(),
                    end: $('#fecha_termino').val() + ' ' + $('#txtHoraTermino').val(),
                    textColor: '#000000',
                    id_curso: '<?php echo $folio_grupo; ?>',
                    id_instructor: document.getElementById('instructor').value.split(',')[0],
                    '_token': $("meta[name='csrf-token']").attr("content"),
                    '_method': method
                }
                return nuevoEvento;
            }
            function EnviarInformacion(accion, objEvento, tipo) {
                $.ajax({
                    type: 'POST',
                    url: "/preinscripcion/calendario/guardar",
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
            // formato fechas
            var dateFormat = "dd-mm-yy", from = $("#fecha_firma").datepicker({
                        defaultDate: ('<?php echo $inicio; ?>').split('-').reverse().join('-'),
                        changeMonth: true,
                        minDate: ('<?php echo $inicio; ?>').split('-').reverse().join('-'),
                        maxDate: ('<?php echo $termino; ?>').split('-').reverse().join('-'),
                        numberOfMonths: 1,
                        dateFormat: 'dd-mm-yy',
                        beforeShow: function (input, inst) {
                            var rect = input.getBoundingClientRect();
                            setTimeout(function () {
                                inst.dpDiv.css({ top: rect.top + 35, left: rect.left + 0 });
                            }, 0);
                        }
            }).on("change", function() { to.datepicker("option", "minDate", getDate(this));}),
            to = $("#fecha_termino").datepicker({
                        defaultDate: ('<?php echo $inicio; ?>').split('-').reverse().join('-'),
                        changeMonth: true,
                        minDate: ('<?php echo $inicio; ?>').split('-').reverse().join('-'),
                        maxDate: ('<?php echo $termino; ?>').split('-').reverse().join('-'),
                        numberOfMonths: 1,
                        dateFormat: 'dd-mm-yy',
                        beforeShow: function (input, inst) {
                            var rect = input.getBoundingClientRect();
                            setTimeout(function () {
                                inst.dpDiv.css({ top: rect.top + 35, left: rect.left + 0 });
                            }, 0);
                        }
            }).on("change", function() {
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
        </script>
    @endsection
@endsection
