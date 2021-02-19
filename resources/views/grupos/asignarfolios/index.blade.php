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
            @if(isset($acta))
            <h5>REFERENCIA DEL ACTA</h5>
            <div class="row bg-light" style="padding:20px">
                    <div class="form-group col-md-3">NUM. ACTA: <b>{{ $acta->num_acta }}</b></div>
                    <div class="form-group col-md-3">FECHA ACTA: <b>{{ $acta->facta }}</b></div>
                    <div class="form-group col-md-2">FOLIO INICIAL: <b>{{ $acta->finicial }}</b></div>
                    <div class="form-group col-md-2">FOLIO FINAL: <b>{{ $acta->ffinal }}</b></div>
                    <div class="form-group col-md-2">FOLIOS ASIGNADOS: <b>{{ $acta->contador }}</b></div>
            </div>
            @endif
       <h5>DATOS DEL CURSO</h5>      
       
        <div class="row bg-light" style="padding:20px">            
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
            <hr/>
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
                        </tr>
                    </thead>
                    @if(isset($alumnos))   
                    <tbody>
                        <?php $asignado = true;?>
                        @foreach($alumnos as $a)
                            <tr>
                                <td> {{ $a->matricula }} </td>
                                <td> {{ $a->alumno }} </td>
                                <td class="text-center"> {{ $a->calificacion }} </td>
                                <td class="text-center"> {{ $a->folio }} </td>
                                <td class="text-center"> {{ $a->fecha_expedicion }} </td>                                
                            </tr>
                            <?php if(($a->folio OR $a->calificacion==NULL) AND $asignado==true)$asignado = false;?>
                        @endforeach                       
                    </tbody>
                    <tfoot>
                        <tr>
                        @if($asignado AND count($alumnos)>0 AND  !$message) 
                            <td colspan="5" class="text-right">{{ Form::button('ASIGNAR FOLIOS', ['id' => 'guardar','class' => 'btn']) }}</td>
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
                $("#guardar" ).click(function(){ $('#frm').attr('action', "{{route('grupos.asignarfolios.guardar')}}"); $('#frm').submit(); });             
            });       
        </script>  
    @endsection
@endsection
