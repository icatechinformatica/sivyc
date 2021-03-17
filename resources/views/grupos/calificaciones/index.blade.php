<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Calificaciones | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
   
    <div class="card-header">
        Registrar Calificaciones
        
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if ($message)
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
        {{ Form::open(['route' => 'grupos.calificaciones.buscar', 'method' => 'post', 'id'=>'frm']) }}                    

         <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CLAVE DEL CURSO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 30]) }}
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['class' => 'btn', 'type' => 'submit']) }}
            </div>
                
        </div>
       @if(isset($curso))      
        <div class="row bg-light" style="padding:20px">
            <div class="form-group col-md-6">
                CURSO: <b>{{ $curso->curso }}</b>
            </div>
            <div class="form-group col-md-4">
                INSTRUCTOR: <b>{{ $curso->nombre }}</b>
            </div>
            <div class="form-group col-md-2">
                DURACI&Oacute;N: <b>{{ $curso->dura }} hrs.</b>
            </div>            
            <div class="form-group col-md-6">
                ESPECIALIDAD: <b>{{ $curso->espe }}</b>
            </div>                        
            <div class="form-group col-md-6">
                &Aacute;REA: <b>{{ $curso->area }}</b>
            </div>
            <div class="form-group col-md-6">
                FECHAS DEL <b> {{ $curso->inicio }}</b> AL <b>{{ $curso->termino }}</b>
            </div>
            <div class="form-group col-md-4">
                HORARIO: <b>{{ $curso->hini }} A {{ $curso->hfin }}</b>
            </div>
            <div class="form-group col-md-2">
                CICLO: <b>{{ $curso->ciclo}}</b>
            </div> 
        </div>        
       
        <div class="row">
            <div class="table-responsive ">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" >MATR&Iacute;CULA</th>
                            <th scope="col">ALUMNOS</th>
                            <th scope="col" class="text-center" width="10%">FOLIO ASIGNADO</th>
                            <th scope="col" class="text-center" width="10%">CALIFICACI&Oacute;N</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php $cambios = false; ?>
                        @foreach($alumnos as $a)
                            <tr>
                                <td> {{ $a->matricula}} </td>
                                <td> {{ $a->alumno}} </td>
                                <td class="text-center"> @if($a->folio=='0') {{ 'NINGUNO' }} @else {{$a->folio}}@endif</td>
                                <td>
                                @if($a->folio=='0')
                                    <?php $cambios = true; ?>
                                    {{ Form::text('calificacion['.$a->id.']', $a->calificacion , ['id'=>$a->id, 'class' => 'form-control numero', 'required' => 'required', 'size' => 1]) }}
                                @else
                                    {{ $a->calificacion }}    
                                @endif 
                                 
                                 </td>                                 
                            </tr>
                        @endforeach                       
                    </tbody>
                    <tfoot>
                        <tr>
                        @if(count($alumnos)>0 AND $fecha_valida>=0 AND $cambios==true) 
                            <td colspan="4" class="text-right">{{ Form::button('GUARDAR CAMBIOS', ['id' => 'guardar','class' => 'btn']) }}</td>
                        @endif
                        </tr>
                    </tfoot>
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
                        $('#frm').attr('action', "{{route('grupos.calificaciones.guardar')}}"); $('#frm').submit(); 
                    }
                });             

                $('.numero').keyup(function (){                    
                    this.value = (this.value + '').replace(/[^0-9NP]/g, '');
                });
            });       
        </script>  
    @endsection
@endsection
