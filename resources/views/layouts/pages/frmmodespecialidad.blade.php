<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Modificación de Especialidad Validada a Impartir | Sivyc Icatech')
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
        <form action="{{ route('especinstructor-modguardar') }}" method="post" id="register_espec">
            @csrf
                <div class="text-center">
                    <h1>Modificar Especialidad Validada a Impartir</h1>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputvalido_perfil">Perfil Profesional con el que se valido</label>
                        <select class="form-control" name="valido_perfil" id="valido_perfil">
                            <option value="{{$sel_espec->id}}">{{$sel_espec->grado_profesional}}</option>
                            @foreach ($data_espec as $item)
                                <option value="{{$item->id}}">{{$item->grado_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputcriterio_pago">Criterio de Pago</label>
                        <select class="form-control" name="criterio_pago" id="criterio_pago">
                            <option value="{{$sel_pago->id}}">{{$sel_pago->perfil_profesional}}</option>
                            @foreach ($data_pago as $item)
                                <option value="{{$item->id}}">{{$item->perfil_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputcriterio_pago">Zona</label>
                        <select class="form-control" name="zona" id="zona">
                            @if ($especvalid->zona == "2")
                                <option value="2" selected>Zona II</option>
                                <option value="3">Zona III</option>
                            @else
                                <option value="2">Zona II</option>
                                <option value="3" selected>Zona III</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputunidad_validacion">Unidad de Capacitación que Solicita Validación</label>
                        <select name="unidad_validacion" id="unidad_validacion" class="form-control">
                            <option value="{{$sel_unidad->unidad}}">{{$sel_unidad->unidad}}</option>
                            @foreach ($data_unidad as $item)
                                <option value="{{$item->unidad}}">{{$item->unidad}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemorandum">Memorandum de Validación</label>
                        <input name="memorandum" id="memorandum" class="form-control" type="text" aria-required="true" value={{$especvalid->memorandum_validacion}}>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input type="date" name="fecha_validacion" id="fecha_validacion" class="form-control" aria-required="true" value="{{$especvalid->fecha_validacion}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputmemoranum_modificacion">Memorandum de Revalidación</label>
                        <input type="text" name="memoranum_modificacion" id="memoranum_modificacion" class="form-control" aria-required="true" value="{{$especvalid->memorandum_modificacion}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md 5">
                        <label for="inputimpartir"><h2>Validado Unicamente Para Impartir</h2></label>
                        <textarea name="impartir" id="impartir" class="form-control" cols="5" rows="8">{{$especvalid->validado_impartir}}</textarea>
                    </div>
                    <div class="form-group col-md 5">
                        <label for="inputexp_doc"><h2>Observaciones</h2></label>
                        <textarea name="observaciones" id="observaciones" class="form-control" cols="5" rows="8">{{$especvalid->observacion}}</textarea>
                    </div>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-1" style="text-align: right;width:0%">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="form-group col-md-11" style="text-align: right;width:100%">
                        <button type="submit" class="btn btn-primary" >Modificar</button>
                    </div>
                </div>
                <br>
                <input type="hidden" name="idesp" id="idesp" value="{{ $idesp }}">
                <input type="hidden" name="idins" id="idins" value="{{ $idins }}">
                <input type="hidden" name="idespec" id="idespec" value="{{$especvalid->id}}">
        </form>
    </section>
@stop

