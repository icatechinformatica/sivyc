<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />  
    <div class="card-header text-center">
        <h1>Supervisi&oacute;n Escolar al Instructor.</h1>
    </div>
    <div class="card card-body">
    @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
    @endif
   
    <section class="container g-py-40 g-pt-40 g-pb-0">
    @if(session('mensaje'))
        <div class="card text-gray bg-warning">
            <div class="card-header">
                <div class="row warning">   
                    <div class="col-md-9 ">
                        <br />
                        {{ html_entity_decode(session('mensaje')) }}
                        <br />  <br />                  
                    </div>  
                </div>
            </div>
            
        </div>
        <br />
    @else
        <form action="{{ url('/form/instructor-guardar') }}"  method="post" id="frmInstructor" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="tmpToken" value="{{ $tmpToken }}"/>            
            <div>
                <label><h4>DATOS DEL INSTRUCTOR</h4></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputarch_ine">Fotograf&iacute;a</label>
                    <input id="file_photo" name="file_photo" type="file" class="file-loading"/>
                    <label for="inputarch_ine" class="text-primary">(Permite archivos jpg, jpeg y png; m&aacute;ximo: 2MB)</label>
                </div>                
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="nombre" aria-required="true" value="{{old('nombre')}}"/>                    
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellidoPaterno' id='apellidoPaterno' type="text" class="form-control" aria-required="true" value="{{old('apellidoPaterno')}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellidoMaterno' id='apellidoMaterno' type="text" class="form-control" aria-required="true" value="{{old('apellidoMaterno')}}" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputcurp">Fecha Firma de Contrato</label>
                    <input type='text' id="fecha_contrato" autocomplete="off" readonly="readonly" name="fecha_contrato" class="form-control datepicker" value="{{old('fecha_contrato')}}" />
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputcurp">Fecha Inscripci&oacute;n al Padr&oacute;n de Instructores</label>
                    <input type='text' id="fecha_padron" autocomplete="off" readonly="readonly" name="fecha_padron" class="form-control datepicker" value="{{old('fecha_padron')}}" />
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputcurp">Monto Honorarios</label>
                    <input name='monto_honorarios' id='monto_honorarios' type="text" class="form-control" aria-required="true" value="{{old('monto_honrarios')}}" />
                </div>
            </div>
             <div>
                <label><h4>DATOS DEL CURSO</h4></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputnombre">Nombre de Curso</label>
                    <input name='nombre_curso' id='nombre_curso' type="text" class="form-control" aria-required="true" value="{{old('nombre_curso')}}" />
                </div>            
                 <div class="form-group col-md-3">
                    <label for="inputcurp">Fecha Autorizaci&oacute;n</label>
                    <input type='text' id="fecha_autorizacion" autocomplete="off" readonly="readonly" name="fecha_autorizacion" class="form-control datepicker" value="{{old('fecha_autorizacion')}}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmodalidad">Prop&oacute;sito del Curso</label>
                    <select class="form-control" id="modalidad" name="modalidad">
                      <option value="">--SELECCIONAR--</option>
                      <option value="CAE" {{ old('modalidad') == 'CAE' ? 'selected' : '' }}>PARA EL TRABAJO</option>
                      <option value="EXT" {{ old('modalidad') == 'EXT' ? 'selected' : '' }}> EN EL TRABAJO</option>                      
                    </select>
                </div>                
            </div>
            <label><h5>Peri&oacute;do de Desarrollo</h5></label>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputcurp">Fecha Inicio</label>
                    <input type='text' id="inicio_curso" autocomplete="off" readonly="readonly" name="inicio_curso" class="form-control datepicker" value="{{old('inicio_curso')}}" />
                </div>                 
                 <div class="form-group col-md-4">
                    <label for="inputcurp">Fecha Termino</label>
                    <input type='text' id="termino_curso" autocomplete="off" readonly="readonly" name="termino_curso" class="form-control datepicker" value="{{old('termino_curso')}}" />
                </div>
                <div class="form-group col-md-2">                
                    <label for="inputcurp">Total Mujeres</label>
                    <input name='total_mujeres' id='total_mujeres' type="text" class="form-control" aria-required="true" value="{{old('total_mujeres')}}" />
                </div>                 
                <div class="form-group col-md-2">
                    <label for="inputcurp">Total Hombres</label>
                    <input name='total_hombres' id='total_hombres' type="text" class="form-control" aria-required="true" value="{{old('total_hombres')}}" />                        
                    
                </div>
            </div>
            <label><h5>Horario</h5></label>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputcurp">Hora Inicio</label>
                     <input name='hini_curso' id='hini_curso' type="text" class="form-control" aria-required="true" value="{{old('hini_curso')}}" />
                </div>                 
                 <div class="form-group col-md-3">
                    <label for="inputcurp">Hora Termino</label>
                    <input name='hfin_curso' id='hfin_curso' type="text" class="form-control" aria-required="true" value="{{old('hfin_curso')}}" />
                </div>
                <div class="form-group col-md-3">                   
                   <label for="inputcurp">Horas Diarias</label>
                   <input name='horas_diarias' id='horas_diarias' type="text" class="form-control" aria-required="true" value="{{old('horas_diarias')}}" />
                </div>
                <div class="form-group col-md-3">        
                    <label for="inputcurp">Total Horas</label>
                    <input name='horas_curso' id='horas_curso' type="text" class="form-control" aria-required="true" value="{{old('horas_curso')}}" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcurp">Tipo</label>
                    <select class="form-control" id="tipo_curso" name="tipo_curso" value="{{old('tipo_curso')}}" >
                      <option value="">--SELECCIONAR--</option>
                      <option value="A DISTANCIA" {{ old('tipo_curso') == 'A DISTANCIA' ? 'selected' : '' }} >A DISTANCIA</option>
                      <option value="PRESENCIAL" {{ old('tipo_curso') == 'PRESENCIAL' ? 'selected' : '' }} >PRESENCIAL</option>
                      
                    </select>
                </div>
                 <div class="form-group col-md-6">
                    <label for="inputcurp">Lugar / Medio Virtual</label>
                    <input name='lugar_curso' id='lugar_curso' type="text" class="form-control" aria-required="true" value="{{old('lugar_curso')}}"/>
                </div>                
            </div>
            <br /> 
            <label><h5>Anexar Fotos o Capturas de Pantalla</h5></label>
            <div class="form-row">                                    
                <input id="file_data[]" name="file_data[]" type="file" multiple="true" class="file-loading"/>
                <label for="inputarch_ine" class="text-primary">(Permite archivos jpg, jpeg y png; m&aacute;ximo: 2MB)</label>
            </div>
            <br/><br/>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                      
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            
            
            <br/>
        </form>
    @endif
    </section>
    </div>    
    <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
    <script src="{{ asset('js/supervisiones/validate.frminstructor.js') }}"></script>
@stop

