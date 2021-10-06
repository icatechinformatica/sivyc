<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')

@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection

@section('content')
    <div class="card-header">Consulta de Folios Asignados</div>

    <div class="card card-body" style="min-height:450px;">
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

        <div class="row">
            <div class="col">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tipoBusqueda">Busqueda por</label>
                    </div>
                    <select class="custom-select" id="tipoBusqueda">
                        <option {{$busquedaGeneral == 'null' ? 'selected' : ''}}>selecionar</option>
                        <option {{$busquedaGeneral == '1' ? 'selected' : ''}} value="1">Curso</option>
                        <option {{$busquedaGeneral == '2' ? 'selected' : ''}} value="2">Alumno</option>
                        <option {{$busquedaGeneral == '3' ? 'selected' : ''}} value="3">Rango de folios</option>
                    </select>
                </div>
            </div>
            <div class="col"></div>
        </div>

        {{-- busqueda por rango de folios --}}
        {{-- {{$busquedaGeneral != '3' ? 'class'=>'d-none' : ''}} --}}
        {{ Form::open(['method' => 'post','id'=>'frm']) }} 
            <div id="classRango" class="row form-inline {{$busquedaGeneral != '3' ? 'd-none' : ''}}">
                {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4 mt-3','title' => 'UNIDAD']) }}
                {{ Form::select('mod', ['EXT'=>'EXT','CAE'=>'CAE','GRAL'=>'GENERAL'], $mod ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title' => 'MODALIDAD')) }}                    
                {{ Form::text('finicial', $finicial, ['id'=>'finicial', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => ' FOLIO INICIAL', 'title' => ' FOLIO INICIAL','size' => 20]) }}
                {{ Form::text('ffinal', $ffinal, ['id'=>'ffinal', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => 'FOLIO FINAL', 'title' => 'FOLIO FINAL', 'size' => 20]) }}                    
                {{ Form::text('busquedaGeneral', '3', ['id'=>'busquedaGeneral', 'class' => 'd-none']) }}                    
                {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4 mt-3']) }}
                {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4 mt-3']) }}
            </div>
        {!! Form::close() !!}

        {{-- busqueda por curso --}}
        <form id="formCurso" action="{{ route('consultas.folios') }}"method="post" class="{{$busquedaGeneral != '1' ? 'd-none' : ''}}">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="cursoS">Buscar alumnos por</label>
                        </div>
                        <select class="custom-select" id="cursoS" name="cursoS">
                            <option value="">seleccionar</option>
                            <option {{$buscarComboC == '1' ? 'selected' : ''}} value="1">Nombre del curso</option>
                            <option {{$buscarComboC == '2' ? 'selected' : ''}} value="2">Clave del curso</option>
                            {{-- <option value="3">Nombre instructor</option> --}}
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <input type="text" class="form-control" id="datoCurso" name="datoCurso" value="{{$buscarDatoC}}">
                    </div>
                    <input type="text" id="claveCurso" name="claveCurso" class="d-none" value="{{$buscarDatoClave}}">
                    <input type="text" class="d-none" value="1" id="busquedaGeneral" name="busquedaGeneral">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-info pb-1">Buscar</button>
                    <button id="btnXlsCurso" type="button" style="width: 80px" class="btn btn-warning pb-1">XLS</button>
                </div>
            </div>
        </form>

        {{-- busqueda por alumno --}}
        <form id="formAlumno" action="{{ route('consultas.folios') }}" method="post" class="{{$busquedaGeneral != '2' ? 'd-none' : ''}}">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="alumnoS">Buscar alumno por</label>
                        </div>
                        <select class="custom-select" id="alumnoS" name="alumnoS">
                            <option value="" selected>seleccionar</option>
                            <option {{$buscarCombo == '1' ? 'selected' : ''}} value="1">Curp</option>
                            <option {{$buscarCombo == '2' ? 'selected' : ''}} value="2">Nombre</option>
                            <option {{$buscarCombo == '3' ? 'selected' : ''}} value="3">Matricula</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <input type="text" class="form-control" id="datoAlumno" name="datoAlumno" value="{{$buscarDato}}">
                    </div>
                    <input type="text" class="d-none" value="2" id="busquedaGeneral" name="busquedaGeneral">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-info pb-1">Buscar</button>
                    <button id="btnXlsAlumno" type="button" style="width: 80px" class="btn btn-warning pb-1">XLS</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">UNIDAD</th>
                            <th class="text-center"><div style="width: 130px;" class="col">CLAVE</div></th>
                            <th class="text-center"><div style="width: 150px;" class="col">CURSO</div></th>
                            <th scope="col" class="text-center">FOLIO</th>
                            <th scope="col" class="text-center">MODALIDAD</th>
                            <th scope="col" class="text-center">EXPEDICI&Oacute;N</th>
                            <th scope="col" class="text-center">ESTATUS</th>                            
                            <th scope="col" class="text-center">MOTIVO</th>
                            <th scope="col" class="text-center">MATR&Iacute;CULA</th>
                            <th class="text-center"><div style="width: 150px;" class="col">ALUMNOS</div></th>
                            <th scope="col" class="text-center">DURACIÓN</th>
                            <th class="text-center"><div style="width: 70px;" class="col">INICIO</div></th>
                            <th class="text-center"><div style="width: 90px;" class="col">TERMINO</div></th>
                            <th scope="col" class="text-center">MES TERMINO</th>
                            <th class="text-center"><div style="width: 100px;" class="col">HORARIO</div></th>
                            <th scope="col" class="text-center">DIAS</th>
                            <th class="text-center"><div style="width: 150px;" class="col">INSTRUCTOR</div></th>
                            <th scope="col" class="text-center">MUNICIPIO</th>
                            <th scope="col" class="text-center">DEPENDENCIA BENEFICIADA</th>
                            <th scope="col" class="text-center">ESPACIO</th>
                            <th scope="col" class="text-center">ESTATUS FORMATO T</th>
                            <th scope="col" class="text-center">CAPACITACIÓN</th>
                            <th scope="col" class="text-center">ESTATUS APERTURA</th>
                            <th scope="col" class="text-center">AUTORIZACI&Oacute;N</th>
                        </tr>
                    </thead>
                    @if(isset($folios))
                    <?php $n=1;   ?>
                    <tbody>                        
                        @foreach($folios as $f)
                            <tr>
                                <td class="text-center">{{ $n++ }} </td>
                                <td class="text-center">{{ $f->unidad }} </td>
                                <td class="text-center">{{ $f->clave}} </td>
                                <td class="text-center">{{ $f->curso}} </td>                            
                                <td class="text-center">{{ $f->folio }} </td>                           
                                <td class="text-center">{{ $f->mod }} </td>
                                <td class="text-center">@if($f->fecha_expedicion) {{ date('d/m/Y', strtotime($f->fecha_expedicion)) }} @endif</td>
                                <td class="text-center">{{ $f->movimiento}} </td>
                                <td class="text-center">{{ $f->motivo}} </td>
                                <td class="text-center">{{ $f->matricula}}  </td>
                                <td class="text-center">{{ $f->alumno}} </td>
                                <td class="text-center">{{ $f->dura}} </td>
                                <td class="text-center">{{ $f->inicio}} </td>
                                <td class="text-center">{{ $f->termino}} </td>
                                <td class="text-center">{{ $f->mes}} </td>
                                <td class="text-center">{{ $f->horario}} </td>
                                <td class="text-center">{{ $f->dia}} </td>
                                <td class="text-center">{{ $f->nombre}} </td>
                                <td class="text-center">{{ $f->muni}} </td>
                                <td class="text-center">{{ $f->depen}} </td>
                                <td class="text-center">{{ $f->efisico}} </td>
                                <td class="text-center">{{ $f->status}} </td>
                                <td class="text-center">{{ $f->tcapacitacion}} </td>
                                <td class="text-center">{{ $f->status_curso}} </td>
                                <td class="text-center">
                                 @if($f->file_autorizacion)
                                    <a class="nav-link"  href="{{ $path_file.$f->file_autorizacion }}" target="_blank">
                                        <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                                    </a>  
                                @else 
                                    {{ "NO ADJUNTADO"}}
                                @endif
                                </td>                                                                                                      
                            </tr>                            
                        @endforeach                       
                    </tbody>                    
                    
                    <tfoot>                       
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>     
    </div>    
@endsection

@section('script_content_js') 
    <script language="javascript">           
        $(document).ready(function(){ 
            $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.folios')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
            $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.folios.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
        });

        $('#btnXlsCurso').click(function() {
            $('#formCurso').attr('action', "{{route('consultas.folios.xls')}}");
            $('#formCurso').submit();
            $('#formCurso').attr('action', "{{route('consultas.folios')}}");
        });

        $('#btnXlsAlumno').click(function() {
            $('#formAlumno').attr('action', "{{route('consultas.folios.xls')}}");
            $('#formAlumno').submit();
            $('#formAlumno').attr('action', "{{route('consultas.folios')}}");
        });

        $('#tipoBusqueda').on('change', () => {
            tipo = $('#tipoBusqueda').val();
            switch (tipo) {
                case '1':
                    $('#classRango').addClass('d-none');
                    $('#formAlumno').addClass('d-none');
                    $('#formCurso').removeClass('d-none');
                    break;
                case '2':
                    $('#classRango').addClass('d-none');
                    $('#formAlumno').removeClass('d-none');
                    $('#formCurso').addClass('d-none');
                    break;
                case '3':
                    $('#classRango').removeClass('d-none');
                    $('#formAlumno').addClass('d-none');
                    $('#formCurso').addClass('d-none');
                    break;
            }
        });

        $('#alumnoS').on('change', () => {
            alumnoSearch = $('#alumnoS').val();
            $('#datoAlumno').val('');
            switch (alumnoSearch) {
                case '1':
                    $('#datoAlumno').attr('placeholder', 'Ingrese la curp');
                    break;
                case '2':
                    $('#datoAlumno').attr('placeholder', 'Ingrese el nombre');
                    break;
                case '3':
                    $('#datoAlumno').attr('placeholder', 'Ingrese la matricula');
                    break;
            }
        });

        $('#cursoS').on('change', () => {
            cursoS = $('#cursoS').val();
            $('#datoCurso').val('');
            switch (cursoS) {
                case '1':
                    $('#datoCurso').attr('placeholder', 'Ingrese el nombre  del curso');
                    break;
                case '2':
                    $('#datoCurso').attr('placeholder', 'Ingrese la clave del curso');
                    break;
                case '3':
                    $('#datoCurso').attr('placeholder', 'Ingrese el nombre  del instructor');
                    break;
            }
        });

        $('#formAlumno').validate({
            rules: {
                alumnoS: {
                    required: true
                },
                datoAlumno: {
                    required: true
                }
            },
            messages: {
                alumnoS: {
                    required: 'Campo requerido'
                },
                datoAlumno: {
                    required: 'Campo requerido'
                }
            }
        });

        $('#formCurso').validate({
            rules: {
                cursoS: {
                    required: true
                },
                datoCurso: {
                    required: true
                }
            },
            messages: {
                cursoS: {
                    required: 'Campo requerido'
                },
                datoCurso: {
                    required: 'Campo requerido'
                }
            }
        });

        // autocompletes
        $('#datoCurso').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('autocomplete.curso') }}",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        search: request.term,
                        tipoCurso: $('#cursoS').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $('#datoCurso').val(ui.item.label);
                $('#claveCurso').val(ui.item.value);
                return false;
            }
        });

        $('#datoAlumno').autocomplete({
            source: function(request, response){
                $.ajax({
                    url: "{{ route('autocomplete.alumno') }}",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        search: request.term,
                        tipoAlumno: $('#alumnoS').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $('#datoAlumno').val(ui.item.label);
                return false;
            }
        });


    </script>  
@endsection
