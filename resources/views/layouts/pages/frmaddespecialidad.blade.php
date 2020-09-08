<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
<!--contenido de css-->
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
                    <br><h2>Especialidad Seleccionada: {{$nomesp->nombre}}</h2>
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
                        <label for="inputmemoranum_modificacion">Memorandum de Revalidación</label>
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

                    <hr style="border-color:dimgray">
                    <h2>Selección de Cursos Validados para Impartir</h2>

                    <div class="card card-grid mb-4" role="grid" aria-labelledby="gridLabel">
                        <div class="card-header">
                            <div class="row" role="row">
                                <div class="col-md-3" role="columnheader">
                                    <p class="form-control-plaintext">NOMBRE</p>
                                </div>
                                <div class="col-md-3" role="columnheader">
                                    <p class="form-control-plaintext">RANGOS</p>
                                </div>
                                <div class="col-md-3" role="columnheader">
                                    <p class="form-control-plaintext">TIPO DE CURSO</p>
                                </div>
                                <div class="col-md-3" role="columnheader">
                                    <p class="form-control-plaintext">AÑADIR</p>
                                </div>
                            </div>
                            <div id="gridLabel" class="card-grid-caption">
                                <p class="form-control-plaintext">CURSO</p>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($cursos as $itemData)
                            <div class="row" role="row">
                                <div class="col-md-3" role="gridcell">
                                    <label><h5>NOMBRE</h5></label>
                                    <div class="form-control-plaintext text-truncate">{{$itemData->nombre_curso}}</div>
                                </div>
                                <div class="col-md-3" role="gridcell">
                                    <label><h5>RANGOS</h5></label>
                                    <div class="form-control-plaintext text-truncate">MINIMO {{$itemData->rango_criterio_pago_minimo}} -- MÁXIMO {{$itemData->rango_criterio_pago_maximo}}</div>
                                </div>
                                <div class="col-md-3" role="gridcell">
                                    <label><h5>TIPO DE CURSO</h5></label>
                                    <div class="form-control-plaintext text-truncate">
                                        @if ($itemData->tipo_curso === "ONLINE")
                                            A DISTANCIA
                                        @else
                                            PRESENCIAL
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3" role="gridcell">
                                    <label>AÑADIR</label>
                                    <div class="form-control-plaintext text-truncate">
                                        <input type="checkbox"
                                            data-toggle="toggle"
                                            data-style="ios"
                                            data-on="ON"
                                            data-off="OFF"
                                            data-onstyle="success"
                                            data-offstyle="danger"
                                            name="check_cursos[]"
                                            value="{{$itemData->id}}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                <br>
                <div class="form-row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{route('instructor-curso', ['id' => $idins])}}">Regresar</a>
                        </div>
                        <div class="pull-right">
                            <input type="submit" value="Agregar" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <br>
                <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idins }}">
                <input type="hidden" name="idespec" id="idespec" value="{{ $id }}">
        </form>
    </section>
@stop
@section('script_content_js')
    <script src="{{ asset("js/scripts/bootstrap-toggle.js") }}"></script>
@endsection
