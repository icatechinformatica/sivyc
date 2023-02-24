<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Apertura | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card-header">
        Solicitud / Modificación de Apertura        
    </div>
    <div class="card card-body" style=" min-height:450px;">       
        <?php
            $horario = $modalidad = $clave = $munidad = $mov = $disabled = NULL;
            $activar = true;
            if(isset($grupo)){
                $clave = $grupo->clave;
                $modalidad = $grupo->mod;
                if($grupo->hini){
                   $grupo->hini = date("H:i",strtotime($grupo->hini));
                   $grupo->hfin= date("H:i",strtotime($grupo->hfin));
                   $horario = $grupo->hini." A ".$grupo->hfin;
                }else $horario = $grupo->horario;                
                if(isset($grupo->munidad)) $munidad = $grupo->munidad;
                if($grupo->tcapacitacion=='PRESENCIAL'){
                    $disabled = 'disabled';
                    $grupo->medio_virtual='';
                    $grupo->link_virtual='';
                }  
            } 
            if(isset($alumnos[0]->mov))$mov = $alumnos[0]->mov;            
        ?>
    {{ Form::open(['route' => 'solicitud.apertura.modificar', 'method' => 'post', 'id'=>'frm']) }}
         <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CLAVE DE APERTURA', 'aria-label' => 'CLAVE DE APERTURA', 'required' => 'required', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div>
                
        </div>
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        
        @if(isset($grupo))           
            <h5><b>DEL CURSO</b></h5> 
            @if($grupo->clave)
                <div class="row bg-light" style="padding:15px;">
                    <div class="form-group col-md-3">CLAVE DE APERTURA: <b>{{ $grupo->clave}}</b></div>
                    <div class="form-group col-md-4">ESTATUS DEL CURSO: <b>{{ $grupo->status }}</b></div>
                    <div class="form-group col-md-1">ARC: <b>{{ $grupo->arc }}</b></div>
                    <div class="form-group col-md-3">ESTATUS: <b>{{ $grupo->status_curso}}</b></div>
                </div>   
            @endif  
            <div class="row bg-light" style="padding:15px">
                <div class="form-group col-md-3">UNIDAD/ACCI&Oacute;N M&Oacute;VIL: <b>{{ $grupo->unidad }}</b></div>
                <div class="form-group col-md-5">CURSO: <b>@if($grupo->clave){{ $grupo->id }}@endif {{ $grupo->curso }}</b></div>
                <div class="form-group col-md-4">ESPECIALIDAD: <b>{{ $grupo->clave_especialidad }} &nbsp{{ $grupo->espe }}</b></div>
                <div class="form-group col-md-3">&Aacute;REA: <b>{{ $grupo->area }}</b></div>
                <div class="form-group col-md-2">MODALIDAD: <b>{{ $grupo->mod}}</b></div>
                <div class="form-group col-md-3">TIPO CAPACITACI&Oacute;N: <b>{{ $grupo->tcapacitacion}}</b></div>            
                <div class="form-group col-md-4">DURACI&Oacute;N: <b>{{ $grupo->dura }} hrs.</b></div>    
                <div class="form-group col-md-3">HORARIO: <b>{{ $horario }}</b></div>
                <div class="form-group col-md-2">COSTO ALUMNO: <b>{{ $grupo->costo }}</b></div>
                <div class="form-group col-md-3">HOMBRES: <b>{{ $grupo->hombre }}</b></div>
                <div class="form-group col-md-2">MUJERES: <b>{{ $grupo->mujer }}</b></div>                  
            </div>            
            
            <h5><b>DE LA APERTURA</b></h5>
            <hr />
            @if($munidad)
                <div class="row bg-light" style="padding:15px;">
                    <div class="form-group col-md-3">CUOTA TOTAL: <b>{{ $grupo->costo}}</b></div>
                    <div class="form-group col-md-3">TIPO CUOTA: <b>{{ $tcuota}}</b></div>                    
                </div>   
            @endif 
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Memor&aacute;ndum de Apertura:</label>
                    <input name='munidad' id='munidad' type="text" class="form-control" value="{{$munidad}}" disabled/>
                </div>                
                <div class="d-flex flex-row">
                  <div class="p-2">HORARIO: <br /><input type="time" name='hini' id='hini' type="text" class="form-control" aria-required="true" value="{{$grupo->hini}}" disabled/></div>
                  <div class="p-2"><br />A</div>
                  <div class="p-2"><br /><input type="time" name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{$grupo->hfin}}" disabled/></div>
                </div>           
               
                <div class="form-group col-md-2">
                    <label>DIAS:</label>
                    <input type="text" class="form-control" value="{{$grupo->dia}}" disabled />
                </div>
                <div class="form-group col-md-2">
                    <label>Fecha Inicio:</label>
                    <input type="text" class="form-control"  value="{{ $grupo->inicio }}" class="form-control" disabled />
                </div>
                <div class="form-group col-md-2">
                    <label>Fecha T&eacute;rmino:</label>
                    <input type="text" class="form-control" value="{{ $grupo->termino }}" class="form-control" disabled />
                </div>
            </div>   
            <div class="form-row" >             
                <div class="form-group col-md-3">
                    <label>Plantel:</label>
                    <input type="text" class="form-control" value="{{ $grupo->plantel }}" class="form-control" disabled />                    
                </div>                   
               
                <div class="form-group col-md-3">
                    <label>Sector:</label>
                    <input type="text" class="form-control" value="{{ $grupo->sector }}" class="form-control" disabled />                    
                </div>
                <div class="form-group col-md-3">
                    <label>Programa Estrat&eacute;gico:</label>
                    <input type="text" class="form-control" value="{{ $grupo->programa }}" class="form-control" disabled />                    
                </div>                
                <div class="form-group col-md-3">
                    <label>Lugar o Espacio F&iacute;sico:</label>
                    <input name='efisico' id='efisico' type="text" class="form-control" aria-required="true" value="{{$grupo->efisico}}" disabled />
                </div>
            </div>
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Municipio:</label>
                    <input type="text" class="form-control" value="{{ $grupo->muni }}" class="form-control" disabled />                    
                </div> 
                <div class="form-group col-md-6">
                    <label>Dependencia:</label>
                    <input type="text" class="form-control" value="{{ $grupo->depen }}" class="form-control" disabled />                    
                </div>                               
                <div class="form-group col-md-3">
                    <label>Convenio General:</label>
                    <input name='cgeneral' id='cgeneral' type="text" class="form-control" aria-required="true" value="{{$grupo->cgeneral}}" disabled/>
                </div>                
            </div>  
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Fecha Convenio General:</label>                    
                    <input type="date" id="fcgen" name="fcgen" class="form-control"  aria-required="true" value="{{$grupo->fcgen}}" disabled />
                </div>
                <div class="form-group col-md-3">
                    <label>Convenio Espec&iacute;fico:</label>
                    <input name='cespecifico' id='cespecifico' type="text" class="form-control" aria-required="true" value="{{ $grupo->cespecifico}}" disabled/>
                </div>
                <div class="form-group col-md-3">
                    <label>Fecha Convenio Espec&iacute;fico:</label>
                    <input type="date" id="fcespe" name="fcespe" aria-required="true" class="form-control" value="{{$grupo->fcespe}}" disabled />
                </div>                        
                <div class="form-group col-md-3">
                     <label>No. Memor&aacute;dum de Exoneraci&oacute;n:</label>
                     <input type="text" class="form-control" value="{{ $grupo->mexoneracion }}" class="form-control" disabled />                     
                </div>               
            </div>
            <div class="form-row" >
                <div class="form-group col-md-2">
                    <label>Servicio:</label>
                    <input type="text" class="form-control" value="{{ $grupo->tipo_curso }}" class="form-control" disabled />                    
                </div> 
                <div class="form-group col-md-2">
                     <label>Medio Virtual:</label>
                     <input type="text" class="form-control" value="{{ $grupo->medio_virtual}}" class="form-control" disabled />                     
                </div>
                <div class="form-group col-md-3">
                     <label>Link Virtual:</label>
                     <input name='link_virtual' id='link_virtual' type="url" class="form-control" aria-required="true" value="{{$grupo->link_virtual}}" disabled />
                </div>
                <div class="form-group col-md-5">
                    <label>INSTRUCTOR:</label>
                    <input type="text" class="form-control" value="{{$grupo->nombre }}" class="form-control" disabled />                    
                </div>
             </div>
            <div class="form-row" >            
                <div class="form-group col-md-12">
                    <label>Observaciones:</label>
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="5" disabled >{{$grupo->nota}}</textarea>
                </div>
            </div><br />
            <h5><b>DE LA SOLICITUD DE CORRECCIÓN/CANCELACIÓN ARC02</b></h5>
            <hr />
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Memor&aacute;ndum ARC02:</label>
                    <input name='nmunidad' id='nmunidad' type="text" class="form-control" aria-required="true" @if($grupo->nmunidad!='0') value="{{$grupo->nmunidad}}"  @endif />
                </div>            
                <div class="form-group col-md-5">
                    <label>Motivo:</label>
                    {{ Form::select('opcion',$motivo, $grupo->opcion, ['id'=>'opcion','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>                
            </div> 
            <div class="form-row" >            
                <div class="form-group col-md-12">
                    <label>Observaciones:</label>
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="5" >@if($grupo->nmunidad!='0') {{$grupo->observaciones}} @endif</textarea>
                </div>
            </div><br />
                          
                
            <h4><b>ALUMNOS</b></h4>
            <div class="row">
                @include('solicitud.modificarApertura.table')
            </div>
                 
        @endif
    {!! Form::close() !!}    
</div>
    @section('script_content_js')     
        <script src="{{ asset('js/solicitud/apertura.js') }}"></script>             

        <script language="javascript">
            $(document).ready(function(){                
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.apertura.modificar')}}"); $('#frm').submit();}); 
                $("#guardar" ).click(function(){
                    if ($('#nmunidad').val()==''||$('#opcion').val()==''||$('#observaciones').val()==''||$('#observaciones').val()==' ') {
                        alert("Todos los campos deben ser llenados!! ");
                    } else {
                        if(confirm("Esta seguro de ejecutar la acción?")==true){
                            $('#frm').attr('action', "{{route('solicitud.apertura.modguardar')}}"); $('#frm').submit();
                        }
                    }
                });
                $("#deshacer" ).click(function(){if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.moddeshacer')}}"); $('#frm').submit();}});
            });       
        </script>  
    @endsection
@endsection