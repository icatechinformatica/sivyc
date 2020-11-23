<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Rerpote RIAC | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <div class="card-header">
        Reportes de Cursos Autorizados
        
    </div>
    <div class="card card-body" >
        @if ($message = Session::get('success'))
            <div class="row">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <br />
        {{ Form::open([ 'url' => '#', 'method' => 'post', 'id'=>'frm','target'=>'_blank','accept-charset'=>'UTF-8']) }}            
            <div class="container">
                <div class="form-row">
                    <div class="form-group col-md-4">                                            
                        {{ Form::text('clave', null, ['class' => 'form-control', 'placeholder' => 'CLAVE DEL CURSO', 'aria-label' => 'CLAVE DEL CURSO']) }}
                                      
                    </div>
                </div>
                <table class="table table-striped col-md-10">
                  <thead>
                    <tr>
                      <th class="h4" scope="col">Reportes</th>
                      <th class="h4" scope="col"></th>
                      <th class="h4 text-center" scope="col">Opciones</th>
                      
                    </tr>
                  </thead>                                    
                  <tbody>                    
                    <tr>
                      <th class="h6" scope="row"> ARC 02 </th>
                      <th></th>
                      <th class="text-center"><i class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr>
                    <tr>
                      <th class="h6" scope="row"> LISTA DE ASISTENCIA </th>
                      <th></th>
                      <th class="text-center"><i class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr>                                        
                    <tr>
                      <th class="h6" scope="row"> CALIFICACIONES </th>
                      <th></th>
                      <th class="text-center"><i class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr>
                     <tr>
                      <th class="h6" scope="row"> RIAC DE INSCRIPCI&Oacute;N </th>
                      <th></th>
                      <th class="text-center"><i id="botonRIAC-INS" value='riac' class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr>
                     <tr>
                      <th class="h6" scope="row"> RIAC DE ACREDITACI&Oacute;N</th>
                      <th></th>
                      <th class="text-center"><i id="botonRIAC-ACRED" value='riac' class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr>
                    
                    <tr>
                      <th class="h6" scope="row"> RIAC DE CERTIFICACI&Oacute;N</th>
                      <th></th>
                      <th class="text-center"><i id="botonRIAC-CERT" value='riac' class="fa fa-file-pdf-o fa-2x fa-lg text-danger cursor-pointer" ></i></th>                      
                    </tr> 
                    <tr><th colspan="3"></th></tr>                   
                  </tbody>
                </table>            
            </div>
        {!! Form::close() !!}        
    </div>
    @section('script_content_js') 
        <script language="javascript">
             $(document).ready(function(){
                $("#botonRIAC-INS" ).click(function(){ $('#frm').attr('action', "{{route('reportes.ins.pdf')}}"); $('#frm').submit(); });
                $("#botonRIAC-ACRED" ).click(function(){ $('#frm').attr('action', "{{route('reportes.acred.pdf')}}"); $('#frm').submit(); });
                $("#botonRIAC-CERT" ).click(function(){ $('#frm').attr('action', "{{route('reportes.cert.pdf')}}"); $('#frm').submit(); });
                
             });
        </script>  
    @endsection
@endsection
