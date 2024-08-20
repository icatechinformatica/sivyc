@extends('theme.principal.dashboard')

@section('title', 'PERSONAL RECURSOS HUMANOS | Sivyc Icatech')

@section('csscontent')
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrapcustomizer.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrap.min.css") }}">
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
    </style>
@endsection

@section('content')


    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                {!! Form::open(['route' => 'personal.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                    <select name="tipo_busqueda_personal" class="form-control mr-sm-2" id="tipo_busqueda_personal">
                        <option value="">BUSCAR POR TIPO</option>
                        <option value="numero_enlace">NÚMERO DE ENLACE</option>
                        <option value="nombres">NOMBRE COMPLETO</option>
                    </select>

                    {!! Form::text('busquedaPersonal', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                {!! Form::close() !!}
                <div class="btn-actions-pane-right">
                    <div role="group" class="btn-group-sm btn-group">
                        <button class="btn-wide btn btn-success">Nuevo Usuario</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-grid mb-4" role="grid" aria-labelledby="gridLabel">
                            <div class="card-header">
                                <div class="row" role="row" style="width: 100%">
                                    <div class="col-md-4" role="columnheader">
                                        <p class="form-control-plaintext">NÚMERO DE ENLACE</p>
                                    </div>
                                    <div class="col-md-4" role="columnheader">
                                        <p class="form-control-plaintext">NOMBRE</p>
                                    </div>
                                    <div class="col-md-4" role="columnheader">
                                        <p class="form-control-plaintext">DETALLES</p>
                                    </div>
                                </div>
                                <div id="gridLabel" class="card-grid-caption">
                                    <p class="form-control-plaintext">RECURSOS HUMANOS</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row" role="row">
                                    <div class="col-md-4" role="gridcell">
                                        <label><h5>NOMBRE</h5></label>
                                        <div class="form-control-plaintext text-truncate">CURSO</div>
                                    </div>
                                    <div class="col-md-4" role="gridcell">
                                        <label><h5>RANGOS</h5></label>
                                        <div class="form-control-plaintext text-truncate">MINIMO </div>
                                    </div>
                                    <div class="col-md-4" role="gridcell">
                                        <label><h5>TIPO DE CURSO</h5></label>
                                        <div class="form-control-plaintext text-truncate">
                                            PRESENCIAL
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-block text-center card-footer">
                                <!--footer-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
