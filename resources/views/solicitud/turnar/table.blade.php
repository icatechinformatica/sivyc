<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="table-responsive ">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col" class="text-center">ID</th>  
                @if ($status_solicitud=='RETORNO')
                <th scope="col" class="text-center" colspan="2">OBSERVACION PRELIMINAR</th>
                @endif   
                @if (($status_solicitud=='VALIDADO')&&($extemporaneo))
                <th scope="col" class="text-center" colspan="2">MOTIVO EXTEMPORANEO</th>
                @endif      
                <th scope="col" class="text-center" >No. GRUPO</th>
                <th scope="col" class="text-center" >CLAVE</th>
                <th scope="col" class="text-center">SERVICIO</th>
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
                <th scope="col" class="text-center">TURNADO</th>
                <th scope="col" class="text-center">SOLICITUD</th>
                <th scope="col" class="text-center">ESTATUS</th>
                <th scope="col" class="text-center">LUGAR</th>
                <th scope="col" class="text-center">OBSERVACIONES</th>
                <th scope="col" class="text-center">AVISO</th>
            </tr>
        </thead>
        @if(count($grupos)>0) 
            <tbody>                    
                <?php 
                    $consec=1; 
                    $activar = true; 
                    $munidad = $grupos[0]->munidad; 
                    $nmunidad = $grupos[0]->nmunidad; 
                    $pdf_curso = $grupos[0]->pdf_curso;
                    $rojo = null;             
                ?>
                @foreach($grupos as $g)
                    <?php
                    $aviso = NULL;                    
                    if( ($g->option =='ARC01' AND ($g->turnado_solicitud != 'UNIDAD' OR  $g->clave!='0')) 
                        OR ($g->option =='ARC02'
                        AND ($g->status_curso!='AUTORIZADO' OR $g->turnado!='UNIDAD' OR $g->status == 'TURNADO_DTA' OR $g->status == 'TURNADO_PLANEACION' OR $g->status == 'REPORTADO'))){
                        $activar = false;                        
                        $aviso = "Grupo turnado a ".$g->turnado_solicitud.", Clave de Apertura ".$g->status_curso." y Estatus: ".$g->status;
                    }else if( ($g->status_solicitud_arc02 == 'TURNADO' AND $g->option =='ARC02') OR ($g->status_solicitud == 'TURNADO' AND $g->option =='ARC01') ){
                        $activar = false;                        
                        $aviso = "Grupo turnado a revisión";
                    }else if( $g->turnado_solicitud == 'VINCULACION'  ){
                        $activar = false;
                        $rojo = true; 
                        $aviso = "GRUPO TURNADO A VINCULACIOÓN"; 
                    }elseif($g->tipo!='PINS' AND ($g->mexoneracion=='NINGUNO' OR $g->mexoneracion == null)) { 
                        $activar = false;
                        $rojo = true;                         
                        $aviso = "INGRESE EL MEMORÁNDUM DE EXONERACÓN"; 
                    }elseif( $g->option =='ARC01' AND (($g->horas_agenda < $g->dura) or ($g->horas_agenda > $g->dura)) ){
                        $activar = false;
                        $rojo = true;                         
                        $aviso = "HORAS AGENDADAS NO CORRESPONDIENTES A LA DURACIÓN DEL CURSO";
                    }else if( $g->option =='ARC01' AND ( $g->soltermino < date('Y-m-d') ) ){
                        $activar = false;
                        $rojo = true;                         
                        $aviso = "EL CURSO HA SOBREPASADO EL LIMITE DE TIEMPO PARA REALIZAR SU SOLICITUD ARC 01";
                    }else $rojo = false;         
                    
                    if ($g->option =='ARC01'){
                        $id_mextemporaneo = $g->mextemporaneo;
                        $rextemporaneo = $g->rextemporaneo;
                    }else if($g->option =='ARC02'){
                        $id_mextemporaneo = $g->mextemporaneo_arc02;
                        $rextemporaneo = $g->rextemporaneo_arc02;
                    }
                    ?>
                    <tr @if($rojo)class='text-danger' @endif >
                        <td class="text-center"> {{ $g->id }}</td>
                        @if ($status_solicitud=='RETORNO')
                        <td class="text-center"><a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar"></i></a></td>
                        <td class='text-center'><div style="width: 400px;">{{$g->obspreliminar}}</div></td>
                        @endif
                        @if (($status_solicitud=='VALIDADO')&&($extemporaneo))
                        <td class='text-center'> <div style="width:305px;">{!! Form::select('motivo['.$g->id.']',$mextemporaneo,$id_mextemporaneo,['id' =>'motivo['.$g->id.']', 'class' => 'form-control','placeholder' =>'-- SELECCIONAR --']) !!}</div></td>
                        <td class='text-center'><div style="width:400px;">{!! Form::textarea('mrespuesta['.$g->id.']',  $rextemporaneo,['id' =>'mrespuesta['.$g->id.']', 'class' => 'form-control','rows' =>'3']) !!}</div></td>      
                        @endif
                        <td class="text-center"><div style="width:128px;"> {{ $g->folio_grupo}} </div> </td>
                        <td><div style="width:128px;"> {{ $g->clave}} </div> </td>              
                        <td class="text-center"> {{ $g->tipo_curso }} </td>
                        <td> {{ $g->espe }} </td>
                        <td> {{ $g->curso }} </td>
                        <td><div style="width:120px;">{{ $g->nombre }}</div></td>
                        <td class="text-center"> {{ $g->mod }} </td>
                        <td class="text-center"> {{ $g->tipo }} </td>
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
                        <td class="text-center"> {{ $g->turnado_solicitud }} </td>
                        <td class="text-center"> @if($g->status_curso) {{ $g->status_curso }} @else {{"EN CAPTURA" }} @endif </td>
                        <td class="text-center"> {{ $g->status }} </td>
                        <td> {{ $g->efisico }} </td>
                        <td class="text-left">
                            <div style="width:900px;">
                                @if($g->option =='ARC01')  {{ $g->nota }}
                                @elseif($g->option =='ARC02') {{ $g->observaciones }} @endif
                            </div>    
                        </td>
                        <td> <div style="width:200px;"> {{ $aviso }} </div></td>
                    </tr>
                 @endforeach                       
            </tbody>                   
        @else 
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
</div>

