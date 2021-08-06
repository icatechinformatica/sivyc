<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="table-responsive ">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">CURP</th>                            
                <th scope="col" class="text-center">ALUMNO</th>
                <th scope="col" class="text-center">MATRICULA</th>
                <th scope="col" class="text-center">FEC. NAC.</th>
                <th scope="col" class="text-center">SEXO</th>
                <th scope="col" class="text-center" width="20%">ESCOLARIDAD</th>
                <th scope="col" class="text-center">TIPO DE INSCRIPCI&Oacute;N</th>
                <th scope="col" class="text-center">COUTA</th> 
            </tr>
        </thead>
        @if(isset($alumnos)) 
            <tbody>                    
                <?php $consec=1; ?>
                @foreach($alumnos as $a)
                    <?php 
                    $mov = $a->mov;                     
                    ?>
                    <tr>
                        <td class="text-center"> {{ $consec++ }} </td>
                        <td class="text-center"> {{ $a->curp}} </td>
                        <td> {{ $a->alumno }} </td>
                        <td class="text-center"> {{ $a->matricula }} </td>
                        <td class="text-center">{{ $a->fecha_nacimiento }}</td>
                        <td class="text-center">{{ $a->sexo }}</td>
                        <td >{{ $a->escolaridad }}</td>
                        <td class="text-center">{{ $a->tinscripcion }}</td>
                        @if($grupo->clave=='0' AND !$grupo->status_curso)
                            <td class="text-center">{{ Form::text('costo['.$a->id_reg.']', $a->costo , ['id'=>'costo['.$a->id_reg.']', 'class' => 'form-control numero', 'required' => 'required', 'size' => 1]) }}</td>
                        @else
                            <td class="text-center">{{ $a->costo }}</td>
                        @endif
                    </tr>
                 @endforeach                       
            </tbody>                   
        @else 
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
</div>

 <div class="col-md-12 text-right">                
    @if((($grupo->status=='NO REPORTADO' OR $grupo->status=='RETORNO_UNIDAD') AND $grupo->turnado=='UNIDAD' AND $grupo->status_curso=='AUTORIZADO') )
        <button class="btn" id="guardar" >GUARDAR SOLICITUD</button> &nbsp;&nbsp;       
    @endif
    @if($grupo->status=='NO REPORTADO' AND !$grupo->status_curso AND trim($grupo->arc)=='02')
        <button class="btn bg-danger" id="deshacer" >DESHACER SOLICITUD</button> &nbsp;&nbsp;  
    @endif      
</div>  