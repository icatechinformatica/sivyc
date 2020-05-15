<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Edición de Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/instructor/guardar-mod') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
            @csrf
            <div class="text-center">
                <h1>Editar Instructor<h1>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" id="observacion" name="observacion">{{$datains->rechazo}}</textarea>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Datos Personales</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true" value="{{$datains->nombre}}">
                    <input name="id" id="id" type="text" hidden value="{{$datains->id}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true" value="{{$datains->apellidoPaterno}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true" value="{{$datains->apellidoMaterno}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputcurp">CURP</label>
                    <input name='curp' id='curp' type="text" class="form-control" aria-required="true" value="{{$datains->curp}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputrfc">RFC</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true" value="{{$datains->rfc}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfolio_ine">Folio de INE</label>
                    <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true" value="{{$datains->folio_ine}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                    <select class="form-control" name="sexo" id="sexo">
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
                    <select class="form-control" name="estado_civil" id="estado_civil">
                        <option selected value="{{$estado_civil->nombre}}">{{$estado_civil->nombre}}</option>
                        @foreach ($lista_civil as $item)
                            <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input name='fecha_nacimiento' id='fecha_nacimiento' type="date" class="form-control" aria-required="true" value="{{$datains->fecha_nacimiento}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad</label>
                    <input name='entidad' id='entidad' type="text" class="form-control" aria-required="true" value="{{$datains->entidad}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio</label>
                    <input name='municipio' id='municipio' type="text" class="form-control" aria-required="true" value="{{$datains->municipio}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputasentamiento">Asentamiento</label>
                    <input name='asentamiento' id='asentamiento' type="text" class="form-control" aria-required="true" value="{{$datains->asentamiento}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono Personal</label>
                    <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" value="{{$datains->telefono}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcorreo">Correo Electronico</label>
                    <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" value="{{$datains->correo}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputunidad_registra">Unidad que Registra</label>
                    <select class="form-control" name="unidad_registra" id="unidad_registra">
                        <option value="{{$unidad->cct}}">{{$unidad->unidad}}</option>
                        @foreach ($lista_unidad as $value )
                            <option value="{{$value->cct}}">{{$value->unidad}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Tipo de Honorarios</label>
                    <select class="form-control" name="honorario" id="honorario">
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
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            <br>
        </form>
    </section>
@stop

