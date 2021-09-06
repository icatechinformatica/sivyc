<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="table-responsive ">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th> 
                <th scope="col" class="text-center" >MOTIVO</th>
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
                <th scope="col" class="text-center">ESTATUS</th>          
                <th scope="col" class="text-center">LUGAR</th>
                <th scope="col" class="text-center">OBSERVACIONES</th>
                @if($opt=="ARC02")
                    <th scope="col" class="text-center" >EDIT</th>                    
                @else
                <th scope="col" class="text-center" >VER</th>
                @endif
            </tr>
        </thead>
        @if(count($grupos)>0) 
            <tbody>                    
                <?php 
                    $consec=1; 
                    $activar = true; 
                    $munidad = $grupos[0]->munidad; 
                    $nmunidad = $grupos[0]->nmunidad; 
                    $status_curso = $grupos[0]->status_curso; 
                    $mvalida = $grupos[0]->mvalida;             
                ?>
                @foreach($grupos as $g)
                    <?php 
                    $rojo=null;
                    /*
                    if( $g->turnado_solicitud != 'DTA' ) $activar = false;                      
                    if( $g->turnado_solicitud == 'VINCULACION'  )$rojo = true; 
                    else $rojo = false;
                    */
                    switch($opt){
                        case "ARC01":
                            if($g->status_curso<>'SOLICITADO' OR $g->status<>'NO REPORTADO' OR $g->turnado<>'UNIDAD') $activar=false;
                        break;
                        case "ARC02":
                            if($g->status_curso<>'SOLICITADO' OR ($g->status<>'NO REPORTADO' AND $g->status<>'RETORNO_UNIDAD' ) OR $g->turnado<>'UNIDAD' ) $activar=false;
                        break;
                    }
                    ?>
                    <tr @if($rojo)class='text-danger' @endif >
                        <td class="text-center"> {{ $consec++ }}</td>
                        <td class="text-center"><div style="width:450px;">
                            <!--<textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>-->
                        </div> </td>
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
                        <td class="text-center"> 
                            @if($opt=="ARC01")
                            {{ $g->turnado_solicitud }} 
                            @else
                            {{ $g->turnado }} 
                            @endif
                        
                        </td>
                        <td class="text-center"> @if($g->status_curso) {{ $g->status_curso }} @else {{"EN CAPTURA" }} @endif</td>
                        <td> {{ $g->efisico }} </td>
                        <td class="text-left">
                            <div style="width:900px;">
                                @if($g->option =='ARC01')  {{ $g->nota }}
                                @elseif($g->option =='ARC02') {{ $g->observaciones }} @endif
                            </div>    
                        </td>
                        @if($opt == "ARC02")
                            <td class='text-center'>
                                <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="show('{{$g->id}}')"></i></a>
                            </td>
                        @else
                            <td class='text-center'>
                                <a class="nav-link" ><i class="fa fa-search  fa-2x fa-lg text-success" title="Ver detalle" onclick="show('{{$g->id}}')"></i></a>
                            </td>
                        @endif
                    </tr>
                 @endforeach                       
            </tbody>                   
        @else 
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
</div>

<div class="form-inline  col-md-12 mt-4 justify-content-end align-items-end"> 
    @if($activar==true)
        MOVIMIENTO: {{ Form::select('movimiento', $movimientos, $opt, ['id'=>'movimiento','class' => 'form-control col-md-2  mx-4' ] ) }}
        {{ Form::text('mrespuesta', null, ['id'=>'mrespuesta', 'class' => 'form-control', 'placeholder' => 'MEMORÁNDUM RESPUESTA',  'required' => 'required', 'size' => 25]) }}
        {{ form::date('fecha', date('Y-m-d'), [ 'id'=>'fecha', 'class'=>'form-control mx-4']) }}    
        <div class="custom-file col-md-3 mx-4 text-center" id="file">
            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
            <label for="file_autorizacion" class="custom-file-label">PDF SOLICITUD FIRMADA</label>
        </div>    
        @if($status_curso == "EN FIRMA")
            {{ Form::button('GENERAR MEMORÁNDUM PDF', ['id'=>'generar','class' => 'btn  mx-4']) }}
        @endif
        {{ Form::button(' ACEPTAR ', ['id'=>'aceptar','class' => 'btn  bg-danger mx-4']) }}  
    @endif
    @if($mvalida)
        <a href="{{$mvalida}}" target="_blank" class="btn bg-danger">MEMORÁNDUM DE AUTORIZACIÓN (PDF)</a>   
    @endif
</div>
