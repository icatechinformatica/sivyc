<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
   
    <div class="card-header">
        Asignación de Folios
        
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <?php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;
        ?>
        {{ Form::open(['route' => 'grupos.asignarfolios', 'method' => 'post', 'id'=>'frm']) }}                    

         <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CLAVE DEL CURSO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 30]) }}
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['class' => 'btn', 'type' => 'submit']) }}
            </div>
                
        </div>        
       @if(isset($curso))        
            @if(count($acta)>0)
                <h5>{{count($acta)}} ACTA(S) DISPONIBLE(S)</h5>                
                @foreach($acta as $a)
                    <div class="row bg-light" style="padding-top:8px; margin-bottom: 2px ;">                    
                        <div class="form-group col-md-2">ID: <b>{{ str_pad ($a->id, 8, 0, STR_PAD_LEFT)}}</b></div>
                        <div class="form-group col-md-2">MOD: <b>{{ $a->mod }}</b></div>
                        <div class="form-group col-md-2">NUM. ACTA: <b>{{ $a->num_acta }}</b></div>
                        <div class="form-group col-md-2">FECHA ACTA: <b>{{ date('d/m/Y', strtotime($a->facta)) }}</b></div>
                        <div class="form-group col-md-2">FOLIO INICIAL: <b>{{ $a->finicial }}</b></div>
                        <div class="form-group col-md-2">FOLIO FINAL: <b>{{ $a->ffinal }}</b></div>
                        <div class="form-group col-md-2">DISPONIBLE: <b>{{ $a->folio_disponible }}</b></div>                        
                        <div class="form-group col-md-2">TOTAL DISPONIBLES: <b>{{ $a->total-$a->contador }}</b></div>
                        
                    </div>
                    <?php $actas[$a->id] =  str_pad ($a->id, 8, 0, STR_PAD_LEFT); ?>
                @endforeach            
            @endif
        <br />
        <h5>DATOS DEL CURSO</h5>  
        <div class="row bg-light" style="padding:8px">            
            <div class="form-group col-md-3">
                UNIDAD/ACCIÓN MÓVIL: <b>{{ $curso->unidad }}</b>
            </div>
            <div class="form-group col-md-5">
                CURSO: <b>{{ $curso->id }} {{ $curso->curso }}</b>
            </div>
            <div class="form-group col-md-3">
                INSTRUCTOR: <b>{{ $curso->nombre }}</b>
            </div>
            <div class="form-group col-md-3">
                &Aacute;REA: <b>{{ $curso->area }}</b>
            </div>                       
            <div class="form-group col-md-5">
                ESPECIALIDAD: <b>{{ $curso->espe }}</b>
            </div>                        
            
            <div class="form-group col-md-3">
                FECHAS DEL <b> {{ $curso->inicio }}</b> AL <b>{{ $curso->termino }}</b>
            </div>
            <div class="form-group col-md-3">
                HORARIO: <b>{{ $curso->hini }} A {{ $curso->hfin }}</b>
            </div>
            <div class="form-group col-md-2">
                DURACI&Oacute;N: <b>{{ $curso->dura }} hrs.</b>
            </div> 
            <div class="form-group col-md-3">
                CICLO: <b>{{ $curso->ciclo}}</b>
            </div> 
            <div class="form-group col-md-2">
                MODALIDAD: <b>{{ $curso->mod}}</b>
            </div> 
            
        </div>        
        <h5>ALUMNOS</h5>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" >MATR&Iacute;CULA</th>
                            <th scope="col">ALUMNOS</th>
                            <th scope="col" class="text-center" width="10%">CALIFICACI&Oacute;N</th>
                            <th scope="col" class="text-center" width="10%">FOLIO</th>                            
                            <th scope="col" class="text-center" width="10%">EXPEDICI&Oacute;N</th>
                            <th scope="col" class="text-center" width="10%">ESTATUS</th>
                            <th scope="col" class="text-center" width="10%">MOTIVO</th>                        
                        </tr>
                    </thead>
                    @if(isset($alumnos))   
                    <tbody>
                        <?php $asignar = false;?>
                        @foreach($alumnos as $a)
                            <tr>
                                <td> {{ $a->matricula }} </td>
                                <td> {{ $a->alumno }} </td>
                                <td class="text-center"> {{ $a->calificacion }} </td>
                                <td class="text-center"> @if($a->movimiento=='CANCELADO'  AND $a->reexpedicion==false){{ '0' }} @else {{ $a->folio }}@endif </td>                                
                                <td class="text-center"> @if($a->movimiento=='CANCELADO'  AND $a->reexpedicion==false ){{ '' }} @elseif($a->fecha_expedicion) {{ date('d/m/Y', strtotime($a->fecha_expedicion)) }} @endif </td>
                                <td class="text-center"> {{ $a->movimiento}} </td>
                                <td class="text-center"> {{ $a->motivo}} </td>                                    
                            </tr>
                            <?php if(($a->calificacion>5 AND !$a->folio) OR ($a->movimiento=='CANCELADO'  AND $a->reexpedicion==false))$asignar = true;?>
                        @endforeach                       
                    </tbody>
                    <tfoot>
                        <tr>                            
                            <td colspan="3" class="text-right" style="border-color:white;"></td>                                                            
                            @if(count($alumnos)>0 AND  $asignar AND isset($actas))
                                <td colspan="2" class="text-right" style="border-color:white;">                                
                                    {{ Form::select('id_afolio', $actas, NULL ,['id'=>'id_afolio', 'class' => 'form-control mt-2']) }}
                                </td>
                                <td colspan="2" class="text-right" style="border-color:white;">
                                    {{ Form::button('ASIGNAR FOLIOS', ['id' => 'guardar','class' => 'form-control btn']) }}
                                </td>                                    
                            @endif
                                
                                                
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @endif
        {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">
             $(document).ready(function(){                
                $("#guardar" ).click(function(){ 
                    if(confirm("Esta seguro de ejecutar la acción?")==true){ 
                        $('#frm').attr('action', "{{route('grupos.asignarfolios.guardar')}}"); $('#frm').submit(); 
                    }
                });             
            });       
        </script>  
    @endsection
@endsection
