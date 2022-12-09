@extends('theme.sivyc.layout')
<!--llamar a la plantilla agc -->
@section('title', 'Agregar organismo | Sivyc Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
        <link rel="stylesheet" href="{{asset('css/preinscripcion/index.css') }}" />
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/tools/combox_edit.css') }}" />    
@endsection
@section('content')
    <?php
    $area = $activo = $sec = $tip = $id_estado = $id_municipio = $id_localidad = 
    $nombre = $nombre_ti = $telefono = $correo = $dir = $id = null;
    if($organismo){
        $id = $organismo->id;
        $nombre = $organismo->organismo;
        $telefono = $organismo->telefono;
        $correo = $organismo->correo;
        $dir = $organismo->direccion;
        $nombre_ti = $organismo->nombre_titular;
        $area = $organismo->poder_pertenece;
        $sec = $organismo->sector;
        $tip = $organismo->tipo;
        $id_estado= $organismo->id_estado;
        $id_municipio = $organismo->id_municipio;
        $id_localidad = $organismo->clave_localidad;
        if($organismo->activo=='true'){
            $activo = 'ACTIVO';
        }else{$activo='INACTIVO';}
    }
    ?>
    <div class="card-header">
        @if ($update)
            Modificación Organismos Públicos
        @else
            Agregar Organismo Público
        @endif
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div>
                <br />                    
            </div>
            <form method="post" id="frm" enctype="multipart/form-data" style="width: 100%;">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="clave" class="control-label">Organismo</label>
                        <input type="hidden" name="id" id="id" value="{{$id}}">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del organismo" value="{{$nombre}}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nombre" class="control-label">Nombre del titular</label>
                        <input type="text" class="form-control" id="nombre_titular" name="nombre_titular" placeholder="Nombre del titular"
                        value="{{$nombre_ti}}"    required>
                    </div>
                    <!-- Telefono -->
                    <div class="form-group col-md-4">
                        <label for="telefono" class="control-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono"
                            name="telefono" placeholder="telefono" value="{{$telefono}}">
                    </div>
                </div>
                <div class="form-row">
                    <!-- email -->
                    <div class="form-group col-md-4">
                        <label for="correo_ins" class="control-label">Correo electrónico</label>
                        <input type="email" class="form-control" onkeypress="return solonumeros(event)" id="correo_ins"
                            name="correo_ins" placeholder="CORREO DE LA INSTITUCIÓN" value="{{$correo}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="area" class="control-label">Area</label>
                        {{ Form::select('area', $areas,$area, ['id'=>'area','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status" class="control-label">Estado del organismo</label>
                        {{ Form::select('status', $status,$activo, ['id'=>'status','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="estado" class="control-label">Estado</label>
                        {{ Form::select('estado', $estados,$id_estado, ['id'=>'estado','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <!--municipio-->
                    <div class="form-group col-md-3">
                        <label for="municipio" class="control-label">Municipio</label>
                        {{ Form::select('municipio', $municipio, $id_municipio, ['id'=>'municipio','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <!--localidad-->
                    <div class="form-group col-md-3">
                        <label for="localidad" class="control-label">Localidad</label>
                        {{ Form::select('localidad', $localidad, $id_localidad, ['id'=>'localidad','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    {{-- direccion --}}
                    <div class="form-group col-md-3">
                        <label for="direccion" class="control-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="direccion" placeholder="dirección" value="{{$dir}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="sector" class="control-label">Sector</label>
                        {{ Form::select('sector', $sector,$sec, ['id'=>'sector','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipo" class="control-label">Tipo</label>
                        {{ Form::select('tipo', $tipo,$tip, ['id'=>'tipo','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <div class="pull-left">
                            <a type="submit" class="btn btn-red" href="{{route('organismos.index')}}">Regresar</a>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        @if ($update == 'true')
                        <div class="pull-right">
                            <a type="submit" class="btn btn-green" id="actualizar">Actualizar</a>
                        </div>
                        @else
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" id="guardar">Guardar</button>
                        </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @section('script_content_js')
        <script type="text/javascript">
            $(document).ready(function(){
                $("#guardar").click(function(){ $('#frm').attr('action', "{{route('organismos.insert')}}"); $('#frm').submit(); });
                $("#actualizar").click(function(){ $('#frm').attr('action', "{{route('organismos.update')}}"); $('#frm').submit(); });
                $('#estado').change(function(){
                    console.log('print');
                    var estado_id=$(this).val();
                    if($(estado_id != '')){
                        $.get('/organismo/municipio',{estado_id: estado_id}, function(novo){
                            $('#municipio').empty();
                            $('#municipio').append("<option value=''>-- SELECCIONAR --</option>");
                            $.each(novo, function(index,value){
                                $('#municipio').append("<option value='" + index + "'>" + value + "</option>");
                            });
                        });
                    }
                });
                $('#municipio').change(function(){
                    console.log('print');
                    var municipio_id=$(this).val();
                    if($(municipio_id != '')){
                        $.get('/organismo/municipio',{municipio_id: municipio_id}, function(novo){
                            $('#localidad').empty();
                            $('#localidad').append("<option value=''>-- SELECCIONAR --</option>");
                            $.each(novo, function(index,value){
                                $('#localidad').append("<option value='" + index + "'>" + value + "</option>");
                            });
                        });
                    }
                });
                $('#frm').validate({
                    rules:{
                        nombre:{
                            required: true
                        },
                        nombre_titular:{
                            required: true
                        },
                        telefono:{
                            required: true
                        },
                        estado:{
                            required: true
                        },
                        municipio:{
                            required: true
                        },
                        direccion:{
                            required: true
                        },
                        status:{
                            required: true
                        },
                        sector:{
                            required: true
                        }
                    },
                    messages:{
                        nombre:{
                            required: 'Escriba un nombre'
                        },
                        nombre_titular:{
                            required: 'Escriba un nombre'
                        },
                        telefono:{
                            required: 'Escriba el teléfono que pertenece al organismo'
                        },
                        estado:{
                            required: 'Seleccione una opción'
                        },
                        municipio:{
                            required: 'Seleccione una opción'
                        },
                        direccion:{
                            required: 'Escriba la direccion al que pertenece'
                        },
                        status:{
                            required: 'Seleccione una opción'
                        },
                        sector:{
                            required: 'Seleccione una opción'
                        }
                    }
                });
            });
        </script>
    @endsection
@endsection
