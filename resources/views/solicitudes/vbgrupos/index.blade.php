<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Solicitudes -VB Grupos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .form-check-input{
            width:22px;
            height:22px;
        }
        #result_body ul { padding: 5px; list-style-type: none}
        #result_body ul li { font-size: 12x; margin-bottom:10px; width: 232px;}
        #result_body ul li p{ font-size: 11px; margin-bottom:12px; display: block;  }
        #result_body ul li b { font-size: 14px; padding: 3px;}
        .modal-header p {font-size: 10px;}
        .fas { cursor: pointer; color: red;}

        /* Made By Jose Luis */
        /* Reescribir algunos estilos del select2 */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 15px !important; /* Cambia el valor según lo que necesites */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b::before {
            content: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important; /* o usa el valor que quieras */
        }

        .btn-outline-success{
            color: #0f9986 !important;
            border: 2px solid #0f9986 !important;
        }
        /* #009885 */
        .btn-outline-success:hover {
            color: white !important;
            background-color: #009885 !important;
            border-color: #009885 !important;
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
            0% {transform: translate(-50%, -50%) rotate(0deg);}
            100% {transform: translate(-50%, -50%) rotate(360deg);}
        }

        #loader-text {
            color: #fff;
            margin-top: 150px;
            text-align: center;
            font-size: 20px;
        }

        /* Texto loader */
        #loader-text span {
            opacity: 0; /* Inicia los puntos como invisibles */
            font-size: 30px;
            font-weight: bold;
            animation: fadeIn 1s infinite; /* Aplica la animación de aparecer */
        }

        @keyframes fadeIn {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        #loader-text span:nth-child(1) {animation-delay: 0.5s; }
        #loader-text span:nth-child(2) {animation-delay: 1s; }
        #loader-text span:nth-child(3) {animation-delay: 1.5s;}

    </style>
@endsection
@section('content')
    <div id="loader-overlay">
        <div id="loader"></div>
        <div id="loader-text">
            Espere un momento mientras se realiza la consulta .<span> . </span><span> . </span><span> . </span>
        </div>
    </div>
    <div class="card-header">
        Solicitudes / V.B. de Grupos de Capacitación
    </div>
    <div class="card card-body pt-4">
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        {{-- Made by Jose Luis --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('error') }}</strong>

            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('success') }}</strong>
            </div>
        @endif
        <div id="mensajeInstructor" style="display: none;">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="close" onclick="$('#mensajeInstructor').hide();">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong id="textoMensajeInstructor"></strong>
            </div>
        </div>
        <?php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;
        ?>
        <div class="row d-flex justify-content-end mt-2">
            <a href="{{ route('consultas.pagos') }}" target="_blank" title="Ver Pagos" class="btn btn-sm btn-outline-success">VER PAGOS
                <i class="fa fa-credit-card ml-1" aria-hidden="true"></i>
            </a>
        </div>
        {{ Form::open(['route' => 'solicitudes.vb.grupos', 'method' => 'post', 'id'=>'frm']) }}
            <div class="row form-inline">
                <div class="d-flex flex-lg-row flex-column col-12 col-md-6 col-sm-12 justify-content-left">
                    {{ Form::text('clave', $clave ?? '', ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CURSO / INSTRUCTOR / UNIDAD', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 60]) }}
                </div>
                <div class="d-flex flex-lg-row flex-column col-12 col-md-6 col-sm-12 justify-content-end pr-4">
                    @foreach ($estatus as $key => $value)
                        <div class="form-check d-flex mt-2">
                            <input type="radio" class="form-check-input col-md-6" name="estatus" id="estatus{{ $key }}" value="{{ $key }}" {{ $key == $status ? 'checked' : '' }}>
                            <label class="form-check-label col-md-6 mt-1" for="estatus{{ $key }}">
                                {{ $value }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                @include('solicitudes.vbgrupos.table')
            </div>

            {{-- Modal DATOS --}}
            <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">
                    <div class="modal-content text-center">
                        <!--Header-->
                        <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);" >
                            <p class="heading font-weight-bold" id="result_head">DATOS</p>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>
                        </div>
                        <!--Body-->
                        <div class="modal-body">
                            <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert"  id="result_body" ></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FIN Modal DATOS --}}

            {{-- Modal RECHAZAR --}}
            <div class="modal fade" id="modalMotivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">
                    <div class="modal-content text-center">
                        <!--Header-->
                        <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);" >
                            <p class="heading font-weight-bold">Curso: <block id="body_motivo"> </block></p>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>
                        </div>
                        <!--Body-->
                        <div class="modal_body">
                            <div class="alert alert-dismissible fade show p-2 text-left" role="alert">
                                {{ Form::hidden('id_curso', '',['id'=>'id_curso']) }}
                                <h6>MOTIVO:</h6>
                                {{ Form::textarea('motivo',old('motivo'), ['id'=>'motivo', 'class' => 'form-control mt-2', 'placeholder' => 'Describir el motivo del rechazo.', 'rows' => 3]) }}
                                <div class="modal-footer flex-center">
                                    <button class="btn btn-danger" id="rechazar" onclick="rechazar()">RECHAZAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FIN Modal RECHAZAR --}}

        {!! Form::close() !!}

        {{-- Modal elegir instructor --}}
        <div class="modal fade" id="modalElegirInstruc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-notify modal-danger" id="" role="document">
                <div class="modal-content text-center">
                    <!--Header-->
                    <div class="modal-header d-flex justify-content-center" style="background-color:rgb(201, 1, 102);">
                        <p class="heading font-weight-bold">SELECCIONAR INSTRUCTOR</p>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="text-light">&times;</span></button>
                    </div>
                    <!--Body-->
                    <form action="{{route('solicitudes.vb.intruc.save')}}" method="post">
                        @csrf

                        <div class="modal-body">
                            {{-- <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert"  id="result_instructor" ></div> --}}
                            <input type="hidden" name="val_folio_grupo" id="val_folio_grupo">
                            <select name="sel_instructor" id="sel_instructor" class="form-control sel_instructor">
                                <option value="0">Seleccionar Instructor</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-outline-success">ASIGNAR Y AUTORIZAR</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
        {{-- FIN Modal DATOS --}}
    </div>
    @section('script_content_js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script language="javascript">
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
            function cambia_estado(id, status){
                estado = status.prop('checked');
                $.ajax({
                        method: "POST",
                        url: "vbgrupos/vistobueno",
                        data: {
                            id: id,
                            estado: estado
                        }
                })
                .done(function( msg ) { alert(msg); });
            }

            function actualiza_data(){
                var clave = $('#clave').val();
                var estatus = $('input[name="estatus"]:checked').val();
                if (clave.length >= 3 || clave.length ==0 || estatus.length>0){
                    $.ajax({
                        url: "vbgrupos/buscar",
                        method: 'POST',
                        data: {
                            clave: clave,
                            estatus: estatus
                        },
                        success: function(data) {
                            $('#result_table').html(data);
                        }
                    });
                }
            }

            $(document).ready(function(){
                $('#clave').on('keyup', function() { actualiza_data(); });
                $('input[name="estatus"]').on('click', function() {
                    actualiza_data();
                });

                $("#rechazar" ).click(function(){
                    if(confirm("Esta seguro de GUARDAR?")==true){
                        $('#frm').attr('target', '_self');
                        $('#frm').attr('action', "{{route('solicitudes.vb.grupos.rechazar')}}"); $('#frm').submit();
                    }
                });

            });

            function ver_modal(tipo, folio_grupo){
                $('#result_head').html("");
                $('#result_body').html("");
                if (folio_grupo.length >0){
                    $.ajax({
                        url: "vbgrupos/getinfo",
                        method: 'POST',
                        data: {
                            tipo: tipo,
                            folio_grupo : folio_grupo
                        },
                        success: function(data) {
                            $('#result_head').html(data[0]);
                            $('#result_body').html(data[1]);
                            $("#modalDetalles").modal("show");
                        }
                    });

                }
            }

            function modal_motivo(curso,id_curso){
                $('#id_curso').val(id_curso);
                $('#body_motivo').html(curso);
                $("#modalMotivo").modal("show");
            }

            // Made by Jose Luis
            function seleccion_instructor(folio_grupo){
                loader('show');
                if (folio_grupo.length >0){
                    $.ajax({
                        url: "vbgrupos/getinstruc",
                        method: 'POST',
                        data: {
                            folio_grupo : folio_grupo
                        },
                        success: function(data) {
                            loader('hide');
                            // console.log(data);

                            // Limpiar mensaje previo
                            $('#mensajeInstructor').hide();
                            $('#textoMensajeInstructor').text('');
                            if (data.status === 200) {

                                let options = '<option value="0">Seleccionar Instructor</option>'; // Dejar siempre el primer option

                                data.instructores.forEach(function(item) {
                                    options += `<option value="${item.id}">${item.instructor} / ${item.unidad} / ${item.telefono} </option>`;
                                });
                                $(".sel_instructor").html(options);

                                // Inicializar o reinicializar Select2
                                $(".sel_instructor").select2({
                                    dropdownParent: $('#modalElegirInstruc'), // Importante si el select está en un modal
                                    width: '100%',
                                    placeholder: "Seleccionar Instructor"
                                });

                                $("#val_folio_grupo").val(folio_grupo);
                                $("#modalElegirInstruc").modal("show");
                            }else{
                                // if (data.instructores.length === 0) {
                                $('#textoMensajeInstructor').text(data.mensaje);
                                $('#mensajeInstructor').show();
                                return;
                                // }
                            }
                        }
                    });

                }
            }

            function loader(make) {
                if(make == 'hide') make = 'none';
                if(make == 'show') make = 'block';
                document.getElementById('loader-overlay').style.display = make;
            }
        </script>
    @endsection
@endsection
