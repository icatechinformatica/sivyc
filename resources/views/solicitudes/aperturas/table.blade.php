<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="row">
    <div class="table-responsive ">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col" class="text-center" >OPCIONES</th>
                <th scope="col" class="text-center">GRUPO</th>
                @if (($opt== "ARC01" AND $status_solicitud != "VALIDADO") OR ($opt== "ARC02" AND $status_solicitud != "VALIDADO"))
                    <th scope="col" class="text-center">OBSERVACIONES PRELIMINAR</th>
                @elseif ($extemporaneo)
                    <th scope="col" class="text-center" colspan="2">MOTIVO EXTEMPORANEO</th>
                @endif
                @if($motivo_soporte)
                    <th scope="col" class="text-center">MOTIVO REEMPLAZO</th>
                @endif
                <th scope="col" class="text-center">FECHA ARC01</th>
                <th scope="col" class="text-center" >CLAVE</th>
                <!--<th scope="col" class="text-center" >MOTIVO</th> -->
                <th scope="col" class="text-center">CURSO/ CERTIFICACIÓN</th>
                <th scope="col" class="text-center">UNIDAD</th>
                <th scope="col" class="text-center">ESPECIALIDAD</th>
                <th scope="col" class="text-center">CURSO</th>
                <th scope="col" class="text-center">INSTRUCTOR</th>
                <th scope="col" class="text-center">MOD</th>
                <th scope="col" class="text-center">TIPO</th>
                <th scope="col" class="text-center">DURA</th>
                <th scope="col" class="text-center">INICIO</th>
                <th scope="col" class="text-center">TERMINO</th>
                <th scope="col" class="text-center">HORARIO</th>
                <th scope="col" class="text-center">DIAS</th>
                <th scope="col" class="text-center">HOMBRES</th>
                <th scope="col" class="text-center">MUJERES</th>
                <th scope="col" class="text-center">CPAGO</th>
                <th scope="col" class="text-center">MUNICIPIO</th>
                <th scope="col" class="text-center">ZE</th>
                <th scope="col" class="text-center">DEPENDENCIA</th>
                <th scope="col" class="text-center">TIPO</th>
                <th scope="col" class="text-center">SOLICITUD</th>
                <th scope="col" class="text-center">VoBo</th>
                <th scope="col" class="text-center">FORMATO T</th>
                <th scope="col" class="text-center">PLANTEL</th>
                <th scope="col" class="text-center">LUGAR</th>
                <th scope="col" class="text-center">OBSERVACIONES</th>
                <th scope="col" class="text-center">M.VALIDACIÓN</th>
                <th scope="col" class="text-center">FECHA ARC02</th>
                <th scope="col" class="text-center">CONVENIO</th>
                <th scope="col" class="text-center">CONVENIO ESPECIFICO</th>
            </tr>
        </thead>
        @if(count($grupos)>0)
            <tbody>
                @foreach($grupos as $g)
                    @php
                        $rojo = $motivo = null;
                        if(!isset($soporte) AND $g->status_folio=="SOPORTE") $soporte = true;

                        switch($opt){
                            case "ARC01":
                                if(($g->status<>'NO REPORTADO' OR $g->turnado<>'UNIDAD') AND $g->status_curso =='AUTORIZADO') $activar=false;
                                $mextemporaneo = $g->mextemporaneo;
                                $rextemporaneo = $g->rextemporaneo;
                            break;
                            case "ARC02":
                                if(($g->status<>'NO REPORTADO' AND $g->status<>'RETORNO_UNIDAD') OR $g->turnado<>'UNIDAD' OR $status_solicitud<>'VALIDADO') $activar=false;
                                $mextemporaneo = $g->mextemporaneo_arc02;
                                $rextemporaneo = $g->rextemporaneo_arc02;
                            break;
                        }
                        $mov = json_decode($g->movimientos, true); 
                        if (!empty($mov[0]['VoBo'][0]['motivo'])) $motivo =  $mov[0]['VoBo'][0]['motivo'];
                        
                    @endphp

                    <tr @if($rojo)class='text-danger' @endif >
                        <td class='text-center'>
                            @if($g->file_pdf)
                                <a class="nav-link" href="{{ env('APP_URL').'/storage/'.$g->file_pdf }}" target="_blank">
                                    <i class="fa fa-dollar-sign  fa-2x fa-lg text-primary" title="RECIBO DE PAGO PDF"></i>
                                </a>
                            @elseif($g->comprobante_pago)
                                <a class="nav-link" href="{{ $path.$g->comprobante_pago }}" target="_blank">
                                    <i class="fa fa-dollar-sign  fa-2x fa-lg text-primary" title="RECIBO DE PAGO PDF"></i>
                                </a>
                            @else
                                <i class="fa fa-dollar-sign  fa-2x fa-lg text-mute" title="RECIBO NO DISPONIBLE"></i>
                            @endif

                            @if ($g->soporte_exo)
                                <a  class="nav-link"   title="PDF EXONERACION"
                                    @if ($g->rev_exo) href="{{$path.$g->soporte_exo}}" @else href="{{$g->soporte_exo}}" @endif >
                                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center">{{$g->folio_grupo}}</td>
                        @if (($opt== "ARC01" AND $status_solicitud != "VALIDADO") OR ($opt== "ARC02" AND $status_solicitud != "VALIDADO"))
                            <td>
                                <div style="width: 400px;">{{ Form::textarea('prespuesta['.$g->id.']', $g->obspreliminar ?? $motivo, ['id' => 'prespuesta['.$g->id.']' ,'class' => 'form-control', 'placeholder' => 'OBSERVACIONES','rows' =>'3']) }}</div>
                            </td>
                        @elseif($extemporaneo)
                            <td class="text-center">{{$mextemporaneo }}</td>
                            <td class="text-center">{{$rextemporaneo}}</td>
                        @endif
                        @if($motivo_soporte)
                            <td class="text-center text-danger">{{$g->motivo ?? $motivo_soporte}}</td>
                        @endif
                        <td class="text-center"> {{$g->fecha_arc01}}</td>
                        <td class="text-center">
                            @if($g->clave=='0')
                                <div class="form-check"><input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked></div>
                            @else
                                <div style="width:128px;">{{ $g->clave}}</div>
                            @endif
                       </td>

                       <!--
                        <td class="text-center"><div style="width:400px;">
                            <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                        </div> </td>
                        -->
                        <td class="text-center"> {{ $g->tipo_curso }} </td>
                        <td> <div style="width:100px;">{{ $g->unidad }} </div></td>
                        <td> <div style="width:148px;">{{ $g->espe }} </div></td>
                        <td><div style="width:220px;"> {{ $g->curso }}</div></td>
                        <td><div style="width:150px;">
                            @if($g->vb_dg==true or $g->clave!='0')
                                {{ $g->nombre }}. {{ $g->instructor_mespecialidad}}
                            @endif
                            </div>
                        <a class="mt-2 text-center" onclick="seleccion_instructor('{{ $g->folio_grupo }}')" title="Seleccionar Instructor"><i class="fa fa-address-book mr-2" aria-hidden="true" style="color:rgb(1, 95, 84);"></i> Ver Instructores</a>
                        </td>
                        <td class="text-center"> {{ $g->mod }} </td>
                        <td class="text-center"> @if ($g->tipo=='EXO') {{"EXONERACION"}} @elseif($g->tipo=='EPAR') {{"REDUCCION DE CUOTA"}}  @else {{"PAGO ORDINARIO"}}   @endif </td>
                        <td class="text-center"> {{ $g->dura }} </td>
                        <td class="text-center"><div style="width:65px;"> {{ $g->inicio }}</div> </td>
                        <td class="text-center"><div style="width:65px;"> {{ $g->termino }}</div> </td>
                        <td class="text-center"><div style="width:70px;"> {{ $g->hini }} A {{ $g->hfin }}</div> </td>
                        <td class="text-center"> {{ $g->dia }} </td>
                        <td class="text-center"> {{ $g->hombre }} </td>
                        <td class="text-center"> {{ $g->mujer }} </td>
                        <td class="text-center"> {{ $g->cp }} </td>
                        <td> {{ $g->muni }} </td>
                        <td class="text-center"> {{ $g->ze}} </td>
                        <td><div style="width:150px;">{{ $g->depen }}</div></td>
                        <td class="text-center"> {{ $g->tcapacitacion }} </td>
                        <td class="text-center">
                            @if($g->status_curso) {{ $g->status_curso }} @else {{"EN CAPTURA" }} @endif
                            @if( $g->turnado=='VoBo' OR  $g->turnado=='DGA' ){{ $g->turnado }} @endif
                        </td>
                        <td class="text-center">
                            @php
                                $movs = json_decode($g->movimientos);
                            @endphp
                            @if($g->vb_dg)
                                {{ 'AUTORIZADO'}}
                            @elseif($g->vb_dg == false and $g->turnado=='DGA')
                                <span class="text-danger">{{ 'RECHAZADO'}} <br/>({{ $motivo}})</span>
                            @endif

                        </td>
                        <td class="text-center"> {{ $g->status}}</td>
                        <td class="text-center">{{$g->plantel }}</td>
                        <td> <div style="width:300px;"> {{ $g->efisico }} </div></td>
                        <td class="text-left">
                            <div style="width:500px;">
                                @if($g->option =='ARC01')  {{ $g->nota }}
                                @elseif($g->option =='ARC02') {{ $g->observaciones }} @endif
                            </div>
                        </td>
                        <td class="text-center"> {{ $g->mvalida}}</td>
                        <td class="text-center">{{$g->fecha_arc02}}</td>
                        <td class="text-center">{{$g->cgeneral}} {{$g->fecha_vigencia}}</td>
                        <td class="text-center"><div style="width:120px;">{{$g->cespecifico }} {{$g->fcespe}}</div></td>
                    </tr>
                 @endforeach
            </tbody>
        @else
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
    </div>
