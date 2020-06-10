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
        <div class="text-center">
            <h1>Ver Instructor<h1>
        </div>
        <h2>Vista de Documentos</h2>
        <div class="form-row">
            @if ($datains->archivo_ine != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_ine}} download>Comprobante INE</a><br>
            @endif
            @if ($datains->archivo_domicilio != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_domicilio}} download>Comprobante de Domicilio</a><br>
            @endif
            @if ($datains->archivo_curp != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_curp}} download>CURP</a><br>
            @endif
            @if ($datains->archivo_alta != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_alta}} download>Alta de Instructor</a><br>
            @endif
        </div>
        <div class="form-row">
            @if ($datains->archivo_bancario != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_bancario}} download>Datos Bancarios</a><br>
            @endif
            @if ($datains->archivo_fotografia != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_fotografia}} download>Fotografía</a><br>
            @endif
            @if ($datains->archivo_estudios != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_estudios}} download>Estudios</a><br>
            @endif
            @if ($datains->archivo_otraid != NULL)
                <a class="btn btn-danger" href={{$datains->archivo_otraid}} download>Otra Identificación</a><br>
            @endif
        </div>
        <form id="registerinstructor"  method="POST" action="{{ route('saveins') }}" enctype="multipart/form-data">
            @csrf
                <br>
                <label><h2>Datos Personales</h2></label>
                <div style="text-align: right;width:100%">
                    @can('instructor.editar_fase2')
                        <button type="button" id="mod_instructor_fase2" class="btn btn-warning btn-lg">Modificar Campos</button>
                    @endcan
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre">Nombre</label>
                        <input name='nombre' id='nombre' value="{{$datains->nombre }}" type="text" disabled class="form-control" aria-required="true">
                        <input name="id" id="id" value="{{$datains->id }}" hidden>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_paterno">Apellido Paterno</label>
                        <input name='apellido_paterno' id='apellido_paterno' value="{{$datains->apellidoPaterno }}" type="text" class="form-control" aria-required="true" disabled>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_materno">Apellido Materno</label>
                        <input name='apellido_materno' id='apellido_materno' value="{{$datains->apellidoMaterno}}" type="text" class="form-control" aria-required="true" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputcurp">CURP</label>
                        <input name='curp' id='curp' value="{{$datains->curp}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputrfc">RFC</label>
                        <input name='rfc' id='rfc' value="{{$datains->rfc}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputfolio_ine">Clave de Elector</label>
                        <input name='folio_ine' id='folio_ine' value="{{$datains->folio_ine }}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputsexo">Sexo</label>
                        <select class="form-control" name="sexo" id="sexo" disabled>
                            @if ($datains->sexo == 'MASCULINO')
                                <option selected value='MASCULINO'>Masculino</option>
                                <option value='FEMENINO'>Femenino</option>
                            @else
                                <option value='MASCULINO'>Masculino</option>
                                <option selected value='FEMENINO'>Femenino</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-gorup col-md-4">
                        <label for="inputestado_civil">Estado Civil</label>
                        <select class="form-control" name="estado_civil" id="estado_civil" disabled>
                            <option selected value="{{$estado_civil->nombre}}">{{$estado_civil->nombre}}</option>
                            @foreach ($lista_civil as $item)
                                <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                        <input name='fecha_nacimientoins' id='fecha_nacimientoins' value="{{$datains->fecha_nacimiento}}" type="date" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputentidad">Entidad</label>
                        <input name='entidad' id='entidad' type="text" class="form-control" aria-required="true" disabled value="{{$datains->entidad}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputmunicipio">Municipio</label>
                        <input name='municipio' id='municipio' type="text" class="form-control" disabled aria-required="true" value="{{$datains->municipio}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputasentamiento">Asentamiento</label>
                        <input name='asentamiento' id='asentamiento' type="text" class="form-control" aria-required="true" disabled value="{{$datains->asentamiento}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-7">
                        <label for="inputdomicilio">Direccion de Domicilio</label>
                        <input name="domicilio" id="domicilio" value="{{$datains->domicilio }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputtelefono">Numero de Telefono Personal</label>
                        <input name="telefono" id="telefono" value="{{$datains->telefono }}" type="tel" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputcorreo">Correo Electronico</label>
                        <input name="correo" id="correo" value="{{$datains->correo }}" type="email" disabled class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputbanco">Nombre del Banco</label>
                        <input name="banco" id="banco" value="{{$datains->banco }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputclabe">Clabe Interbancaria</label>
                        <input name="clabe" id="clabe" value="{{$datains->interbancaria }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputnumero_cuenta">Numero de Cuenta</label>
                        <input name="numero_cuenta" value="{{$datains->no_cuenta }}" id="numero_cuenta" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputarch_ine">Archivo INE</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_ine" name="arch_ine" placeholder="Archivo PDF" disabled value="{{$datains->archivo_ine}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_domicilio">Archivo Comprobante de Domicilio</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF" disabled value="{{$datains->archivo_domicilio}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_curp">Archivo CURP</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF" disabled value="{{$datains->archivo_curp}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_alta">Archivo Alta de Instructor</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_alta" name="arch_alta" placeholder="Archivo PDF" disabled value="{{$datains->archivo_alta}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputarch_banco">Archivo Datos Bancarios</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF" disabled value="{{$datains->archivo_bancario}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_foto">Archivo Fotografia</label>
                            <input type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF" disabled value="{{$datains->archivo_fotografia}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_estudio">Archivo Grado de Estudios</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF" disabled value="{{$datains->archivo_estudios}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_id">Archivo Otra Identificación</label>
                            <input type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF" disabled value="{{$datains->archivo_otraid}}">
                        </div>
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <label><h2>Datos Academicos</h2></label>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputunidad_registra">Unidad que Registra</label>
                        <select class="form-control" name="unidad_registra" id="unidad_registra" disabled>
                            <option value="{{$unidad->cct}}">{{$unidad->unidad}}</option>
                            @foreach ($lista_unidad as $value )
                                <option value="{{$value->cct}}">{{$value->unidad}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputnumero_control">Numero de Control</label>
                        <input id="numero_control" name="numero_control" value="{{$datains->numero_control }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputhonorario">Tipo de Honorario</label>
                        <select class="form-control" name="honorario" id="honorario" disabled>
                            @if ($datains->tipo_honorario == 'HONORARIOS')
                                <option selected value="HONORARIOS">Honorarios</option>
                                <option value="SIN HONORARIOS">Sin Honorarios</option>
                                <option value="INTERNO">Interno</option>
                            @endif
                            @if ($datains->tipo_honorario == 'SIN HONORARIOS')
                                <option value="HONORARIOS">Honorarios</option>
                                <option selected value="SIN HONORARIOS">Sin Honorarios</option>
                                <option value="INTERNO">Interno</option>
                            @endif
                            @if ($datains->tipo_honorario == 'INTERNO')
                                <option value="HONORARIOS">Honorarios</option>
                                <option value="SIN HONORARIOS">Sin Honorarios</option>
                                <option selected value="INTERNO">Interno</option>
                            @endif
                            @if ($datains->tipo_honorario == NULL)
                                <option value="HONORARIOS">Honorarios</option>
                                <option value="SIN HONORARIOS">Sin Honorarios</option>
                                <option value="INTERNO">Interno</option>
                            @endif
                        </select>
                    </div>
                </div>
                <br>
                <label><h4>Perfiles Profesionales</h4></label>
                @if (count($perfil) > 0)
                    <table class="table table-bordered" id="table-perfprof">
                        <thead>
                            <tr>
                                <th scope="col">Grado Profesional</th>
                                <th scope="col">Area de la Carrera</th>
                                <th scope="col">Estatus</th>
                                <th scope="col">Nombre de Institucion</th>
                                <th width="85px">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perfil as $item)
                                <tr>
                                    <th scope="row">{{$item->grado_profesional}}</th>
                                    <td>{{ $item->area_carrera }}</td>
                                    <td>{{ $item->estatus }}</td>
                                    <td>{{ $item->nombre_institucion }}</td>
                                    <td>
                                        @can('instructor.editar_fase2')
                                            <a class="btn btn-info" href="{{route('instructor-perfilmod', ['id' => $item->id, 'idins' => $datains->id])}}">Modificar</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                <div class="alert alert-warning">
                    <strong>Info!</strong> No hay Registros
                  </div>
                @endif
                <br>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <caption>Lista de Perfiles Profesionales</caption>
                        </div>
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <a class="btn btn-info" href="{{route('instructor-perfil', ['id' => $datains->id])}}">Agregar Perfil Profesional</a>
                            @endcan
                        </div>
                    </div>
                </div>
                <br>
                <label><h4>Cursos Validados para Impartir</h4></label>
                @if (count($validado) > 0)
                <table class="table table-bordered" id="table-perfprof">
                    <thead>
                        <tr>
                            <th scope="col">Especialidad</th>
                            <th scope="col">Criterio de Pago</th>
                            <th scope="col">Zona</th>
                            <th scope="col">Obsevaciones</th>
                            <th width="85px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($validado as $item)
                            <tr>
                                <th scope="row">{{$item->nombre}}</th>
                                <td>{{ $item->perfil_profesional }}</td>
                                <td>{{ $item->zona }}</td>
                                <td>{{ $item->observacion }}</td>
                                <td>
                                    @can('instructor.editar_fase2')
                                        <a class="btn btn-info" href="{{ route('instructor-editespectval', ['id' => $item->especialidadinsid,'idins' => $datains->id]) }}">Modificar</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">
                    <strong>Info!</strong> No hay Registros
            @endif
                </div>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <caption>Lista de Cursos Validados para Impartir</caption>
                        </div>
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <a class="btn btn-info" href="{{ route('instructor-curso', ['id' => $datains->id]) }}">Agregar Curso Validado para Impartir</a>
                            @endcan
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                        </div>
                        <div  class="pull-right">
                            <button disabled id="savemodbuttonins" type="submit" class="btn btn-primary" >Guardar Cambios</button>
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop

