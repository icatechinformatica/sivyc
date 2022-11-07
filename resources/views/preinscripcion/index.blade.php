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
        $id_grupo = $folio = $tipo = $id_curso = $id_cerss = $horario = $turnado = $hini = $id_vulnerable =
        $hfin = $termino = $inicio = $id_localidad = $id_muni = $organismo = $modalidad = "";    $costo = null;   
        if($curso){
            $id_curso = $curso->id;
            $costo = $curso->costo;
        }
        if(count($alumnos)>0){ 
            $hfin = substr($alumnos[0]->horario, 8, 5);
            $hini = substr($alumnos[0]->horario, 0, 5);
            $id_cerss = $alumnos[0]->id_cerss;               
            //$hini = $alumnos[0]->hini;
            //$hfin = $alumnos[0]->hfin;
            $inicio = $alumnos[0]->inicio;
            $termino = $alumnos[0]->termino;
            $id_muni = $alumnos[0]->id_muni;
            $id_localidad = $alumnos[0]->clave_localidad;
            $organismo = $alumnos[0]->organismo_publico;
            $unidad = $alumnos[0]->unidad;
            $folio = $alumnos[0]->folio_grupo;
            $turnado = $alumnos[0]->turnado;   
            $id_vulnerable = $alumnos[0]->id_vulnerable;  
            $modalidad = $alumnos[0]->mod; 
            $tipo = $alumnos[0]->tipo_curso;                 
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
                    <div class="form-group col-md-2">
                        <label>UNIDAD/ACCI&Oacute;N M&Oacute;VIL</label>
                        {{ Form::select('unidad', $unidades, $unidad, ['id'=>'unidad','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>MUNICIPIO:</label>
                        {{ Form::select('id_municipio', $municipio, $id_muni, ['id'=>'id_municipio','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="localidad" class="control-label">LOCALIDAD</label>
                        {{--<select class="form-control" id="localidad" name="localidad">
                            <option value="">--SELECCIONAR--</option>
                        </select>--}}
                        {{ Form::select('localidad', $localidad, $id_localidad, ['id'=>'localidad','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>    
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>MODALIDAD</label>
                        {{ Form::select('modalidad', ['EXT'=>'EXTENSION','CAE'=>'CAE'], $modalidad, ['id'=>'modalidad', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>
                    <div class="form-group col-md-5">
                        <label>CURSO</label>
                        {{ Form::select('id_curso', $cursos, $id_curso, ['id'=>'id_curso','old'=>'curso', 'class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                    </div>  
                    <div class="form-group col-md-2">
                        <label>FECHA INICIO:</label>
                        <input type="date" id="inicio" name="inicio" value="{{$inicio}}" class="form-control" >
                    </div>
                    <div class="form-group col-md-2">
                        <label>FECHA TERMINO:</label>
                        <input type="date" id="termino" name="termino" value="{{$termino}}" class="form-control" >
                    </div> 
                </div>
                <div class="form-row">
                    <div class="d-flex flex-row ">
                        <div class="p-2">HORARIO: <br /><input type="time" name='hini' id='hini' type="text" class="form-control" aria-required="true" value="{{$hini}}"/></div>
                        <div class="p-2"><br />A</div>
                        <div class="p-2"><br /><input type="time" name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{$hfin}}"/></div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>ORGANISMO PUBLICO:</label>
                        {{ Form::select('dependencia', $dependencia,$organismo, ['id'=>'dependencia','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div> 
                    <div class="form-group col-md-4">
                        @if ($es_vulnerable == 'true')
                        <label><input type="checkbox" value="vulnerable" id="vulnerable_ok" @if($id_vulnerable){{'checked'}}@endif>&nbsp;&nbsp;GRUPO VULNERABLE</label> 
                        @else
                        <label><input type="checkbox" value="vulnerable" id="vulnerable_ok" @if($id_vulnerable){{'checked'}}@endif disabled>&nbsp;&nbsp;GRUPO VULNERABLE</label> 
                        @endif     
                        {{ Form::select('grupo_vulnerable', $grupo_vulnerable, $id_vulnerable, ['id'=>'grupo_vulnerable','class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR','disabled'=>'disabled'] ) }}                  
                    </div>                
                </div>
                <div class="form-row">
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
                    <div class="col-md-2"><br />
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
                $("#agregar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.save')}}",'target':'_self'}); $('#frm').submit(); });
                $("#nuevo").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.nuevo')}}",'target':'_self'}); $('#frm').submit(); });
                $("#update").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.update')}}",'target':'_self'}); $('#frm').submit(); });
                $("#turnar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.turnar')}}",'target':'_self'}); $('#frm').submit(); });
                $("#comprobante").click(function(){ $('#frm').attr('action', "{{route('preinscripcion.grupo.comprobante')}}"); $('#frm').submit(); });
                $("#btnremplazo").click(function(){if (confirm("Est\u00E1 seguro de ejecutar la acci\u00F3n?")==true) {$('#frm').attr({'action':"{{route('preinscripcion.grupo.remplazar')}}",'target':'_self'}); $('#frm').submit();}});
                $("#generar").click(function(){ $('#frm').attr({'action':"{{route('preinscripcion.grupo.generar')}}", 'target':'_target'}); $('#frm').submit(); });
            });
        </script>
    @endsection
@endsection
