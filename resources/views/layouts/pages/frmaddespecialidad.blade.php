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
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
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
    <form action="{{ route('especinstructor-guardar') }}" method="post" id="register_espec">
        @csrf
        <div class="card-header">
            <h2>Añadir Solicitud de Especialidad a Impartir</h2>
            <br><h5>Especialidad Seleccionada: <span id="tituloespecialidad"></span></h5>
        </div>
        <div class="card card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputvalido_perfil">ESPECIALIDAD</label>
                    <select class="form-control" name="especialidad" id="especialidad" onchange="cursos()">
                        <option value="sin especificar">SIN ESPECIFICAR</option>
                        @foreach ($data_especialidad as $item)
                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputvalido_perfil">PERFIL PROFESIONAL CON EL QUE SE VALIDA</label>
                    <select class="form-control" name="valido_perfil" id="valido_perfil">
                        <option value="sin especificar">SIN ESPECIFICAR</option>
                        @foreach ($perfil as $item)
                            <option value="{{$item->id}}">{{$item->grado_profesional}} {{$item->area_carrera}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="inputunidad_validacion">UNIDAD DE CAPACITACIÓN QUE SOLICITA VALIDACIÓN</label>
                    <select name="unidad_validacion" id="unidad_validacion" class="form-control" readonly>
                        {{-- <option value="sin especificar">SIN ESPECIFICAR</option> --}}
                        @foreach ($data as $item)
                        @if($item->id == $unidadUser) <option value="{{$item->unidad}}"  selected>{{$item->unidad}}</option> @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="criterio_pago_instructor">CRITERIO DE PAGO</label>
                    <select name="criterio_pago_instructor" id="criterio_pago_instructor" class="form-control">
                        <option value="sin especificar">SIN ESPECIFICAR</option>
                        @foreach ($pago as $item)
                            <option value="{{$item->id}}">{{$item->perfil_profesional}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="inputexp_doc"><h2>OBSERVACIONES</h2></label>
                    <textarea name="observaciones" id="observaciones" class="form-control" cols="5" rows="8"></textarea>
                </div>
            </div>

                <hr style="border-color:dimgray">
                <h2>SELECCIÓN DE CURSOS SOLICITADOS PARA IMPARTIR</h2>
                <table class="table table-bordered table-responsive-md" id='tablecursos'>
                    <thead>
                        <tr>
                            <td style="font-size:16px; width:25%;">NOMBRE</H5></td>
                            <td style="font-size:16px; width:25%;">RANGOS</td>
                            <td style="font-size:16px; width:25%;">TIPO DE CURSOS</td>
                            <td width="20%" style="font-size:16px;">AÑADIR <br><input  type="checkbox" id="ckbCheckAll"
                                data-toggle="toggle"
                                data-style="ios"
                                data-on= "."
                                data-off= "."
                                data-onstyle="success"
                                data-offstyle="danger"
                                onchange="toggleOnOff()"/> <label style="color: black">Seleccionar Todo</label>
                            </td>
                        </tr>
                    </thead>
                </table>
                <br>
                <div class="form-row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn mr-sm-4 mt-3" href="{{route('instructor-curso', ['id' => $idins])}}">Regresar</a>
                        </div>
                        <div class="pull-right">
                            <input type="submit" value="Agregar" class="btn mr-sm-4 mt-3 btn-danger">
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idins }}">
            <input type="hidden" name="idespec" id="idespec">
        </div>
    </form>
@stop
@section('script_content_js')
    <script src="{{ asset("js/scripts/bootstrap-toggle.js") }}"></script>
    <script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleOnOff() {
            var checkBox = document.getElementById("ckbCheckAll");
            if (checkBox.checked == true){
                $('.checkBoxClass').prop('checked', true).change()
            } else {
                $('.checkBoxClass').prop('checked', false).change()
            }
        }

        function cursos() {
            var valor = document.getElementById("especialidad").value;
            var datos = {
                            idins: document.getElementById("idInstructor").value,
                            valor: valor
                        };
            var url = '/instructores/busqueda/nomesp';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });
            request.done(( respuesta) =>
            {
                // console.log(respuesta);
                const span = document.getElementById('tituloespecialidad');
                span.textContent = respuesta['nombre'];
            });

            var url = '/instructores/busqueda/cursos';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                var table = document.getElementById('tablecursos')
                var row = table.rows.length;
                if(row == 1)
                {
                    var body = table.createTBody();
                }
                else
                {
                    var body = table.tBodies['0'];
                    for (var i = 1; i < row; i++)
                    {
                        table.deleteRow(1);
                    }
                }
                // console.log(respuesta);
                respuesta.forEach(function(valor)
                {
                    var row = body.insertRow(0);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    cell1.innerHTML = valor['nombre_curso'];
                    cell2.innerHTML = 'MINIMO ' + valor['rango_criterio_pago_minimo'] + ' - ' + 'MÁXIMO ' + valor['rango_criterio_pago_maximo'];
                    cell3.innerHTML = valor['tipo_curso'];
                    cell4.innerHTML = valor['btn'];
                    document.getElementById('idespec').value = document.getElementById("especialidad").value;
                    $('#tgl' + valor['id']).bootstrapToggle();
                });
            });
        }
    </script>
@endsection
