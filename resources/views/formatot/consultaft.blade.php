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
                                        <td>{{ $d->espe }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->curso }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->clave }}</div>
                                        </td>
                                        <td>{{ $d->mod }}</td>
                                        {{-- <td>
                                            <div style="width: 150px">{{ $d->bloques_folios }}</div>
                                        </td> --}}
                                        {{-- <td>{{ $d->folios_cancelados }}</td> --}}
                                        <td>{{ $d->dura }}</td>
                                        <td>{{ $d->turno }}</td>
                                        <td>{{ $d->diai }}</td>
                                        <td>{{ $d->mesi }}</td>
                                        <td>{{ $d->diat }}</td>
                                        <td>{{ $d->mest }}</td>
                                        <td>{{ $d->pfin }}</td>
                                        <td>{{ $d->horas }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->dia }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->horario }}</div>
                                        </td>
                                        <td>{{ $d->tinscritos }}</td>
                                        <td>{{ $d->imujer }}</td>
                                        <td>{{ $d->ihombre }}</td>
                                        <td>{{ $d->egresado }}</td>
                                        <td>{{ $d->emujer }}</td>
                                        <td>{{ $d->ehombre }}</td>
                                        <td>{{ $d->desertado }}</td>
                                        <td>{{ $d->costo }}</td>
                                        <td>{{ $d->ctotal }}</td>
                                        <td>{{ $d->etmujer }}</td>
                                        <td>{{ $d->ethombre }}</td>
                                        <td>{{ $d->epmujer }}</td>
                                        <td>{{ $d->ephombre }}</td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->cespecifico }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->mvalida }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->efisico }}</div>
                                        </td>
                                        <td>
                                            <div style="width:200px; word-wrap: break-word">{{ $d->nombre }}</div>
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
                                        <td>{{ $d->adolescente_calle }}</td>
                                        <td>{{ $d->jefa_familia }}</td>
                                        <td>{{ $d->indigena }}</td>
                                        <td>{{ $d->cerss_nombre }}</td>
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
