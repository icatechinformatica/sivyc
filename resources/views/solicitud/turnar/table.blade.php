<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="table-responsive ">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>                
                <th scope="col" class="text-center" >CLAVE</th>
                <th scope="col" class="text-center">ESQUEMA</th>
                <th scope="col" class="text-center">ESPECIALIDAD</th>
                <th scope="col" class="text-center">NOMBRE</th>
                <th scope="col" class="text-center">MOD</th>
                <th scope="col" class="text-center">TIPO</th>
                <th scope="col" class="text-center">DURA</th>
                <th scope="col" class="text-center">INICIO</th>
                <th scope="col" class="text-center">TERMINO</th>
                <th scope="col" class="text-center">HORARIO</th> 
                <th scope="col" class="text-center">DIAS</th> 
                <th scope="col" class="text-center">HOMBRES</th> 
                <th scope="col" class="text-center">MUJERES</th> 
                <th scope="col" class="text-center">INSTRUCTOR</th> 
                <th scope="col" class="text-center">CPAGO</th> 
                <th scope="col" class="text-center">MUNICIPIO</th> 
                <th scope="col" class="text-center">ZE</th> 
                <th scope="col" class="text-center">DEPENDENCIA</th> 
                <th scope="col" class="text-center">TIPO COUTA</th> 
                <th scope="col" class="text-center">ESPACIO</th>
                <th scope="col" class="text-center">TURNADO</th>
                <th scope="col" class="text-center">ESTATUS</th>
                <th scope="col" class="text-center">SOLICITUD</th>          
                <th scope="col" class="text-center">OBSERVACIONES</th>
            </tr>
        </thead>
        @if(count($grupos)>0) 
            <tbody>                    
                <?php 
                    $consec=1; 
                    $activar = false; 
                    $munidad = $grupos[0]->munidad; 
                    $nmunidad = $grupos[0]->nmunidad; 
                ?>
                @foreach($grupos as $g)
                    <?php 
                    if(!$g->file_arc01 AND trim($g->arc)=="01" AND $g->option == "ARC01" ) $activar = true;
                    elseif(!$g->file_arc02 AND trim($g->arc)=="02" AND  $g->option == "ARC02") $activar = true;
                    ?>
                    <tr>
                        <td class="text-center"> {{ $consec++ }}</td>
                        <td><div style="width:128px;"> {{ $g->clave}} </div> </td>              
                        <td class="text-center"> {{ $g->tipo_curso }} </td>
                        <td> {{ $g->espe }} </td>
                        <td> {{ $g->curso }} </td>
                        <td class="text-center"> {{ $g->mod }} </td>
                        <td class="text-center"> {{ $g->tipo }} </td>
                        <td class="text-center"> {{ $g->dura }} </td>
                        <td class="text-center"><div style="width:65px;"> {{ $g->inicio }}</div> </td>
                        <td class="text-center"><div style="width:65px;"> {{ $g->termino }}</div> </td>
                        <td class="text-center"><div style="width:70px;"> {{ $g->hini }} A {{ $g->hfin }}</div> </td>
                        <td class="text-center"> {{ $g->dia }} </td>
                        <td class="text-center"> {{ $g->hombre }} </td>
                        <td class="text-center"> {{ $g->mujer }} </td>
                        <td><div style="width:120px;">{{ $g->nombre }}</div></td>
                        <td class="text-center"> {{ $g->cp }} </td>
                        <td> {{ $g->muni }} </td>
                        <td class="text-center"> {{ $g->ze}} </td>
                        <td><div style="width:150px;">{{ $g->depen }}</div></td>
                        <td class="text-center"> {{ $g->tcapacitacion }} </td>
                        <td> {{ $g->efisico }} </td>
                        <td class="text-center"> {{ $g->turnado }} </td>
                        <td class="text-center"> {{ $g->status }} </td>
                        <td class="text-center"> {{ $g->status_curso }} </td>
                        <td class="text-left">
                            <div style="width:900px;">
                                @if($g->option =='ARC01')  {{ $g->nota }}
                                @elseif($g->option =='ARC02') {{ $g->observaciones }} @endif
                            </div>    
                        </td>
                    </tr>
                 @endforeach                       
            </tbody>                   
        @else 
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
</div>

<div class="col-md-12 text-right  mt-4">   
    @if($activar)
        {{ Form::button('GENERAR MEMORÁNDUM PDF', ['id'=>'generar','class' => 'btn  mx-4']) }}
        <div class="custom-file col-md-3 mx-4 text-center">
            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
            <label for="file_autorizacion" class="custom-file-label">PDF SOLICITUD FIRMADA</label>
        </div>    
        {{ Form::button('ENVIAR A LA DTA >>', ['id'=>'enviar','class' => 'btn  bg-danger mx-4']) }}        
    @else
        <a href="{{$file}}" target="_blank" class="btn  bg-warning">IMPRIMIR
            @if($g->option =='ARC01')  {{$munidad}}
            @elseif($g->option =='ARC02') {{$nmunidad}} @endif

            .PDF
        </a>        
    @endif    
</div>
