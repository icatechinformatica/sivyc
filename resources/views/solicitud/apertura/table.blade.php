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
@if (isset($grupo))
    @if($grupo->depen == 'CAPACITACION ABIERTA')   
        <h5><b>DEL OFICIO DE ENTREGA DE CONSTANCIAS</b></h5>         
        <div class="row col-md-12 col-lg-12  bg-light p-4 mb-5 ">
            {{-- Formulario pdf generar soporte by Jose Luis Moreno Arcos  --}}                        
                        <input type="hidden" name="idorg" value="{{isset($grupo->id_organismo) ? $grupo->id_organismo : ''}}">
                        <input type="hidden" name="unidad_sop" value="{{isset($grupo->unidad) ? $grupo->unidad : ''}}">
                        <input type="hidden" name="cgeneral_sop" value="{{isset($grupo->cgeneral) ? $grupo->cgeneral : ''}}">
                        <div class="col-3">                            
                            <input type="text" class="form-control" style="" id="num_oficio" name="num_oficio" placeholder="NUMERO DE OFICIO" title="NUMERO DE OFICIO" value="{{$num_oficio_sop != NULL ? $num_oficio_sop : ''}}">
                        </div>
                        <div class="col-3 px-0">                            
                            <input type="text" class="form-control" style="" id="datos_titular" name="datos_titular" placeholder="TITULAR DE LA DEPENDENCIA, CARGO" title="TITULAR DE LA DEPENDENCIA, CARGO" value="{{$titular_sop != NULL ? $titular_sop : ''}}">
                        </div>
                    
            
            <div class="col-3">
                <button type="button" class="btn" id="genpdf_soporte">GUARDAR Y GENERAR PDF</button>
            </div>
        </div>
    @endif
@endif


 <div class="col-md-12 col-lg-12 text-right mt-20">
    @if($grupo->clave=='0' AND !$grupo->status_curso AND (!$grupo->status_solicitud OR $grupo->status_solicitud=='RETORNO'))
        <button type="button" class="btn bg-danger" id="regresar" ><< REGRESAR A VINCULACI&Oacute;N</button>
         {{--<button id="btnShowCalendarFlex" type="button" class="btn btn-amber">Agendar Horario Flexible</button>--}}
        <button type="submit" class="btn" id="guardar" >GUARDAR SOLICITUD</button> &nbsp;&nbsp;
        @if ($instructor)
             <button id="btnShowCalendar" type="button" class="btn btn-info">Agendar</button>
        @endif
    @elseif($grupo->clave!='0' AND $grupo->status_curso=="AUTORIZADO" AND $grupo->status=="NO REPORTADO" AND $mov == "INSERT")
        <button type="button" class="btn bg-warning" id="inscribir" >ACEPTAR APERTURA </button>
    @endif       
</div>
