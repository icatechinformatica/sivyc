<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Preinscripción | SIVyC Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
        <link rel="stylesheet" href="{{asset('css/preinscripcion/index.css') }}" />
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/tools/combox_edit.css') }}" />    
@endsection
@section('content')   
    <?php 
        $id_grupo = $folio = $tipo = $id_curso = $id_cerss = $horario = $turnado = "";        
        if($curso){
            $id_curso = $curso->id;
            $tipo = $curso->tipo_curso;
            $id_cerss = $alumnos[0]->id_cerss;
        }
        if($alumnos){                        
            $horario = $alumnos[0]->horario;
            $unidad = $alumnos[0]->unidad;
            $folio = $alumnos[0]->folio_grupo;
            $turnado = $alumnos[0]->turnado;                        
        }
        if($turnado!='VINCULACION' AND !$message AND $turnado) $message = "Grupo turnado a  ".$turnado;
        $consec = 1;
    ?>  
    <div class="card-header">
        Preinscripci&oacute;n / Registro de Grupo 
    </div>
    <div class="card card-body">
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <div class="row"> 
            <div>
                <br />                    
            </div>            
            <form method="post" id="frm" enctype="multipart/form-data" style="width: 100%;" >
                @csrf   
                            
                <div>
                    <label><h4>DATOS DEL CURSO </h4></label>
                    <hr />                    
                </div>
                @if($folio)  
                    <div class="form-row">  
                        <div class="form-group col-md-12">
                            <h4 ><b>Grupo No. {{ $folio_grupo}}</b></h4>
                        </div>
                    </div>
                @endif             
                <div class="form-row">                    
                    <div class="form-group col-md-2">
                        <label>TIPO DE CURSO</label>
                        {{ Form::select('tipo', ['PRESENCIAL'=>'PRESENCIAL','A DISTANCIA'=>'A DISTANCIA'], $tipo, ['id'=>'tipo', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>UNIDAD/ACCI&Oacute;N M&Oacute;VIL</label>
                        {{ Form::select('unidad', $unidades, $unidad, ['id'=>'unidad','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-5">
                        <label>CURSO</label>
                        {{ Form::select('id_curso', $cursos, $id_curso, ['id'=>'id_curso','old'=>'curso', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>       
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>HORARIO</label>
                        <input name='horario' id='horario' type="text" class="form-control" aria-required="true" value="{{ $horario }}"/>
                    </div>
                    <div class="form-group col-md-4">
                          <label><input type="checkbox" value="cerss" id="cerss_ok" @if($id_cerss){{'checked'}}@endif>&nbsp;&nbsp;CERSS</label>      
                          {{ Form::select('cerss', $cerss, $id_cerss, ['id'=>'cerss','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR','disabled'=>'disabled'] ) }}                  
                    </div>                 
                </div>
                
                <br />
                <br />
                <div>
                    <label><h4>ALUMNOS</h4></label>
                    <hr />
                </div>
                @if($activar)
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <b><label id="etiqueta">CURP</label></b>
                        <input name='busqueda' id='busqueda' oninput="validarInput(this)" type="text" class="form-control" value="{{old('curp')}}"/>
                        <pre id="resultado"></pre>
                    </div>
                    <div class="col-md-4"><br />
                        <button type="button" id="agregar" class="btn btn-success">AGREGAR</button>                                                                  
                    </div>
                </div>
                @endif
                <div class="form-row">
                    @include('preinscripcion.tableAlumnos')
                </div>
                <br />   
                             
            </form>
        </div>    
              
    </div>

    @section('script_content_js') 
        <script src="{{asset('js/preinscripcion/grupo.js')}}"></script>        
        <script src="{{asset('js/preinscripcion/tableAlumnos.js')}}"></script>        	
        <script language="javascript">            
            $(document).ready(function(){    
                $("#agregar").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.save')}}"); $('#frm').submit(); });
                $("#nuevo").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.nuevo')}}"); $('#frm').submit(); });
                $("#update").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.update')}}"); $('#frm').submit(); });
                $("#turnar").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.turnar')}}"); $('#frm').submit(); }); 
            });
        </script>
    @endsection
@endsection
