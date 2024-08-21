<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Bolsa de Trabajo | SIVyC Icatech')

    <!--seccion-->

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }

        .card-header{
                font-variant: small-caps;
                background-color: #621132;
                color: white;
                margin: 1.7% 1.7% 1% 1.7%;
                padding: 1.3% 39px 1.3% 39px;
                font-style: normal;
                font-size: 22px;
            }

            .card-body{
                margin: 1%;
                margin-left: 1.7%;
                margin-right: 1.7%;
                /* padding: 55px; */
                -webkit-box-shadow: 0 8px 6px -6px #999;
                -moz-box-shadow: 0 8px 6px -6px #999;
                box-shadow: 0 8px 6px -6px #999;
            }
            .card-body.card-msg{
                background-color: yellow;
                margin: .5% 1.7% .5% 1.7%;
                padding: .5% 5px .5% 25px;
            }

            body { background-color: #E6E6E6; }

            .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
            .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

            .form-control { height: 40px; }


            #text_buscar_curso {
                height: fit-content;
                width: auto;
            }


            /* Estilo del loader */
            #loader-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
                z-index: 9999; /* Asegura que esté por encima de otros elementos */
                display: none; /* Ocultar inicialmente */
            }

            #loader {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 60px;
                height: 60px;
                border: 6px solid #fff;
                border-top: 6px solid #621132;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }
                100% {
                    transform: translate(-50%, -50%) rotate(360deg);
                }
            }


    </style>

    <div class="card-header py-2">
        <h3>Bolsa de trabajo</h3>
    </div>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    {{-- card para el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div class="container-fluid">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            {{-- Filtrados --}}
            <form action="" class="form-inline mt-4" method="get" id="frmBolsaTrabajo">
                <div class="col-12">
                    <input type="text" id="text_buscar_curso" class="form-control text_buscar_curso"
                    name="text_buscar_curso" placeholder="CURSO" style="width:28%" value="{{ $textcurso ?? '' }}">

                    <select id="sel_nacionalidad" name="sel_nacionalidad" class="form-control mr-2">
                        <option value="">NACIONALIDAD</option>
                        <option value="MEXICANA" @if($nacionalidad == 'MEXICANA') selected @endif>MEXICANA</option>
                        <option value="EXTRANJERA" @if($nacionalidad == 'EXTRANJERA') selected @endif>EXTRANJERA</option>
                    </select>

                    <input type="date" class="form-control datepicker mr-2" name="fechaIniV" id="fechaIniV"
                    placeholder="FECHA DE INICIO" style="width: 15%;" value="{{ $fecha_inicio ?? ''}}">

                    <input type="date" class="form-control datepicker" name="fechaFinV" id="fechaFinV"
                    placeholder="FECHA DE TERMINO" style="width: 15%;" value="{{ $fecha_fin ?? ''}}">
                </div>

                <div class="col-12 mt-3">
                    <button class="btn" id="btnBuscar">BUSCAR</button>

                    <button class="btn btn-info" id="btnLimpiar">LIMPIAR</button>

                    <button class="btn btn-warning text-dark" id="btnReporte"> <i class="far fa-file-excel fa-lg text-dark ml-1"></i> EXPORTAR REGISTROS</button>
                </div>
            </form>

            <hr style="border-color: #ddd; border-width: 2px; margin: 10px 0;">

            {{-- Tabla --}}
            <div class="mt-5">
                @if (count($results) > 0)
                    <h5 class="float-right">Total de registros: <span class="badge badge-dark">{{ $total_reg }}</span></h5>
                    <table class="table table-responsive table-hover" id='tableperfiles'>
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" scope="col">No</th>
                                <th class="text-center" scope="col">NOMBRE</th>
                                <th class="text-center" scope="col">EDAD</th>
                                <th class="text-center" scope="col">MUNICIPIO</th>
                                <th class="text-center" scope="col">NACIONALIDAD</th>
                                <th class="text-center" scope="col">SEXO</th>
                                <th class="text-center" scope="col">CIUDAD</th>
                                <th class="text-center" scope="col">DOMICILIO</th>
                                <th class="text-center" scope="col">ESTADO CIVIL</th>
                                <th class="text-center" scope="col">NIVEL EDUCATIVO</th>
                                <th class="text-center" scope="col">TELEFONO</th>
                                <th class="text-center" scope="col">CORREO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $key => $item)
                                <tr style="text-center">
                                    <td class="text-center">{{ $key + 1}}</td>
                                    <td class="text-center">{{ $item->alumno }}</td>
                                    <td class="text-center">{{ $item->edad }}</td>
                                    <td class="text-center">{{ $item->municipio}}</td>
                                    <td class="text-center">{{ $item->nacionalidad}}</td>
                                    <td class="text-center">{{ $item->sexo}}</td>
                                    <td class="text-center">{{ $item->colonia.', '.$item->municipio.', '.$item->estado}}</td>
                                    <td class="text-center">{{ $item->domicilio.', '.$item->colonia}}</td>
                                    <td class="text-center">{{ $item->estado_civil}}</td>
                                    <td class="text-center">{{ $item->ultimo_grado_est}}</td>
                                    <td class="text-center">{{ $item->telefono ?? 'SIN NUMERO' }}</td>
                                    <td class="text-center" style="width: 40px;">{{ $item->correo ?? 'SIN CORREO' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row py-4">
                        <div class="col d-flex justify-content-center">
                            {{$results->appends(request()->query())->links()}}
                        </div>
                    </div>
                @else
                    <div class="alert alert-success mt-5 mb-5" role="alert">
                        <h4 class="alert-heading">¡Sin resultados en la busqueda!</h4>
                    </div>
                @endif

            </div>
        </div>
    </div>
    {{-- termino del card --}}


    @section('script_content_js')

        <script language="javascript">
            $(document).ready(function(){

                $("#btnLimpiar").click(function(){
                    $("#text_buscar_curso").val("");
                    $("#sel_nacionalidad").val("");
                    $("#fechaIniV").val("");
                    $("#fechaFinV").val("");
                    $('#frmBolsaTrabajo').attr('action', "{{ route('consultas.bolsa.index')}}");
                    $("#frmBolsaTrabajo").attr("target", '_self');
                    $('#frmBolsaTrabajo').submit();
                });

                //Generar reporte excel
                $('#btnReporte').click(function() {
                    $('#frmBolsaTrabajo').attr('action', "{{ route('consulta.bolsa.reporte')}}");
                    $("#frmBolsaTrabajo").attr("target", '_self');
                    $('#frmBolsaTrabajo').submit();
                });
            });

            // paginacion
            $(document).on('click', '.pagination a', function(e) {
                loader('show');
            });

            function loader(make) {
                if(make == 'hide') make = 'none';
                if(make == 'show') make = 'block';
                document.getElementById('loader-overlay').style.display = make;
            }

            /*Funcion Ajax para realizar un autocompletado*/
           $( ".text_buscar_curso" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ route('consulta.bolsa.autocomp') }}",
                        method: 'POST',
                        dataType: "json",
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content"),
                            search: request.term,
                            // tipoCurso: $('#busqueda').val()
                        },
                        success: function( data ) {
                            response( data );
                            console.log(data);
                        }
                    });
                }
            });

        </script>
    @endsection
@endsection
