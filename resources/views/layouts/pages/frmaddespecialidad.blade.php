<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Especialidad Validada a Impartir | Sivyc Icatech')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> hay algunos problemas con los campos.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ route('especinstructor-guardar') }}" method="post" id="register_espec">
            @csrf
                <div class="text-center">
                    <h1>Añadir Especialidad Validada a Impartir</h1>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputvalido_perfil">Perfil Profesional con el que se valido</label>
                        <select class="form-control" name="valido_perfil" id="valido_perfil">
                            <option value="sin especificar">SIN ESPECIFICAR</option>
                            @foreach ($perfil as $item)
                        <option value="{{$item->id}}">{{$item->grado_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputcriterio_pago">Criterio de Pago</label>
                        <select class="form-control" name="criterio_pago" id="criterio_pago">
                            <option value="sin especificar">SIN ESPECIFICAR</option>
                            @foreach ($pago as $item)
                                <option value="{{$item->id}}">{{$item->perfil_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputcriterio_pago">Zona</label>
                        <select class="form-control" name="zona" id="zona">
                            <option value="sin especificar">SIN ESPECIFICAR</option>
                            <option value="2">Zona II</option>
                            <option value="3">Zona III</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputunidad_validacion">Unidad de Capacitación que Solicita Validación</label>
                        <select name="unidad_validacion" id="unidad_validacion" class="form-control">
                            <option value="sin especificar">SIN ESPECIFICAR</option>
                            @foreach ($data as $item)
                                <option value="{{$item->unidad}}">{{$item->unidad}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemorandum">Memorandum de Validación</label>
                        <input name="memorandum" id="memorandum" class="form-control" type="text" aria-required="true">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input type="date" name="fecha_validacion" id="fecha_validacion" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputmemoranum_modificacion">Memorandum de Modificación</label>
                        <input type="text" name="memoranum_modificacion" id="memoranum_modificacion" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md 5">
                        <label for="inputimpartir"><h2>Validado Unicamente Para Impartir</h2></label>
                        <textarea name="impartir" id="impartir" class="form-control" cols="5" rows="8"></textarea>
                    </div>
                    <div class="form-group col-md 5">
                        <label for="inputexp_doc"><h2>Observaciones</h2></label>
                        <textarea name="observaciones" id="observaciones" class="form-control" cols="5" rows="8"></textarea>
                    </div>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-1" style="text-align: right;width:0%">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="form-group col-md-11" style="text-align: right;width:100%">
                        <button type="submit" class="btn btn-primary" >Agregar</button>
                    </div>
                </div>
                <br>
                <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idins }}">
                <input type="hidden" name="idespec" id="idespec" value="{{ $id }}">
        </form>
    </section>
@stop

