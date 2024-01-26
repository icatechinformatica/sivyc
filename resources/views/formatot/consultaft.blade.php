<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>
        @font-face {
    font-family: 'Montserrat', sans-serif;
    font-family: 'Playfair Display', serif;
}
        .pdf{ font-size: 11px; font-family: 'Montserrat', sans-serif;}
    </style>
@endsection
@section('content')
    

    <div class="card-header">
        Consulta de Formato T

    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if($message)
            <div class="row">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif       
        {{ Form::open(['method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            <div class="row form-inline">                
                {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4','title' => 'UNIDAD','placeholder' => '-SELECCIONAR-']) }}
                {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control mr-sm-4', 'placeholder' => '# MEMORÁNDUM', 'size' => 38]) }}
                {{ Form::date('fecha', $fecha , ['id'=>'fecha', 'class' => 'form-control datepicker  mr-sm-4', 'placeholder' => 'FECHA TURNADO', 'title' => 'FECHA TURNADO']) }}
                <div class="form-check mr-sm-4">
                    <input class="form-check-input custom-checkbox checkbox-lg" type="checkbox" value="1"  name="mes" @if($mes) checked @endif>MES COMPLETO
                </div>
                {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4']) }}
                {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4']) }}
            </div>
        {!! Form::close() !!}       
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @foreach($cols as $col)
                                <th scope="col" class="text-center align-middle" >{{$col}}</th>          
                            @endforeach   
                        </tr>
                    </thead>
                    @if($data)
                    <?php $i=1;   ?>
                    <tbody>
                        @foreach($data as $d)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>
                                <div style="width:180px; word-wrap: break-word">
                                @canany(['vista.formatot.unidades.indice', 'vista.validacion.enlaces.dta', 'vista.validacion.direccion.dta'])
                                    @if($d->memo_turnado_dta)
                                        <a class="nav-link pt-0 " href="{{$d->memo_turnado_dta}}" target="_blank">
                                            <i  class="far fa-file-pdf  fa-1x text-danger  pt-0 "  title='DESCARGAR MEMORÁNDUM {{$d->nmemo_turnado_dta}}.'><span class='text-dark pdf'> {{$d->nmemo_turnado_dta}}<span></i>
                                        </a>
                                     @endif
                                     @if($d->memo_turnado_unidad)
                                        <a class="nav-link pt-0 " href="{{$d->memo_turnado_unidad}}" target="_blank">
                                            <i  class="far fa-file-pdf  fa-1x text-danger"  title='DESCARGAR MEMORÁNDUM.'><span class='text-dark pdf'> MEMO CONTESTACIÓN<span></i>
                                        </a>
                                     @endif 
                                @endcanany
                                @canany([ 'vista.validacion.direccion.dta','vista.revision.validacion.planeacion.indice'])
                                     @if($d->memo_turnado_planeacion)
                                        <a class="nav-link pt-0 " href="{{$d->memo_turnado_planeacion}}" target="_blank">
                                            <i  class="far fa-file-pdf  fa-1x text-danger  pt-0 "  title='DESCARGAR MEMORÁNDUM {{$d->nmemo_turnado_planeacion}}.'><span class='text-dark pdf'>{{$d->nmemo_turnado_planeacion}}<span></i>
                                        </a>
                                     @endif 
                                     @if($d->memo_cerrado_planeacion)
                                        <a class="nav-link pt-0 " href="{{$d->memo_cerrado_planeacion}}" target="_blank">
                                            <i  class="far fa-file-pdf  fa-1x text-danger"  title='DESCARGAR MEMORÁNDUM {{$d->nmemo_cerrado_planeacion}}.'><span class='text-dark pdf'> {{$d->nmemo_cerrado_planeacion}}<span></i>
                                        </a>
                                     @endif                                                                       
                                @endcanany                         
                                <div style="width:100px; word-wrap: break-word">
                                </td>
                                <td>{{ $d->unidad }}</td>
                                <td>{{ $d->plantel }}</td>
                                <td><div style="width:150px; word-wrap: break-word">{{ $d->espe }}</div></td>
                                <td>
                                    <div style="width:200px; word-wrap: break-word">{{ $d->curso }}</div>
                                </td>
                                <td>
                                    <div style="width:100px; word-wrap: break-word">{{ $d->clave }}</div>
                                </td>
                                <td>{{ $d->mod }}</td>
                                <td>{{ $d->dura }}</td>
                                <td>{{ $d->turno }}</td>
                                <td>{{ $d->diai }}</td>
                                <td>{{ $d->mesi }}</td>
                                <td>{{ $d->diat }}</td>
                                <td>{{ $d->mest }}</td>
                                <td>{{ $d->pfin }}</td>
                                <td>{{ $d->horas }}</td>
                                <td>{{ $d->dia }}</td>
                                <td><div style="width:100px; word-wrap: break-word">{{ $d->horario }}</div></td>
                                <td>{{ $d->tinscritos }}</td>
                                <td>{{ $d->imujer }}</td>
                                <td>{{ $d->ihombre }}</td>
                                <td>{{ $d->egresado }}</td>
                                <td>{{ $d->emujer }}</td>
                                <td>{{ $d->ehombre }}</td>
                                <td>{{ $d->desertado }}</td>
                                <td>{{ $d->costo }}</td>
                                <td>{{ $d->ctotal }}</td>
                                <td>{{ $d->cuotamixta}}</td>
                                <td>{{ $d->etmujer }}</td>
                                <td>{{ $d->ethombre }}</td>
                                <td>{{ $d->epmujer }}</td>
                                <td>{{ $d->ephombre }}</td>
                                <td>
                                    <div style="width:100px; word-wrap: break-word">{{ $d->cespecifico }}
                                    </div>
                                </td>
                                <td>
                                    <div style="width:100px; word-wrap: break-word">{{ $d->mvalida }}
                                    </div>
                                </td>
                                <td>
                                    <div style="width:200px; word-wrap: break-word">{{ $d->efisico }}
                                    </div>
                                </td>
                                <td>
                                    <div style="width:180px; word-wrap: break-word">{{ $d->nombre }}
                                    </div>
                                </td>
                                <td>{{ $d->grado_profesional }}</td>
                                <td>{{ $d->estatus }}</td>
                                <td>{{ $d->sexo }}</td>
                                <td>{{ $d->memorandum_validacion }}</td>
                                <td>{{ $d->mexoneracion }}</td>
                                <td>{{ $d->empleado }}</td>
                                <td>{{ $d->desempleado }}</td>
                                <td>{{ $d->discapacidad }}</td>
                                <td>{{ $d->migrante }}</td>
                                <td>{{ $d->indigena }}</td>
                                <td>{{ $d->etnia }}</td>
                                <td>{{ $d->programa }}</td>
                                <td>{{ $d->muni }}</td>
                                <td>{{ $d->ze }}</td>
                                <td>{{ $d->region }}</td>
                                <td>
                                    <div style="width:300px; word-wrap: break-word">{{ $d->depen }}</div>
                                </td>
                                <td>{{ $d->cgeneral }}</td>
                                <td>{{ $d->sector }}</td>
                                <td>{{ $d->mpaqueteria }}</td>
                                @if ($d->grupo != NULL)
                                    <td>{{ $d->grupo }}</td>
                                @else
                                    <td>NINGUNO</td>
                                @endif
                                {{-- RUBRO FEDERAL --}}
                                <td>{{ $d->iem1f }}</td>
                                <td>{{ $d->ieh1f }}</td>
                                <td>{{ $d->iem2f }}</td>
                                <td>{{ $d->ieh2f }}</td>
                                <td>{{ $d->iem3f }}</td>
                                <td>{{ $d->ieh3f }}</td>
                                <td>{{ $d->iem4f }}</td>
                                <td>{{ $d->ieh4f }}</td>
                                <td>{{ $d->iem5f }}</td>
                                <td>{{ $d->ieh5f }}</td>
                                <td>{{ $d->iem6f }}</td>
                                <td>{{ $d->ieh6f }}</td>
                                <td>{{ $d->iem7f }}</td>
                                <td>{{ $d->ieh7f }}</td>
                                <td>{{ $d->iem8f }}</td>
                                <td>{{ $d->ieh8f }}</td>

                                <td>{{ $d->iesm1 }}</td>
                                <td>{{ $d->iesh1 }}</td>
                                <td>{{ $d->iesm2 }}</td>
                                <td>{{ $d->iesh2 }}</td>
                                <td>{{ $d->iesm3 }}</td>
                                <td>{{ $d->iesh3 }}</td>
                                <td>{{ $d->iesm4 }}</td>
                                <td>{{ $d->iesh4 }}</td>
                                <td>{{ $d->iesm5 }}</td>
                                <td>{{ $d->iesh5 }}</td>
                                <td>{{ $d->iesm6 }}</td>
                                <td>{{ $d->iesh6 }}</td>
                                <td>{{ $d->iesm7 }}</td>
                                <td>{{ $d->iesh7 }}</td>
                                <td>{{ $d->iesm8 }}</td>
                                <td>{{ $d->iesh8 }}</td>
                                <td>{{ $d->iesm9 }}</td>
                                <td>{{ $d->iesh9 }}</td>

                                <td>{{ $d->aesm1 }}</td>
                                <td>{{ $d->aesh1 }}</td>
                                <td>{{ $d->aesm2 }}</td>
                                <td>{{ $d->aesh2 }}</td>
                                <td>{{ $d->aesm3 }}</td>
                                <td>{{ $d->aesh3 }}</td>
                                <td>{{ $d->aesm4 }}</td>
                                <td>{{ $d->aesh4 }}</td>
                                <td>{{ $d->aesm5 }}</td>
                                <td>{{ $d->aesh5 }}</td>
                                <td>{{ $d->aesm6 }}</td>
                                <td>{{ $d->aesh6 }}</td>
                                <td>{{ $d->aesm7 }}</td>
                                <td>{{ $d->aesh7 }}</td>
                                <td>{{ $d->aesm8 }}</td>
                                <td>{{ $d->aesh8 }}</td>
                                <td>{{ $d->aesm9 }}</td>
                                <td>{{ $d->aesh9 }}</td>

                                <td>{{ $d->naesm1 }}</td>
                                <td>{{ $d->naesh1 }}</td>
                                <td>{{ $d->naesm2 }}</td>
                                <td>{{ $d->naesh2 }}</td>
                                <td>{{ $d->naesm3 }}</td>
                                <td>{{ $d->naesh3 }}</td>
                                <td>{{ $d->naesm4 }}</td>
                                <td>{{ $d->naesh4 }}</td>
                                <td>{{ $d->naesm5 }}</td>
                                <td>{{ $d->naesh5 }}</td>
                                <td>{{ $d->naesm6 }}</td>
                                <td>{{ $d->naesh6 }}</td>
                                <td>{{ $d->naesm7 }}</td>
                                <td>{{ $d->naesh7 }}</td>
                                <td>{{ $d->naesm8 }}</td>
                                <td>{{ $d->naesh8 }}</td>
                                <td>{{ $d->naesm9 }}</td>
                                <td>{{ $d->naesh9 }}</td>
                                <td>
                                    <div style="width:800px; word-wrap: break-word">{{ $d->tnota }}
                                    </div>
                                </td>
                                {{-- RUBRO ESTATAL --}}
                                <td>{{ $d->tinscritos }}</td>
                                <td>{{ $d->imujerest }}</td>
                                <td>{{ $d->ihombreest }}</td>
                                <td>{{ $d->ilgbt }}</td>
                                <td>{{ $d->egresado }}</td>
                                <td>{{ $d->emujer }}</td>
                                <td>{{ $d->ehombre }}</td>
                                <td>{{ $d->elgbt }}</td>
                                <td>{{ $d->etmujerest }}</td>
                                <td>{{ $d->ethombreest }}</td>
                                <td>{{ $d->etlgbt }}</td>
                                <td>{{ $d->epmujerest }}</td>
                                <td>{{ $d->ephombreest }}</td>
                                <td>{{ $d->eplgbt }}</td>

                                <td>{{ $d->iem1 }}</td>
                                <td>{{ $d->ieh1 }}</td>
                                <td>{{ $d->iel1 }}</td>
                                <td>{{ $d->iem2 }}</td>
                                <td>{{ $d->ieh2 }}</td>
                                <td>{{ $d->iel2 }}</td>
                                <td>{{ $d->iem3 }}</td>
                                <td>{{ $d->ieh3 }}</td>
                                <td>{{ $d->iel3 }}</td>
                                <td>{{ $d->iem4 }}</td>
                                <td>{{ $d->ieh4 }}</td>
                                <td>{{ $d->iel4 }}</td>

                                <td>{{ $d->iesmest1 }}</td>
                                <td>{{ $d->ieshest1 }}</td>
                                <td>{{ $d->ieslest1 }}</td>
                                <td>{{ $d->iesmest2 }}</td>
                                <td>{{ $d->ieshest2 }}</td>
                                <td>{{ $d->ieslest2 }}</td>
                                <td>{{ $d->iesmest3 }}</td>
                                <td>{{ $d->ieshest3 }}</td>
                                <td>{{ $d->ieslest3 }}</td>
                                <td>{{ $d->iesmest4 }}</td>
                                <td>{{ $d->ieshest4 }}</td>
                                <td>{{ $d->ieslest4 }}</td>
                                <td>{{ $d->iesmest5 }}</td>
                                <td>{{ $d->ieshest5 }}</td>
                                <td>{{ $d->ieslest5 }}</td>
                                <td>{{ $d->iesmest6 }}</td>
                                <td>{{ $d->ieshest6 }}</td>
                                <td>{{ $d->ieslest6 }}</td>
                                <td>{{ $d->iesmest7 }}</td>
                                <td>{{ $d->ieshest7 }}</td>
                                <td>{{ $d->ieslest7 }}</td>
                                <td>{{ $d->iesmest8 }}</td>
                                <td>{{ $d->ieshest8 }}</td>
                                <td>{{ $d->ieslest8 }}</td>
                                <td>{{ $d->iesmest9 }}</td>
                                <td>{{ $d->ieshest9 }}</td>
                                <td>{{ $d->ieslest9 }}</td>

                                <td>{{ $d->aesmest1 }}</td>
                                <td>{{ $d->aeshest1 }}</td>
                                <td>{{ $d->aeslest1 }}</td>
                                <td>{{ $d->aesmest2 }}</td>
                                <td>{{ $d->aeshest2 }}</td>
                                <td>{{ $d->aeslest2 }}</td>
                                <td>{{ $d->aesmest3 }}</td>
                                <td>{{ $d->aeshest3 }}</td>
                                <td>{{ $d->aeslest3 }}</td>
                                <td>{{ $d->aesmest4 }}</td>
                                <td>{{ $d->aeshest4 }}</td>
                                <td>{{ $d->aeslest4 }}</td>
                                <td>{{ $d->aesmest5 }}</td>
                                <td>{{ $d->aeshest5 }}</td>
                                <td>{{ $d->aeslest5 }}</td>
                                <td>{{ $d->aesmest6 }}</td>
                                <td>{{ $d->aeshest6 }}</td>
                                <td>{{ $d->aeslest6 }}</td>
                                <td>{{ $d->aesmest7 }}</td>
                                <td>{{ $d->aeshest7 }}</td>
                                <td>{{ $d->aeslest7 }}</td>
                                <td>{{ $d->aesmest8 }}</td>
                                <td>{{ $d->aeshest8 }}</td>
                                <td>{{ $d->aeslest8 }}</td>
                                <td>{{ $d->aesmest9 }}</td>
                                <td>{{ $d->aeshest9 }}</td>
                                <td>{{ $d->aeslest9 }}</td>

                                <td>{{ $d->naesmest1 }}</td>
                                <td>{{ $d->naeshest1 }}</td>
                                <td>{{ $d->naeslest1 }}</td>
                                <td>{{ $d->naesmest2 }}</td>
                                <td>{{ $d->naeshest2 }}</td>
                                <td>{{ $d->naeslest2 }}</td>
                                <td>{{ $d->naesmest3 }}</td>
                                <td>{{ $d->naeshest3 }}</td>
                                <td>{{ $d->naeslest3 }}</td>
                                <td>{{ $d->naesmest4 }}</td>
                                <td>{{ $d->naeshest4 }}</td>
                                <td>{{ $d->naeslest4 }}</td>
                                <td>{{ $d->naesmest5 }}</td>
                                <td>{{ $d->naeshest5 }}</td>
                                <td>{{ $d->naeslest5 }}</td>
                                <td>{{ $d->naesmest6 }}</td>
                                <td>{{ $d->naeshest6 }}</td>
                                <td>{{ $d->naeslest6 }}</td>
                                <td>{{ $d->naesmest7 }}</td>
                                <td>{{ $d->naeshest7 }}</td>
                                <td>{{ $d->naeslest7 }}</td>
                                <td>{{ $d->naesmest8 }}</td>
                                <td>{{ $d->naeshest8 }}</td>
                                <td>{{ $d->naeslest8 }}</td>
                                <td>{{ $d->naesmest9 }}</td>
                                <td>{{ $d->naeshest9 }}</td>
                                <td>{{ $d->naeslest9 }}</td>

                                <td>{{ $d->gv1m }}</td>
                                <td>{{ $d->gv1h }}</td>
                                <td>{{ $d->gv1l }}</td>
                                <td>{{ $d->gv2m }}</td>
                                <td>{{ $d->gv2h }}</td>
                                <td>{{ $d->gv2l }}</td>
                                <td>{{ $d->gv3m }}</td>
                                <td>{{ $d->gv3h }}</td>
                                <td>{{ $d->gv3l }}</td>
                                <td>{{ $d->gv4m }}</td>
                                <td>{{ $d->gv4h }}</td>
                                <td>{{ $d->gv4l }}</td>
                                <td>{{ $d->gv5m }}</td>
                                <td>{{ $d->gv5h }}</td>
                                <td>{{ $d->gv5l }}</td>
                                <td>{{ $d->gv6m }}</td>
                                <td>{{ $d->gv6h }}</td>
                                <td>{{ $d->gv6l }}</td>
                                <td>{{ $d->gv7m }}</td>
                                <td>{{ $d->gv7h }}</td>
                                <td>{{ $d->gv7l }}</td>
                                <td>{{ $d->gv8m }}</td>
                                <td>{{ $d->gv8h }}</td>
                                <td>{{ $d->gv8l }}</td>
                                <td>{{ $d->gv9m }}</td>
                                <td>{{ $d->gv9h }}</td>
                                <td>{{ $d->gv9l }}</td>
                                <td>{{ $d->gv11m }}</td>
                                <td>{{ $d->gv11h }}</td>
                                <td>{{ $d->gv11l }}</td>
                                <td>{{ $d->gv12m }}</td>
                                <td>{{ $d->gv12h }}</td>
                                <td>{{ $d->gv12l }}</td>
                                <td>{{ $d->gv13m }}</td>
                                <td>{{ $d->gv13h }}</td>
                                <td>{{ $d->gv13l }}</td>
                                <td>{{ $d->gv15m }}</td>
                                <td>{{ $d->gv15h }}</td>
                                <td>{{ $d->gv15l }}</td>
                                <td>{{ $d->gv16m }}</td>
                                <td>{{ $d->gv16h }}</td>
                                <td>{{ $d->gv16l }}</td>
                                <td>{{ $d->gv17m }}</td>
                                <td>{{ $d->gv17h }}</td>
                                <td>{{ $d->gv17l }}</td>
                                <td>{{ $d->gv18m }}</td>
                                <td>{{ $d->gv18h }}</td>
                                <td>{{ $d->gv18l }}</td>
                                <td>{{ $d->gv19m }}</td>
                                <td>{{ $d->gv19h }}</td>
                                <td>{{ $d->gv19l }}</td>
                                <td>{{ $d->gv20m }}</td>
                                <td>{{ $d->gv20h }}</td>
                                <td>{{ $d->gv20l }}</td>
                                <td>{{ $d->gv21m }}</td>
                                <td>{{ $d->gv21h }}</td>
                                <td>{{ $d->gv21l }}</td>
                                <td>{{ $d->gv22m }}</td>
                                <td>{{ $d->gv22h }}</td>
                                <td>{{ $d->gv22l }}</td>
                                {{-- FIN RURBO ESTATAL --}}
                            </tr>
                        @endforeach
                    </tbody>

                    
                    @else
                    <tfoot>
                         <tr><td colspan="{{ count($cols)}}">NO SE ENCONTRO NINGUN REGISTRO</td></tr>
                    </tfoot>
                    
                    @endif
                </table>
            </div>
        </div>

    </div>
     @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action',"{{route('formatot.consulta.index')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action',"{{route('formatot.consulta.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});
            });
            $(function() {
                $(".datepicker" ).datepicker({
                    dateFormat:"yy-mm-dd"
                });
            });
        </script>
    @endsection
@endsection