</div>
<div class="row justify-content-end">
    @if($activar==true OR isset($soporte) OR $status_curso == 'SOPORTE' OR $status_solicitud == 'TURNADO')
        @if($status_curso == "EN FIRMA")
            <div class="form-group col-md-3">

                    {{ Form::button('GENERAR AUTORIZACIÓN PDF', ['id'=>'generar','class' => 'btn  mx-4']) }}
            </div>
        @endif
        @if($movimientos)
            <div class="form-group col-md2  my-3">
                <label>MOVIMIENTO:</label>
            </div>
            <div class="form-group col-md-4 my-2">
                {{ Form::select('movimiento', $movimientos, $opt, ['id'=>'movimiento','class' => 'form-control' ] ) }}
            </div>
            <div class="form-group col-md-4 my-2" id='observaciones' style="display:none">
                {{ Form::text('observaciones', null, [ 'class' => 'form-control', 'placeholder' => 'OBSERVACIONES',  'required' => 'required', 'size' => 45]) }}
            </div>
            <div class="form-group col-md-2 my-2" id='mrespuesta' style="display:none">
                {{ Form::text('mrespuesta', null, [ 'class' => 'form-control', 'placeholder' => 'NÚMERO DEMEMORÁNDUM',  'required' => 'required', 'size' => 35]) }}
            </div>
            <div class="form-group col-md-2" id="fecha" style="display:none">
                {{ form::date('fecha', date('Y-m-d'), ['class'=>'form-control mx-3']) }}
            </div>
            <div class="custom-file form-group col-md-3 text-center my-2" id="file" style="display:none">
                <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                <label for="file_autorizacion" class="custom-file-label">AUTORIZACIÓN FIRMADA PDF</label>
            </div>
            <div class="form-group col-md-1 ">
                {{ Form::button(' ACEPTAR ', ['id'=>'aceptar','class' => 'btn  bg-danger']) }}
            </div>
        @endif
    @endif
</div>
