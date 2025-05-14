<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Incorporación Laboral | SIVyC Icatech')
@section("content_script_css")
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
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
@endsection
@section('content')
    <div class="card-header">Consultas / Incorporación Laboral de Egresados</div>        
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>
    <div class="card card-body" >        
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            {{-- Filtrados --}}
            <form action="" class="form-inline" method="get" id="frmBolsaTrabajo">
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
                
                    <button class="btn" id="btnBuscar">BUSCAR</button>

                    <button class="btn btn-info" id="btnLimpiar">LIMPIAR</button>

                    <button class="btn btn-warning text-dark" id="btnReporte"> <i class="far fa-file-excel fa-lg text-dark ml-1"></i> EXPORTAR</button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="mt-5">
                @if (count($results) > 0)
                    <h5 class="float-right">Total de registros: <span class="badge badge-dark">{{ $total_reg }}</span></h5>                    
                    <table class="table table-bordered table-striped" id='tableperfiles'>
                        <thead>
                            <tr>
                                <th class="text-center" scope="col">No</th>
                                <th class="text-center" scope="col">NOMBRE</th>
                                <th class="text-center" scope="col">EDAD</th>
                                <th class="text-center" scope="col">MUNICIPIO</th>
                                <th class="text-center" scope="col">NACIONALIDAD</th>
                                <th class="text-center" scope="col">SEXO</th>
                                <th class="text-center" scope="col">CIUDAD</th>
                                <th class="text-center" scope="col">DOMICILIO</th>
                                <th class="text-center" scope="col">EDO.CIVIL</th>
                                <th class="text-center" scope="col">ESCOLARIDAD</th>
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
