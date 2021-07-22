<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Apertura | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
    
    <div class="card-header">
        Solicitud / Clave de Apertura        
    </div>
    <div class="card card-body" style=" min-height:450px;">       
        <?php
            $horario = $modalidad = $valor = $munidad = $mov = $disabled = NULL;
            $activar = true;
            if(isset($grupo)){
                $valor = $grupo->folio_grupo;
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
    {{ Form::open(['route' => 'solicitud.apertura', 'method' => 'post', 'id'=>'frm']) }}
        @csrf
         <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. GRUPO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 25]) }}
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
                <div class="form-group col-md-2">COSTO ALUMNO: <b>{{ $grupo->costo_individual }}</b></div>
                <div class="form-group col-md-3">HOMBRES: <b>{{ $grupo->hombre }}</b></div>
                <div class="form-group col-md-2">MUJERES: <b>{{ $grupo->mujer }}</b></div>                  
            </div>            
            
            <h5><b>DE LA APERTURA</b></h5>
            <hr />
            @if($munidad)
                <div class="row bg-light" style="padding:15px;">
                    <div class="form-group col-md-3">COUTA TOTAL: <b>{{ $grupo->costo}}</b></div>
                    <div class="form-group col-md-3">TIPO CUOTA: <b>{{ $tcuota }}</b></div>                    
                </div>   
            @endif 
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Memor&aacute;ndum de Apertura:</label>
                    <input name='munidad' id='munidad' type="text" class="form-control" aria-required="true" value="@if($munidad){{$munidad}}@else{{old('nombre')}}@endif"/>
                </div>                
                <div class="d-flex flex-row">
                  <div class="p-2">HORARIO: <br /><input type="time" name='hini' id='hini' type="text" class="form-control" aria-required="true" value="{{$grupo->hini}}"/></div>
                  <div class="p-2"><br />A</div>
                  <div class="p-2"><br /><input type="time" name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{$grupo->hfin}}"/></div>
                </div>           
               
                <div class="form-group col-md-2">
                    <label>DIAS:</label>
                    <input name='dia' id='dia' type="text" class="form-control" aria-required="true" value="{{$grupo->dia}}"/>
                </div>
                <div class="form-group col-md-2">
                    <label>Fecha Inicio:</label>
                    <input type="date" id="inicio" name="inicio" value="{{ $grupo->inicio }}" class="form-control" >
                </div>
                <div class="form-group col-md-2">
                    <label>Fecha T&eacute;rmino:</label>
                    <input type="date" id="termino" name="termino" value="{{ $grupo->termino }}" class="form-control" >
                </div>
            </div>   
            <div class="form-row" >             
                <div class="form-group col-md-3">
                    <label>Plantel:</label>
                    {{ Form::select('plantel', $plantel, $grupo->plantel, ['id'=>'plantel','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>                   
               
                <div class="form-group col-md-3">
                    <label>Sector:</label>
                    {{ Form::select('sector', $sector, $grupo->sector, ['id'=>'sector','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>
                <div class="form-group col-md-5">
                    <label>Programa Estrat&eacute;gico:</label>
                    {{ Form::select('programa', $programa, $grupo->programa, ['id'=>'programa','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>                
                
            </div>
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Municipio:</label>
                    {{ Form::select('id_municipio', $municipio, $grupo->id_municipio, ['id'=>'id_municipio','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div> 
                <div class="form-group col-md-6">
                    <label>Dependencia:</label>
                    {{ Form::select('depen', $depen, $grupo->depen, ['id'=>'depen','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>                               
                <div class="form-group col-md-3">
                    <label>Convenio General:</label>
                    <input name='cgeneral' id='cgeneral' type="text" class="form-control" aria-required="true" value="{{$grupo->cgeneral}}"/>
                </div>                
            </div>  
            <div class="form-row" >
                <div class="form-group col-md-3">
                    <label>Fecha Convenio General:</label>                    
                    <input type="date" id="fcgen" name="fcgen" class="form-control"  aria-required="true" value="{{$grupo->fcgen}}" />
                </div>
                <div class="form-group col-md-3">
                    <label>Convenio Espec&iacute;fico:</label>
                    <input name='cespecifico' id='cespecifico' type="text" class="form-control" aria-required="true" value="{{ $grupo->cespecifico}}"/>
                </div>
                <div class="form-group col-md-3">
                    <label>Fecha Convenio Espec&iacute;fico:</label>
                    <input type="date" id="fcespe" name="fcespe" aria-required="true" class="form-control" value="{{$grupo->fcespe}}">
                </div>                        
                <div class="form-group col-md-3">
                     <label>No. Memor&aacute;dum de Exoneraci&oacute;n:</label>
                     {{ Form::select('mexoneracion', $exoneracion, $grupo->mexoneracion, ['id'=>'mexoneracion','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>               
            </div>
            <div class="form-row" >
                <div class="form-group col-md-2">
                    <label>Servicio:</label>
                    {{ Form::select('tcurso', $tcurso, $grupo->tipo_curso, ['id'=>'tcurso','class' => 'form-control mr-sm-2'] ) }}
                </div> 
                <div class="form-group col-md-2">
                     <label>Medio Virtual:</label>
                     {{ Form::select('medio_virtual', $medio_virtual, $grupo->medio_virtual, ['id'=>'medio_virtual','class' => 'form-control mr-sm-2','disabled'=>$disabled] ) }}
                </div>
                <div class="form-group col-md-8">
                     <label>Link Virtual:</label>
                     <input name='link_virtual' id='link_virtual' type="url" class="form-control" aria-required="true" value="{{$grupo->link_virtual}}" {{$disabled}} />
                </div>
                
            </div>
            <div class="form-row" > 
                <div class="form-group col-md-7">
                    <label>Lugar o Espacio F&iacute;sico:</label>
                    <input name='efisico' id='efisico' type="text" class="form-control"  value="{{$grupo->efisico}}"/>
                </div>
                <div class="form-group col-md-5">
                    <label>INSTRUCTOR VIGENTE:</label>
                    {{ Form::select('instructor', $instructor, $grupo->id_instructor, ['id'=>'instructor','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -','disabled'=>$disabled] ) }}
                </div>                
            </div>
            <div class="form-row" >            
                <div class="form-group col-md-12">
                    <label>Observaciones:</label>
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="5" >{{$grupo->nota}}</textarea>
                </div>
            </div><br />
                    
           
            <hr/>              
                
            <h4><b>ALUMNOS</b></h4>
            <div class="row">
                @include('solicitud.apertura.table')
            </div>
                 
        @endif
    {!! Form::close() !!}    
</div>
    @section('script_content_js')     
        <script src="{{ asset('js/solicitud/apertura.js') }}"></script>     
        <script src="{{ asset('edit-select/jquery-editable-select.min.js') }}"></script>    

        <script language="javascript">  
            $(document).ready(function(){
                $('#medio_virtual').editableSelect();
                
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.apertura')}}"); $('#frm').submit();}); 
                $("#regresar" ).click(function(){if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.regresar')}}"); $('#frm').submit();}}); 
                $("#guardar" ).click(function(){
                    validaCERT();
                    if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.guardar')}}"); $('#frm').submit();}
                }); 
                $("#inscribir" ).click(function(){if(confirm("Esta seguro de ejecutar la acción?")==true){$('#frm').attr('action', "{{route('solicitud.apertura.aceptar')}}"); $('#frm').submit();}});
                
                $("#depen").change(function(){
                    if($("#depen" ).val() ){
                        var id_convenio= $("#depen").val();
                        var mod =   '<?php echo $modalidad?>';
                         $.ajax({
                            type: "GET",
                            url: "{{ route('solicitud.apertura.cgral') }}",
                            data:{id:id_convenio,mod:mod},
                            contentType: "application/json",              
                            dataType: "json",
                            success: function (data) { //console.log(data);
                                
                                if(data['no_convenio']){$("#cgeneral" ).val(data['no_convenio']);} 
                                else $("#cgeneral" ).val('');
                                if(data['fecha_firma']){$("#fcgen" ).val(data['fecha_firma']);}
                                else $("#fcgen" ).val('');
                            }
                         });  
                     }     
                });  

                $('#dia').keyup(function (){                    
                    this.value = (this.value + '').replace(/[^LUNES A VIERNES MARTES MIERCOLES JUEVES SABADO Y DOMINGO]/g, '');
                });
                
                
                $("#tcurso").change(function(){
                   validaCERT();
                });
                function validaCERT(){
                    if($("#tcurso").val()=='CERTIFICACION'){
                        var hini = $("#hini").val();
                        var hfin = $("#hfin").val();
                        var inicio = $("#inicio").val();
                        var termino = $("#termino").val();
                        if(inicio==termino){ 
                            var d1= "2014-02-14 "+hfin+":00";
                            var d2 = "2014-02-14 "+hini+":00";
                            var a = new Date(d1);
                            var b = new Date(d2);                            
                            var c = ((a-b)/1000);
                            if(c!=36000){
                                 alert("Para el servicio de CERTIFICACI\u00D3N, se deben cubrir 10 horas. ");
                                 exit;
                            }
                        }else{
                            alert('Fechas incorrectas, para las CERTIFICACIONES deben coincidir la fecha inicio y fecha termino.');
                            exit;
                        }
                    }
                }

            });       
        </script>  
    @endsection
@endsection