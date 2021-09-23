<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Modificación de Perfil Profesional | Sivyc Icatech')
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
        <form action="{{ route('modperfilinstructor-guardar') }}" method="post" id="modperf_prof">
            @csrf
                <div class="text-center">
                    <h1>Modificar Perfil Profesional</h1>
                </div>
                <label><h2>Modificar Perfil Profesional</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputgrado_prof">Grado Profesional</label>
                        <input name="grado_prof" id="grado_prof" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->grado_profesional}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputarea_carrera">Area de la carrera</label>
                        <input name="area_carrera" id="area_carrera" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->area_carrera}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputestatus">Estatus</label>
                        <select class="form-control" name="estatus" id="estatus">
                            <option value="{{$sel_status->estatus}}" selected>{{$sel_status->estatus}}</option>
                            @foreach ($data_status as $item)
                                <option value="{{$item->estatus}}">{{$item->estatus}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_pais">Pais de la Institución Educativa</label>
                        <input name="institucion_pais" id="institucion_pais" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->pais_institucion}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_entidad">Entidad de la Institución Educativa</label>
                        <input name="institucion_entidad" id="institucion_entidad" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->entidad_institucion}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_ciudad">Ciudad de la Institución Educativa</label>
                        <input name="institucion_ciudad" id="institucion_ciudad" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->ciudad_institucion}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputinstitucion_nombre">Nombre de la Institución Educativa</label>
                        <input name="institucion_nombre" id="institucion_nombre" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->nombre_institucion}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_documento">Fecha de Expedicion del Documento</label>
                        <input name="fecha_documento" id="fecha_documento" type="date" class="form-control" aria-required="true" value="{{$perfil_ins->fecha_expedicion_documento}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfolio_documento">Folio del Documento</label>
                        <input name="folio_documento" id="folio_documento" type="text" class="form-control" aria-required="true" value="{{$perfil_ins->folio_documento}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputicursos_recibidos">Cursos Recibidos</label>
                        <select name="cursos_recibidos" id="cursos_recibidos" class="form-control">
                            @if ($perfil_ins->cursos_recibidos == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div>
                    <!-- <div class="form-group col-md-3">
                        <label for="inputconocer">Estandar CONOCER</label>
                        <select name="conocer" id="conocer" class="form-control">
                            @if ($perfil_ins->estandar_conocer == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputinstitucion_ciudad">Registro de capacitador externo STPS</label>
                        <select name="stps" id="stps" class="form-control">
                            @if ($perfil_ins->registro_stps == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div> -->
                    <div class="form-group col-md-3">
                        <label for="inputicapacitador_icatech">Capacitador ICATECH</label>
                        <select name="capacitador_icatech" id="capacitador_icatech" class="form-control">
                            @if ($perfil_ins->capacitador_icatech == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputcursos_recibidos">Cursos Recibidos ICATECH</label>
                        <select name="recibidos_icatech" id="recibidos_icatech" class="form-control">
                            @if ($perfil_ins->recibidos_icatech == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputcursos_impartidos">Cursos Impartidos</label>
                        <select name="cursos_impartidos" id="cursos_impartidos" class="form-control">
                            @if ($perfil_ins->cursos_impartidos == "SI")
                                <option value="SI" selected>SI</option>
                                <option value="NO">NO</option>
                            @else
                                <option value="SI">SI</option>
                                <option value="NO" selected>NO</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md 5">
                        <label for="inputexp_lab"><h2>Experiencia Laboral</h2></label>
                    <textarea name="exp_lab" id="exp_lab" class="form-control" cols="5" rows="8">{{$perfil_ins->experiencia_laboral}}</textarea>
                    </div>
                    <div class="form-group col-md 5">
                        <label for="inputexp_doc"><h2>Experiencia Docente</h2></label>
                        <textarea name="exp_doc" id="exp_doc" class="form-control" cols="5" rows="8">{{$perfil_ins->experiencia_docente}}</textarea>
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
                <input type="hidden" name="id" id="id" value="{{ $id }}">
                <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idins }}">
        </form>
    </section>
@stop

