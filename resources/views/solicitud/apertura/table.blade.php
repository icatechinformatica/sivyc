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
                        <td class="text-center">{{ $a->costo }}</td>
                    </tr>
                 @endforeach                       
            </tbody>                   
        @else 
            {{ 'NO REGISTRO DE ALUMNOS'}}
        @endif
    </table>
</div>

 <div class="col-md-12 text-right">    
    @if ($comprobante)
    <a href="{{$comprobante}}" target="_blank" class="btn  bg-warning">IMPRIMIR COMPROBANTE DE PAGO</a>
    @endif            
    @if($grupo->clave=='0' AND !$grupo->status_curso AND (!$grupo->status_solicitud OR $grupo->status_solicitud=='RETORNO'))
        <button type="button" class="btn bg-warning " id="regresar" ><< REGRESAR A VINCULACI&Oacute;N</button> 
        {{--<button id="btnShowCalendarFlex" type="button" class="btn btn-amber">Agendar Horario Flexible</button>--}}
        <button type="submit" class="btn" id="guardar" >GUARDAR SOLICITUD</button> &nbsp;&nbsp; 
        @if ($instructor)
        <button id="btnShowCalendar" type="button" class="btn btn-info">Agendar</button>
        @endif      
    @elseif($grupo->clave!='0' AND $grupo->status_curso=="AUTORIZADO" AND $grupo->status=="NO REPORTADO" AND $mov == "INSERT") 
            <button type="button" class="btn bg-warning" id="inscribir" >ACEPTAR APERTURA </button>    
    @endif
</div>  