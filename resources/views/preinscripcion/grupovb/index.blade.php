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
    <style>
        .custom-font-size { font-size: 18px; }
        #tblAlumnos tr th{ text-align: center; padding:5px;}
        .btn { font-size: 11px;}
    </style>
@endsection
@section('content')
    @php
        $turnado = $hini = $hfin = $inicio = $termino = $nombre_curso = $organismo = $id_gvulnerable= $checked = null;
        $enviar_vobo = $turnar = false;
        if(isset($grupo)){
            $turnado = $grupo->turnado_grupo;
            $hini = $grupo->hini;
            $hfin = $grupo->hfin;
            $inicio = $grupo->inicio;
            $termino = $grupo->termino;
            $nombre_curso = $grupo->nombre_curso;
            $organismo = $grupo->depen;
            $id_gvulnerable = $grupo->id_gvulnerable;
            if($id_gvulnerable && $grupo->clave !== null ) $checked = 'checked';
            elseif($grupo->clave == null and $es_vulnerable) $checked = 'checked';
            if(!$grupo->vb_dg and $grupo->turnado_vb == "UNIDAD") $enviar_vobo= true ;
            if($grupo->vb_dg and $grupo->turnado_vb == "UNIDAD") $turnar= true ;

        }
        if($turnado!='VINCULACION' AND !$message AND $turnado) $message = "Grupo turnado a  ".$turnado;
        $consec = 1;
    @endphp
    <div class="card-header">
        Preinscripci&oacute;n / Registro de Grupo VoBo
    </div>
    <div class="card card-body">
        @if($message ?? '')
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message ?? '' }}</p>
                </div>
            </div>
        @endif
        @if($msg_rechazo ?? '')
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $msg_rechazo ?? '' }}</p>
                </div>
            </div>
        @endif
        <div class="row">
            <form method="post" id="frm" enctype="multipart/form-data" style="width: 100%;" >
                @csrf
                @if(isset($grupo->folio_grupo))
                    <div class="form-row p-0 mt-2">
                        <div class="form-group col-md-12 form-inline">
                            <h4 ><b>Grupo No.
                            {{ html()->text('folio_grupo', $grupo->folio_grupo)
                                ->id('folio_grupo')
                                ->class('form-control custom-font-size col-md-5')
                                ->attribute('aria-label', 'CLAVE DEL CURSO')
                                ->required()
                                ->readonly()
                            }}
                            </b></h4>
                        </div>
                    </div>
                @endif
                <br/>
                @if(isset($grupo))
                    @if(isset($instructor))
                        <h5>DEL INSTRUCTOR</h5>
                        <div class="row bg-light form-inline" style="padding:15px 0 15px 0; text-indent:1.8em; line-height: 2.1em;">
                            @if(isset($instructor->tipo_honorario))
                                <span>RÉGIMEN :&nbsp;&nbsp;<strong>{{$instructor->tipo_honorario}}</strong></span>
                            @endif
                            <span>&nbsp;&nbsp;
                                MEMORÁNDUM DE VALIDACIÓN:
                                @if($ValidaInstructorPDF and isset($grupo->instructor_mespecialidad ))
                                    <a class="p-0 m-0 text-danger" href="{{$ValidaInstructorPDF}}" target="_blank">
                                        <i  class="far fa-file-pdf  fa-1x text-danger"  title='PDF VALIDACIÓN DEL INSTRUCTOR.'></i>
                                        &nbsp;&nbsp; {{ $grupo->instructor_mespecialidad }}
                                    </a>
                                @else
                                    <i  class="far fa-file-pdf  fa-1x text-mute"  title='VALIDACIÓN DEL INSTRUCTOR.'></i>
                                    <strong>&nbsp;&nbsp; {{ $grupo->instructor_mespecialidad }}</strong>
                                @endif
                            </span>
                        </div>
                    @endif
                    @if($grupo->tdias)
                        <h5>DEL CURSO</h5>
                        <div class="row bg-light form-inline" style="padding:15px 0 15px 0; text-indent:1.8em; line-height: 2.1em;">
                            @if($grupo->clave)<span>CLAVE:&nbsp;&nbsp;<strong>{{$grupo->clave}}</strong></span>@endif
                            @if ($grupo->mexoneracion AND ($grupo->mexoneracion <> '0'))
                                <span>MEMORÁNDUM DE EXONERACIÓN/REDUCCIÓN:&nbsp;&nbsp;<strong>{{$grupo->mexoneracion}}</strong></span>
                            @elseif ($grupo->exo_nrevision )
                                <span>EXONERACIÓN/REDUCCIÓN No. REVISIÓN:&nbsp;&nbsp;<strong>{{$grupo->exo_nrevision}}</strong></span>
                            @elseif ($grupo->tipo != 'PINS' )
                                <span>EXONERACIÓN/REDUCCIÓN:&nbsp;&nbsp;<strong class="text-danger">TRÁMITE PENDIENTE</strong></span>
                            @endif
                            @if($grupo->tdias)<span>TOTAL DIAS:&nbsp;&nbsp;<strong>{{$grupo->tdias}}</strong></span>@endif
                            @if($grupo->dia)<span>DIAS:&nbsp;&nbsp;<strong>{{$grupo->dia}}</strong></span>@endif
                            @if ($grupo->cgeneral!='0')
                                <span>CONVENIO GENERAL:&nbsp;&nbsp;<strong>{{$grupo->cgeneral}}</strong></span>
                                <span>FECHA CONVENIO GENERAL:&nbsp;&nbsp;<strong>{{$grupo->fcgen}}</strong></span>
                            @endif
                        </div>
                    @endif
                @endif
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>TIPO DE CURSO</label>
                        {{ html()->select('tipo', ['PRESENCIAL'=>'PRESENCIAL','A DISTANCIA'=>'A DISTANCIA'], $grupo->tcapacitacion ?? '')
                            ->id('tipo')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>CURSO/CERTIFICACIÓN:</label>
                        {{ html()->select('tcurso', ["CURSO"=>"CURSO","CERTIFICACION"=>"CERTIFICACION"], $grupo->tipo_curso ?? '')
                            ->id('tcurso')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>UNIDAD/ACCIÓN MÓVIL</label>
                        {{ html()->select('unidad', $unidades, $grupo->unidad ?? '')
                            ->id('unidad')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR') }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>MUNICIPIO:</label>
                        {{ html()->select('id_municipio', $municipio, $grupo->id_municipio ?? '')
                            ->id('id_municipio')
                            ->class('form-control mr-sm-2')
                            ->placeholder('- SELECCIONAR -') }}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="localidad" class="control-label">LOCALIDAD</label>
                        {{ html()->select('localidad', $localidad, $grupo->clave_localidad ?? '')
                            ->id('localidad')
                            ->class('form-control mr-sm-2')
                            ->placeholder('- SELECCIONAR -') }}
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>MODALIDAD</label>
                        {{ html()->select('modalidad', ['EXT'=>'EXTENSION','CAE'=>'CAE'], $grupo->mod ?? '')
                            ->id('modalidad')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR') }}
                    </div>
                    <div class="form-group col-md-6">
                        <label>CURSO</label>
                        {{ html()->select('id_curso', $cursos, $grupo->id_curso ?? '')
                            ->id('id_curso')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>FECHA INICIO:</label>
                        {{ html()->date('inicio', $grupo->inicio ?? '')
                            ->id('inicio')
                            ->class('form-control') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>FECHA TERMINO:</label>
                        {{ html()->date('termino', $grupo->termino ?? '')
                            ->id('termino')
                            ->class('form-control') }}
                    </div>
                </div>

                <div class="form-row">
                    <div class="d-flex flex-row ">
                        <div class="p-2">HORARIO: <br />{{ html()->time('hini', $grupo->hini ?? '')
                            ->id('hini')
                            ->class('form-control')
                            ->attribute('aria-required', 'true') }}</div>
                        <div class="p-2"><br />A</div>
                        <div class="p-2"><br />{{ html()->time('hfin', $grupo->hfin ?? '')
                            ->id('hfin')
                            ->class('form-control')
                            ->attribute('aria-required', 'true') }}</div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>ORGANISMO PUBLICO:</label>
                        {{ html()->select('dependencia', $dependencia, $grupo->depen ?? '')
                            ->id('dependencia')
                            ->class('form-control mr-sm-2')
                            ->placeholder('- SELECCIONAR -') }}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">NOMBRE DEL REPRESENTANTE:</label>
                        {{ html()->text('repre_depen', $grupo->depen_repre ?? '')
                            ->id('repre_depen')
                            ->class('form-control') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">TELEFONO REPRESENT:</label>
                        {{ html()->text('repre_tel', $grupo->depen_telrepre ?? '')
                            ->id('repre_tel')
                            ->class('form-control col-md-10') }}
                    </div>
                    <div class="form-group col-md-4">
                        @if($es_vulnerable==true)
                            <label>{{ html()->checkbox('vulnerable_ok', $checked ?? false)->value('vulnerable')->id('vulnerable_ok') }}&nbsp;&nbsp;GRUPO VULNERABLE</label>
                            {{ html()->select('grupo_vulnerable', $grupo_vulnerable, $grupo->id_gvulnerable ?? '')
                                ->id('grupo_vulnerable')
                                ->class('form-control mr-sm-2')
                                ->placeholder('SELECIONAR') }}
                        @else
                            <label>{{ html()->checkbox('vulnerable_ok', !empty($grupo->id_gvulnerable))->value('vulnerable')->id('vulnerable_ok')->disabled() }}&nbsp;&nbsp;GRUPO VULNERABLE</label>
                            {{ html()->select('grupo_vulnerable', $grupo_vulnerable, '')
                                ->id('grupo_vulnerable')
                                ->class('form-control mr-sm-2')
                                ->placeholder('SELECIONAR')
                                ->disabled() }}
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label>DOMICILIO, LUGAR O ESPACIO FÍSICO:</label>
                        {{ html()->textarea('efisico', $grupo->efisico ?? '')
                            ->id('efisico')
                            ->class('form-control')
                            ->rows(2) }}
                    </div>
                    <div class="form-group col-md-2">
                        <label>MEDIO VIRTUAL:</label>
                        {{ html()->select('medio_virtual', $medio_virtual, $grupo->medio_virtual ?? '')
                            ->id('medio_virtual')
                            ->class('form-control mr-sm-2') }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>LINK VIRTUAL:</label>
                        {{ html()->input('url', 'link_virtual', $grupo->link_virtual ?? '')
                            ->id('link_virtual')
                            ->class('form-control') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">CONVENIO ESPECIFICO:</label>
                        {{ html()->text('cespecifico', $grupo->cespecifico ?? '')
                            ->id('cespecifico')
                            ->class('form-control') }}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">FECHA CONV. ESPECIFICO:</label>
                        {{ html()->date('fcespe', $grupo->fcespe ?? '')
                            ->id('fcespe')
                            ->class('form-control') }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ html()->checkbox('cerss_ok', !empty($grupo->id_cerss))->value('cerss')->id('cerss_ok') }}&nbsp;&nbsp;CERSS</label>
                        {{ html()->select('cerss', $cerss, $grupo->id_cerss ?? '')
                            ->id('cerss')
                            ->class('form-control mr-sm-2')
                            ->placeholder('SELECIONAR')
                            ->disabled() }}
                    </div>
                </div>

                @if($instructor)
                    <div class="form-row bg-light form-inline p-3 mt-2 mb-4">
                        <label>INSTRUCTOR ASIGNADO:<b>&nbsp;&nbsp; {{ $instructor->instructor }}</b></label>
                    </div>
                @endif

                @if($folio_grupo)
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="">NUMERO DE RECIBO DE PAGO:</label>
                            <input type="text" name="folio_pago" id="folio_pago" class="form-control" placeholder="NUMERO DE RECIBO DE PAGO" value="{{ $grupo->folio_pago ?? '' }}" @if($grupo->es_recibo_digital) disabled @endif>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">FECHA DEL RECIBO:</label>
                            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" placeholder="FECHA DEL RECIBO" value="{{ $grupo->fecha_pago ?? ''}}"  @if($grupo->es_recibo_digital) disabled @endif>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">COMPROBANTE DE PAGO:</label>
                            <div class="custom-file text-center">
                                <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="fileValidationpdf()"  @if(!$recibo_nulo) disabled @endif>
                                <label class="custom-file-label" for="customFile">PDF COMPROBANTE DE PAGO</label>
                            </div>
                        </div>
                        <div class="form-group col-md-1 mt-3 ml-3">
                            @if($grupo->comprobante_pago ?? '')
                                <a class="nav-link" href="{{$grupo->comprobante_pago}}" target="_blank" title="RECIBO DE PAGO PDF">
                                    <i  class="far fa-file-pdf  fa-3x text-danger"></i>
                                </a>
                            @else
                                <i  class="far fa-file-pdf  fa-3x text-muted mt-1"  title='ARCHIVO NO DISPONIBLE.'></i>
                            @endif
                        </div>
                        @if(!$recibo)
                            <div class="form-group col-md-2 mt-4">
                                <a class="btn btn-dark-green" href="https://www.ilovepdf.com/es/unir_pdf" target="blank">UNIR PDF´s</a>
                            </div>
                        @endif
                    </div>
                @endif
                <br />
                @can('agenda.vinculacion')
                    @if ($folio_grupo)
                        <div>
                            <h4>DE LA APERTURA </h4>
                            <hr />
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="">MEMORÁNDUM DE SOLICITUD DE APERTURA:</label>
                                {!! Form::text('mapertura', $grupo->mpreapertura ?? '', ['id'=>'mapertura', 'class' => 'form-control', 'placeholder' => 'No. MEMORÁNDUM APERTURA', 'aria-label' => 'No. Memorándum']) !!}
                            </div>
                            <div class="form-group col-md-3">
                                <label>PLANTEL:</label>
                                {{ Form::select('plantel', $planteles, $grupo->plantel ?? '', ['id'=>'plantel','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                            </div>
                            <div class="form-group col-md-4">
                                <label>PROGRAMA ESTRAT&Eacute;GICO:</label>
                                {{ Form::select('programa', $programas, $grupo->programa ?? '', ['id'=>'programa','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="">OBSERVACIONES:</label>
                                <textarea name="observaciones" id="observaciones" rows="5" class="form-control">{{$grupo->obs_vincula}}</textarea>
                            </div>
                        </div>
                        {{-- <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="">SOLICITANTE (NOMBRE,CARGO):</label>

                                {!! Form::text('solicita', $grupo->solicita ?? '', ['id'=>'solicita', 'class' => 'form-control', 'placeholder' => 'NOMBRE,CARGO', 'aria-label' => 'SOLICITANTE', $grupo->editar_solicita ? '' : 'readonly' => 'readonly']) !!}
                            </div>
                        </div> --}}
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
                    @include('preinscripcion.grupovb.tableAlumnos')
                </div>
                <br />
                {{ html()->hidden('IDE', $grupo->IDE ?? '') }}
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
                                    @if ($activar AND $folio_grupo AND $enviar_vobo)
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
        <script src="{{asset('js/preinscripcion/grupovb.js')}}"></script>
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
                $('#costoX').on('input', function() {
                    var text = $(this).val();
                    $('.costo').val(text);
                });

                // BOTONES DE PDF
                //Temporalmente se comentó hasta que se homologue se libera
                $("#gen_acta_acuerdo").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.acuerdo_pdf')}}",'target':'_blank'}); $('#frm').submit(); });
                $("#gen_convenio_esp").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.convenio_pdf')}}",'target':'_blank'}); $('#frm').submit(); });

                $("#agregar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupovobo.save')}}",'target':'_self'}); $('#frm').submit(); });
                $("#nuevo").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupovobo.nuevo')}}",'target':'_self'}); $('#frm').submit(); });
                $("#update").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupovobo.update')}}",'target':'_self'}); $('#frm').submit(); });

                $("#vobo").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupovobo.vobo')}}",'target':'_self'}); $('#frm').submit(); });

                $("#turnar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupovobo.turnar')}}",'target':'_self'}); $('#frm').submit(); });
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
                if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == '' || $('#txtHoraTermino').val() == '' ) {
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
                    title: '<?php echo addslashes($nombre_curso); ?>',
                    start: $('#fecha_firma').val() + ' ' + $('#txtHora').val(),
                    end: $('#fecha_termino').val() + ' ' + $('#txtHoraTermino').val(),
                    textColor: '#000000',
                    id_curso: '<?php echo $folio_grupo; ?>',
                    //id_instructor: document.getElementById('instructor').value.split(',')[0],
                    '_token': $("meta[name='csrf-token']").attr("content"),
                    '_method': method
                }
                return nuevoEvento;
            }
            function EnviarInformacion(accion, objEvento, tipo) {
                $.ajax({
                    type: 'POST',
                    url: "/preinscripcion/grupovb/calendario/guardar",
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

            //By Jose Luis Moreno Arcos
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
                let valSelect = document.getElementById('subirPDF').value;
                var showPDF = document.getElementById("verPdfLink");
                var partes = valSelect.split('?');
                switch (partes[0]) {
                    case '0':
                        $("#verPdfLink").addClass('d-none');
                        break;
                    case '1':
                        if (partes[1] != '') {
                            showPDF.href = partes[1];
                            $("#verPdfLink").removeClass('d-none');
                        }else{
                            $("#verPdfLink").addClass('d-none');
                        }
                        break;
                    case '2':
                        if (partes[1] != '') {
                            showPDF.href = partes[1];
                            $("#verPdfLink").removeClass('d-none');
                        }else{
                            $("#verPdfLink").addClass('d-none');
                        }
                        break;
                    case '3':
                        if (partes[1] != '') {
                            showPDF.href = partes[1];
                            $("#verPdfLink").removeClass('d-none');
                        }else{
                            $("#verPdfLink").addClass('d-none');
                        }
                        break;
                    case '4':
                        if (partes[1] != '') {
                            showPDF.href = partes[1];
                            $("#verPdfLink").removeClass('d-none');
                        }else{
                            $("#verPdfLink").addClass('d-none');
                        }
                        break;
                    default:
                        break;
                }
            }

            //Enviar los archivos al backend
            function UploadPDF(event, status_dpto) {

                event.preventDefault();
                //Validar el estatus del expediente unico
                // if (status_dpto != 'CAPTURA') {alert("EL EXPEDIENTE UNICO HA SIDO ENVIADO A DTA PARA VALIDACIÓN\nPOR LO TANTO NO ES POSIBLE CARGAR EL DOCUMENTO."); return false;}

                 //Obtenermos el valor del input archivo
                let valSelect = document.getElementById('subirPDF').value;
                let inputFile = document.getElementById('pdfInputDoc');
                let partes = valSelect.split('?');

                if (partes[0] == '0'){ alert('SELECCIONA UNA OPCIÓN'); return;}
                // if (partes[1] !== '') {alert("EL ARCHIVO YA SE ENCUENTRA CARGADO");return;  }
                if(partes[1] !== ''){
                    if (confirm("YA HAS REALIZADO ESTA ACCIÓN ANTERIORMENTE ¿DESEAS REEMPLAZAR EL ARCHIVO CON UNO NUEVO?")) {
                    } else return;
                }

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
                    url: "{{ route('preinscripcion.grupo.uploadpdf') }}",
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
