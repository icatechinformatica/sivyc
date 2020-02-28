<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <form id="registerinstructor">
            @csrf
                <div class="text-center">
                    <h1>Ver Instructor<h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label><h2>Datos Personales</h2></label>
                    </div>
                    <div class="fomr-group col-md-9" style="text-align: right;width:100%">
                        <button type="button" id="mod_instructor" class="btn btn-warning btn-lg">Modificar Campos</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre">Nombre Completo</label>
                        <input name='nombre' id='nombre' value="{{ $getinstructor->nombre }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputcurp">CURP</label>
                        <input name='curp' id='curp' value="{{ $getinstructor->curp}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputrfc">RFC</label>
                        <input name='rfc' id='rfc' value="{{ $getinstructor->rfc}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputfolio_ine">Folio de INE</label>
                        <input name='folio_ine' id='folio_ine' value="{{ $getinstructor->folio_ine }}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputsexo">Sexo</label>
                            <select disabled class="form-control" id="sexo">
                                <option value="{{ $getinstructor->sexo }}">{{$getinstructor->sexo}}</option>
                            </select>
                    </div>
                    <div class="form-gorup col-md-4">
                        <label for="inputestado_civil">Estado Civil</label>
                            <select disabled class="form-control" id="estado_civil" name="estado_civil">
                                <option value="{{ $getinstructor->estado_civil }}">{{ $getinstructor->estado_civil }}</option>
                            </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                        <input name='fecha_nacimiento' id='fecha_nacimiento' value="{{ $getinstructor->fecha_nacimiento }}" type="date" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputlugar_nacimiento">Lugar de Nacimiento</label>
                        <input name='lugar_nacimiento' id='lugar_nacimiento' value="{{ $getinstructor->asentamiento }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputlugar_residencia">Lugar de Residencia</label>
                        <input name='lugar_residencia' id='lugar_residencia' value="{{ $getinstructor->entidad }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-10">
                        <label for="inputdomicilio">Direccion de Domicilio</label>
                        <input name="domicilio" id="domicilio" value="{{ $getinstructor->domicilio }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputtelefono">Numero de Telefono Personal</label>
                        <input name="telefono" id="telefono" value="{{ $getinstructor->telefono }}" type="tel" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputcorreo">Correo Electronico</label>
                        <input name="correo" id="correo" value="{{ $getinstructor->correo }}" type="email" disabled class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputbanco">Nombre del Banco</label>
                        <input name="banco" id="banco" value="{{ $getinstructor->banco }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputclabe">Clabe Interbancaria</label>
                        <input name="clabe" id="clabe" value="{{ $getinstructor->interbancaria }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputnumero_cuenta">Numero de Cuenta</label>
                        <input name="numero_cuenta" value="{{ $getinstructor->no_cuenta }}" id="numero_cuenta" type="text" disabled class="form-control" aria-required="true">
                    </div>

                </div>
                <hr style="border-color:dimgray">
                <label><h2>Datos Academicos</h2></label>
                <br>
                @if (count($perfil) > 0)
                    <label><h4>Perfil Profesional</h4></label>
                    <table class="table table-bordered" id="table-perfprof">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Especialidad</th>
                                <th scope="col">Clave Especialidad</th>
                                <th scope="col">Nivel de Estudios</th>
                                <th width="85px">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perfil as $item)
                                <tr>
                                    <th scope="row">{{$item->carrera}}</th>
                                    <td>{{ $item->especialidad }}</td>
                                    <td>{{ $item->clave_especialidad }}</td>
                                    <td>{{ $item->nivel_estudios_cubre_especialidad }}</td>
                                    <td>
                                        {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                        {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h1>No hay Registros</h1>
                @endif
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <caption>Lista de Perfiles Profesionales</caption>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-info" href="{{route('instructor-perfil', ['id' => $getinstructor->id])}}">Agregar Perfil Profesional</a>
                        </div>
                    </div>
                </div>
                <!-- *** START Text areas *** -->
                    <div class="form-row">
                        <label for="inputexp_laboral"><h4>Experiencia Laboral</h4></label>
                        <textarea cols="6" rows="6" id="exp_laboral" name="exp_laboral" disabled class="form-control">{{ $getinstructor->experiencia_laboral }}</textarea>
                    </div>
                    <br>
                    <div class="form-row">
                        <label for="inputexp_docente"><h4>Experiencia Docente</h4></label>
                        <textarea cols="6" rows="6" id="exp_docente" name="exp_docente" disabled class="form-control">{{ $getinstructor->experiencia_docente }}</textarea>
                    </div>
                    <br>
                    <div class="form-row">
                        <label for="inputcursos_recibidos"><h4>Cursos Recibidos</h4></label>
                        <textarea cols="6" rows="6" id="cursos_recibidos" name="cursos_recibidos" disabled class="form-control">{{ $getinstructor->cursos_recibidos }}</textarea>
                    </div>
                    <br>
                    <div class="form-row">
                        <label for="inputcursos_conocer"><h4>Cursos CONOCER</h4></label>
                        <textarea cols="6" rows="6" id="cursos_conocer" name="cursos_conocer" disabled class="form-control">{{ $getinstructor->cursos_conocer }}</textarea>
                    </div>
                    <br>
                    <div class="form-row">
                        <label for="inputcursos_impartidos"><h4>Cursos Impartidos</h4></label>
                        <textarea cols="6" rows="6" id="cursos_impartidos" name="cursos_impartidos" disabled class="form-control">{{ $getinstructor->cursos_impartidos }}</textarea>
                    </div>
                <!-- *** END Text areas *** -->
                <br>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="inputcapacitado_icatech"><h6>Capacitado por el ICATECH</h6></label>
                        <select class="form-control" id="capacitado_icatech" disabled>
                            <option>No</option>
                            <option>Si</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label for="inputcursos_recicatech"><h4>Cursos Recibidos por el ICATECH</h4></label>
                    <textarea cols="6" rows="6" id="cursos_recicatech" name="cursos_recicatech" disabled class="form-control">{{ $getinstructor->curso_recibido_icatech }}</textarea>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputcv">Curriculum Vitae</label>
                        <input name="cv" id="cv" type="file" accept="application/pdf" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <label><h2>Datos Institucionales</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputnumero_control">Numero de Control</label>
                        <input id="numero_control" name="numero_control" value="{{ $getinstructor->numero_control }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="inputhonorario">Tipo de Honorario</label>
                        <select class="form-control" id="tipo_honorario" disabled>
                            <option value="{{ $getinstructor->tipo_honorario }}">{{ $getinstructor->tipo_honorario }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputregistro_agente">Registo Agente Capacitador Externo STPS</label>
                        <input id="registro_agente" name="registro_agente" value="{{ $getinstructor->registro_agente_capacitador_externo }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputuncap_validacion">Unidad de Capacitacion que Solicita Validacion</label>
                        <input id="uncap_validacion" name="uncap_validacion" value="{{ $getinstructor->unidad_capacitacion_solicita_validacion_instructor }}" type="text" disabled class="form-control " aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemo_validacion">Memorandum de Validacion</label>
                        <input id="memo_validacion" name="memo_validacion" value="{{ $getinstructor->memoramdum_validacion }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemo_mod">Modificacion de Memorandum</label>
                        <input id="memo_mod" name="memo_mod" type="text" value="{{ $getinstructor->modificacion_memo }}" class="form-control" disabled aria-required="true">
                    </div>
                </div>
                <br>
                <label><h4>Cursos Validados para Impartir</h4></label>
                <table class="table table-bordered" id="table-curval">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                            <th scope="col">Handle</th>
                            <th width="85px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                            <td>
                                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Jacob</td>
                            <td>Thornton</td>
                            <td>@fat</td>
                            <td>
                                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Larry</td>
                            <td>the Bird</td>
                            <td>@twitter</td>
                            <td>
                                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <caption>Lista de Cursos Validados para Impartir</caption>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-info" href="{{route('instructor-curso')}}">Agregar Curso Validado para Impartir</a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-row">
                    <label for="inputobservacion">Observaciones</label>
                    <textarea cols="6" rows="6" id="observacion" name="observacion" disabled class="form-control">{{ $getinstructor->observaciones }}</textarea>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" >Guardar Cambios</button>
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop

