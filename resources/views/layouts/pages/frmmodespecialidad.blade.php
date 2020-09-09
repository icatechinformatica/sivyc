<!-- Creado por Orlando Chávez - Modificado por Daniel Méndez -->
@extends('theme.sivyc.layout')
<!--contenido de css -->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrapcustomizer.css") }}">
    <link href="{{ asset("vendor/toggle/bootstrap-toggle.css") }}" rel="stylesheet">
    <style type="text/css">
		.card-grid .card-header,
		.card-grid .card-body {
			padding: 0;
		}
		.card-grid .card-header .row div[class^="col"],
		.card-grid .card-body .row div[class^="col"],
		.card-grid .card-grid-caption {
			padding-left: 0.5rem;
			padding-right: 0.5rem;
			padding-bottom: 0.25rem;
			padding-top: 0.25rem;
		}
		.card-grid .card-header .row {
			display: none;
			margin-left: 0;
			margin-right: 0;
		}
		.card-grid .card-body .row {
			position: relative;
			margin-left: 0;
			margin-right: 0;
		}
		.card-grid .card-body .row div[class^="col"] {
			position: static;
		}

		.card-grid .card-body .row div[class^="col"]:last-of-type {
			border-bottom: 1px solid #dee2e6;
		}
		.card-grid .card-body .row:last-of-type div[class^="col"] {
			border-bottom: none !important;
		}
		@media (min-width: 768px) {
			.card-grid .card-grid-caption {
				display: none;
			}
			.card-grid .card-header .row {
				display: flex;
			}

			.card-grid .card-header .row div[class^="col"] {
				border-left: 1px solid #dee2e6;
			}
			.card-grid .card-header .row div[class^="col"]:nth-of-type(1) {
				border-left: none !important;
			}

			.card-grid .card-body .row div[class^="col"] {
				border-bottom: 1px solid #dee2e6;
				border-left: 1px solid #dee2e6;
			}
			.card-grid .card-body .row div[class^="col"]:nth-of-type(1) {
				border-left: none !important;
			}
			.card-grid .card-body .row div[class^="col"] label {
				display: none;
			}
			.card-grid .card-body .row div[class^="col"] a.stretched-link:hover:after {
				color: #212529;
				background-color: rgba(0, 0, 0, .075);
			}
        }

        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }
	</style>
@endsection
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
                    <br><h2>Especialidad Seleccionada: {{$nomesp->nombre}}</h2>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputvalido_perfil">Perfil Profesional con el que se valido</label>
                        <select class="form-control" name="valido_perfil" id="valido_perfil">
                            <option value="{{$sel_espec->id}}">{{$sel_espec->grado_profesional}} {{$sel_espec->area_carrera}}</option>
                            @foreach ($data_espec as $item)
                                <option value="{{$item->id}}">{{$item->grado_profesional}} {{$sel_espec->area_carrera}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputunidad_validacion">Unidad de Capacitación que Solicita Validación</label>
                        <select name="unidad_validacion" id="unidad_validacion" class="form-control">
                            <option value="{{$sel_unidad->unidad}}">{{$sel_unidad->unidad}}</option>
                            @foreach ($data_unidad as $item)
                                <option value="{{$item->unidad}}">{{$item->unidad}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">

                    <div class="form-group col-md-4">
                        <label for="inputmemorandum">Memorandum de Validación</label>
                        <input name="memorandum" id="memorandum" class="form-control" type="text" aria-required="true" value={{$especvalid->memorandum_validacion}}>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input type="date" name="fecha_validacion" id="fecha_validacion" class="form-control" aria-required="true" value="{{$especvalid->fecha_validacion}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemoranum_modificacion">Memorandum de Revalidación</label>
                        <input type="text" name="memorandum_modificacion" id="memorandum_modificacion" class="form-control" aria-required="true" value="{{$especvalid->memorandum_modificacion}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="inputexp_doc"><h2>Observaciones</h2></label>
                        <textarea name="observaciones" id="observaciones" class="form-control" cols="5" rows="8">{{$especvalid->observacion}}</textarea>
                    </div>
                </div>

                <hr style="border-color:dimgray">
                <h2>Selección de Cursos Validados para Impartir</h2>

                <div class="card card-grid mb-4" role="grid" aria-labelledby="gridLabel">
                    <div class="card-header">
                        <div class="row" role="row">
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">NOMBRE</p>
                            </div>
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">RANGOS</p>
                            </div>
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">TIPO DE CURSO</p>
                            </div>
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">CRITERIO DE PAGO</p>
                            </div>
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">ZONA</p>
                            </div>
                            <div class="col-md-2" role="columnheader">
                                <p class="form-control-plaintext">AÑADIR</p>
                            </div>
                        </div>
                        <div id="gridLabel" class="card-grid-caption">
                            <p class="form-control-plaintext">CURSO</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($catcursos as $itemDataCatCurso)
                        <div class="row" role="row">
                            <div class="col-md-2" role="gridcell">
                                <label><h5>NOMBRE</h5></label>
                                <div class="form-control-plaintext text-truncate">{{$itemDataCatCurso->nombre_curso}}</div>
                            </div>
                            <div class="col-md-2" role="gridcell">
                                <label><h5>RANGOS</h5></label>
                                <div class="form-control-plaintext text-truncate">MINIMO {{$itemDataCatCurso->rango_criterio_pago_minimo}} -- MÁXIMO {{$itemDataCatCurso->rango_criterio_pago_maximo}}</div>
                            </div>
                            <div class="col-md-2" role="gridcell">
                                <label><h5>TIPO DE CURSO</h5></label>
                                <div class="form-control-plaintext text-truncate">
                                    @if ($itemDataCatCurso->tipo_curso === "ONLINE")
                                        A DISTANCIA
                                    @else
                                        PRESENCIAL
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2" role="gridcell">
                                <label>CRITERIO DE PAGO</label>
                                <div class="form-control-plaintext text-truncate">
                                    <select class="form-control" name="itemEdit[{{$itemDataCatCurso->id}}][criterio_pago_edit]" id="criterio_pago">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($data_pago as $item)
                                            <option
                                            @foreach ($itemDataCatCurso->especialidadinstructor as $itemCatInstructorCurso)
                                                {{ $itemCatInstructorCurso->pivot->pago_id  }}
                                                {{ ($itemCatInstructorCurso->pivot->pago_id == $item->id) ? 'selected' : '' }}
                                            @endforeach
                                            value="{{$item->id}}">{{$item->perfil_profesional}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" role="gridcell">
                                <label>ZONA</label>
                                <div class="form-control-plaintext text-truncate">
                                    <select class="form-control"  name="itemEdit[{{$itemDataCatCurso->id}}][zona_edit]" id="zona">
                                        <option value="">--SELECCIONAR--</option>
                                        <option
                                            @foreach ($itemDataCatCurso->especialidadinstructor as $itemCat)
                                                {{($itemCat->pivot->zona == 2 ) ? 'selected' : ''}}
                                            @endforeach
                                            value="2">Zona II</option>
                                        <option
                                            @foreach ($itemDataCatCurso->especialidadinstructor as $itemCat)
                                                {{($itemCat->pivot->zona == 3 ) ? 'selected' : ''}}
                                            @endforeach
                                            value="3">Zona III</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" role="gridcell">
                                <label>AÑADIR</label>
                                <div class="form-control-plaintext text-truncate">

                                    <input type="checkbox"
                                        @foreach ($itemDataCatCurso->especialidadinstructor as $itemCatCurso)
                                            {{ ($itemCatCurso->pivot->curso_id == $itemDataCatCurso->id) ? 'checked' : '' }}
                                        @endforeach

                                        data-toggle="toggle"
                                        data-style="ios"
                                        data-on="ON"
                                        data-off="OFF"
                                        data-onstyle="success"
                                        data-offstyle="danger"
                                        name="itemEdit[{{$itemDataCatCurso->id}}][check_cursos_edit]"
                                        value="{{$itemDataCatCurso->id}}">

                                </div>
                            </div>
                        </div>
                        @endforeach
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
                <input type="hidden" name="idesp" id="idesp" value="{{ $id }}">
                <input type="hidden" name="idins" id="idins" value="{{ $idins }}">
                <input type="hidden" name="idespec" id="idespec" value="{{$especvalid->id}}">
                <input type="hidden" name="especialidad" id="especialidad" value="{{ $idesp }}">
        </form>
    </section>
@stop
@section('script_content_js')
    <script src="{{ asset("js/scripts/bootstrap-toggle.js") }}"></script>
@endsection
