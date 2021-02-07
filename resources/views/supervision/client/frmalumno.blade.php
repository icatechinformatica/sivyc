<!--Elabor� Romelia P�rez Nang�el� - rpnanguelu@gmail.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Registro de Alumnos | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <div class="card-header text-center">
        <h1>Supervisi&oacute;n Escolar a Un Alumno</h1>
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
        <form action="{{ url('/form/alumno-guardar') }}" method="post" id="frmAlumno" enctype="multipart/form-data" >
            @csrf
            <input type="hidden" name="tmpToken" value="{{ $tmpToken }}"/>

            <div>
                <label><h4>DATOS DEL ALUMNO</h4></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputarch_ine">Fotograf&iacute;a</label>
                    <input id="file_photo" name="file_photo" type="file" class="file-loading"/>
                </div>
                <div class="form-group col-md-12">
                    <label for="inputarch_ine" class="text-primary">(Permite archivos jpg, jpeg y png; m&aacute;ximo: 2MB)</label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true" value="{{old('nombre')}}"/>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellidoPaterno' id='apellidoPaterno' type="text" class="form-control" aria-required="true" value="{{old('apellidoPaterno')}}"/>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellidoMaterno' id='apellidoMaterno' type="text" class="form-control" aria-required="true" value="{{old('horas_curso')}}"/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputedad">Edad</label>
                    <input name='edad' id='edad' type="text" class="form-control" aria-required="true" value="{{old('edad')}}"/>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputescolaridad">Escolaridad</label>
                    {{ Form::select('escolaridad', $escolaridad, null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfecha_inscripcion">Fecha Inscripci&oacute;n al Curso</label>
                    <input type='text' id="fecha_inscripcion" autocomplete="off" readonly="readonly" name="fecha_inscripcion" class="form-control datepicker" value="{{old('fecha_inscripcion')}}" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputdocumentos">Documentos Personales Entregados</label>
                    <input name='documentos' id='documentos' type="text" class="form-control" aria-required="true" value="{{old('documentos')}}"/>
                </div>
            </div>

             <div>
                <label><h4>DATOS DEL CURSO</h4></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="inputnombre_curso">Nombre del Curso</label>
                    <input name='curso' id='curso' type="text" class="form-control" aria-required="true" value="{{old('curso')}}"/>
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputfecha_autorizacion">Fecha Autorizaci&oacute;n</label>
                    <input type='text' id="fecha_autorizacion" autocomplete="off" readonly="readonly" name="fecha_autorizacion" class="form-control datepicker" value="{{old('fecha_autorizacion')}}" />
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputtipo">Tipo</label>
                    <select class="form-control" id="tipo" name="tipo">
                      <option value="">--SELECCIONAR--</option>
                      <option value="A DISTANCIA" {{ old('tipo_curso') == 'A DISTANCIA' ? 'selected' : '' }}>A DISTANCIA</option>
                      <option value="PRESENCIAL" {{ old('tipo_curso') == 'PRESENCIAL' ? 'selected' : '' }}>PRESENCIAL</option>

                    </select>
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputlugar">Lugar / Medio Virtual</label>
                    <input name='lugar' id='lugar' type="text" class="form-control" aria-required="true" value="{{old('lugar')}}" />
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputlugar">Cuota de Recuperaci&oacute;n Pagada</label>
                    <input name='cuota' id='cuota' type="text" class="form-control" aria-required="true" value="{{old('cuota')}}"/>
                </div>
            </div>
            <label><h5>Peri&oacute;do de Desarrollo</h5></label>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputfecha_inicio">Fecha Inicio</label>
                    <input type='text' id="fecha_inicio" autocomplete="off" readonly="readonly" name="fecha_inicio" class="form-control datepicker" value="{{old('fecha_inicio')}}" />
                </div>
                 <div class="form-group col-md-3">
                    <label for="inputfecha_termino">Fecha Termino</label>
                    <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino" class="form-control datepicker" value="{{old('fecha_termino')}}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhinicio">Hora Inicio</label>
                     <input name='hinicio' id='hinicio' type="text" class="form-control" aria-required="true" value="{{old('hinicio')}}" />
                </div>
                 <div class="form-group col-md-3">
                    <label for="inputhfin">Hora Termino</label>
                    <input name='hfin' id='hfin' type="text" class="form-control" aria-required="true" value="{{old('hfin')}}" />
                </div>
            </div>

            <br />
            <label><h5>Anexar Fotos o Capturas de Pantalla</h5></label>
            <div class="form-row">
                <input id="file_data[]" name="file_data[]" type="file" multiple="true" class="file-loading"/>
                <div class="form-group col-md-12">
                    <label for="inputarch_ine" class="text-primary">(Permite archivos jpg, jpeg y png; m&aacute;ximo: 2MB)</label>
                </div>
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
   <script src="{{ asset('js/supervisiones/validate.frmalumno.js') }}"></script>

@stop