<div class="form-row col-md-12 mt-4">
    @if ($activar)
        <div class=" form-group col-md-2">
            
        </div>
        @if ((($opt=='ARC01')&&($grupos[0]->status_solicitud!='VALIDADO')) OR ($opt=='ARC02' && ($grupos[0]->status_solicitud_arc02 !='VALIDADO')) )
            <div class="form-group col-md-4"></div>
            <div class="custom-file mt-1 form-group col-md-3">
                <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                <label for="file_autorizacion" class="custom-file-label">ANEXOS PDF</label>
            </div>
            <div class="form-group col-md-3">
                @if ($opt=='ARC01')
                {{ Form::button('ENVIAR PRELIMINAR ARC 01 >>', ['id'=>'preliminar','class' => 'btn  bg-danger mx-4']) }} 
                @else
                {{ Form::button('ENVIAR PRELIMINAR ARC 02 >>', ['id'=>'preliminar','class' => 'btn  bg-danger mx-4']) }}
                @endif
            </div>
        @else
            <div class="form-group col-md-3">
                
            </div>
            <div class="form-group col-md-2">
                {{ Form::button('GENERAR MEMORÁNDUM PDF', ['id'=>'generar','class' => 'btn mt-5']) }}
            </div>
            <div class="form-group col-md-3">
                <div class="custom-file mt-5">
                    <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                    <label for="file_autorizacion" class="custom-file-label">PDF SOLICITUD FIRMADA</label>
                </div>
            </div>
            <div class="form-group col-md-2">
                {{ Form::button('ENVIAR A LA DTA >>', ['id'=>'enviar','class' => 'btn  bg-danger mt-5']) }}
            </div>
        @endif
    @elseif($file)
        <a href="{{$file}}" target="_blank" class="btn  bg-warning">IMPRIMIR
            @if($g->option =='ARC01')  {{$munidad}}
            @elseif($g->option =='ARC02') {{$nmunidad}} @endif
            .PDF
        </a> 
    @endif
    @if($pdf_curso)  
        <a href="{{$pdf_curso}}" target="_blank" class="btn bg-warning">MEMORÁNDUM DE AUTORIZACIÓN (PDF)</a>
    @endif
</div>
