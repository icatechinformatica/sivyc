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
                <a class="btn btn-danger" href={{$datains->archivo_ine}} target="_blank">Solicitud de Pago</a><br>
                <a class="btn btn-danger" href={{$datains->archivo_domicilio}} target="_blank">Comprobante de Domicilio</a><br>
                <a class="btn btn-danger" href={{$datains->archivo_curp}} target="_blank">CURP</a><br>
                <a class="btn btn-danger" href={{$datains->archivo_alta}} target="_blank">Alta de Instructor</a><br>
        </div>
        <div class="form-row">
            <a class="btn btn-danger" href={{$datains->archivo_bancario}} target="_blank">Datos Bancarios</a><br>
            <a class="btn btn-danger" href={{$datains->archivo_fotografia}} target="_blank">Fotografía</a><br>
            <a class="btn btn-danger" href={{$datains->archivo_estudios}} target="_blank">Estudios</a><br>
            <a class="btn btn-danger" href={{$datains->archivo_otraid}} target="_blank">Otra Identificación</a><br>
        </div>
        <form id="registerinstructor"  method="POST" action="{{ route('saveins') }}" enctype="multipart/form-data">
            @csrf
                <br>
                <label><h2>Datos Personales</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre">Nombre</label>
                        <input name='nombre' id='nombre' value="{{ $datains->nombre }}" type="text" disabled class="form-control" aria-required="true">
                        <input name="id" id="id" value="{{ $datains->id }}" hidden>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_paterno">Apellido Paterno</label>
                        <input name='apellido_paterno' id='apellido_paterno' value="{{ $datains->apellidoPaterno }}" type="text" class="form-control" aria-required="true" disabled>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_materno">Apellido Materno</label>
                        <input name='apellido_materno' id='apellido_materno' value="{{ $datains->apellidoMaterno}}" type="text" class="form-control" aria-required="true" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputcurp">CURP</label>
                        <input name='curp' id='curp' value="{{ $datains->curp}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputrfc">RFC</label>
                        <input name='rfc' id='rfc' value="{{ $datains->rfc}}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputfolio_ine">Folio de INE</label>
                        <input name='folio_ine' id='folio_ine' value="{{ $datains->folio_ine }}" type="text" disabled class="form-control" disabled aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputsexo">Sexo</label>
                        <select class="form-control" name="sexo" id="sexo" disabled>
                            @if ($datains->sexo == 'MASCULINO')
                                <option selected value="MASCULINO">Masculino</option>
                                <option value="FEMENINO">Femenino</option>
                            @else
                                <option value="MASCULINO">Masculino</option>
                                <option selected value="FEMENINO">Femenino</option>
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
                        <input name='fecha_nacimiento' id='fecha_nacimiento' value="{{ $datains->fecha_nacimiento }}" type="date" disabled class="form-control" aria-required="true">
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
                        <input name="domicilio" id="domicilio" value="{{ $datains->domicilio }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputtelefono">Numero de Telefono Personal</label>
                        <input name="telefono" id="telefono" value="{{ $datains->telefono }}" type="tel" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputcorreo">Correo Electronico</label>
                        <input name="correo" id="correo" value="{{ $datains->correo }}" type="email" disabled class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputbanco">Nombre del Banco</label>
                        <input name="banco" id="banco" value="{{ $datains->banco }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputclabe">Clabe Interbancaria</label>
                        <input name="clabe" id="clabe" value="{{ $datains->interbancaria }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputnumero_cuenta">Numero de Cuenta</label>
                        <input name="numero_cuenta" value="{{ $datains->no_cuenta }}" id="numero_cuenta" type="text" disabled class="form-control" aria-required="true">
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
                            <input type="file" accept="application/pdf" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF" disabled value="{{$datains->archivo_fotografia}}">
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
                        <input id="numero_control" name="numero_control" value="{{ $datains->numero_control }}" type="text" disabled class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputhonorario">Tipo de Honorario</label>
                        <select class="form-control" name="honorario" id="honorario" disabled>
                            @if ($datains->tipo_honorario == 'HONORARIOS')
                                <option selected value="HONORARIOS">Honorarios</option>
                                <option value="ASALARIADO ASIMILADO">Asalariado Asimilado</option>
                            @else
                                <option value="HONORARIOS">Honorarios</option>
                                <option selected value="ASALARIADO ASIMILADO">Asalariado Asimilado</option>
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
                            <a class="btn btn-info" href="{{route('instructor-perfil', ['id' => $datains->id])}}">Agregar Perfil Profesional</a>
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
                                    <a class="btn btn-info" href="{{route('instructor-editespectval', ['id' => $item->id, 'idins' => $datains->id])}}">Modificar</a>
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
                            <a class="btn btn-info" href="{{route('instructor-curso', ['id' => $datains->id])}}">Agregar Curso Validado para Impartir</a>
                        </div>
                    </div>
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

