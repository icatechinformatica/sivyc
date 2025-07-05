<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Aperturas | SIVyC Icatech')
@section('content_script_css')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
    Solicitudes / Aperturas ARC01 y ARC02
</div>
<div class="card card-body" style=" min-height:450px;">
     @if ($message)
    <div class="row ">
        <div class="col-md-12 alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    </div>
    @endif

    {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
    @csrf

    <div class="row">
        <div class="form-group col-md-2 mt-1">
            {{ Form::select('opt', ['ARC01'=>'ARC01','ARC02'=>'ARC02'], $opt, ['id'=>'opt','class' => 'form-control mr-sm-2'] ) }}
        </div>
        <div class="form-group col-md-3 mt-1">
            {{ Form::text('memo', $memo, ['id'=>'memo', 'class' => 'form-control', 'placeholder' => 'MEMORÁNDUM ARC', 'aria-label' => 'MEMORÁNDUM ARC', 'required' => 'required', 'size' => 25]) }}
        </div>
        @if($grupos[0]->fecha_arc01??null AND $grupos[0]->status_curso??null !='VALIDADO')
            <div class="form-group col-md-2 d-flex">
                {{ form::date('fecha_arc01', $grupos[0]->fecha_arc01??null, ['id'=>'fecha_arc01', 'class'=>'form-control']) }}
                <a onclick="guardar_fecha('{{ $grupos[0]->munidad??null }}')" title="Guardar Fecha"><i class="fas fa-save fa-lg m-2 " aria-hidden="true" style="color:rgb(165, 2, 2);"></i></a>
            </div>
        @endif
        <div class="form-group col-md-2 mr-sm-1">
            {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
        </div>
        @if(count($grupos)>0)
            @php
                if($movimientos)$activar = true;
                else $activar = false;
                $munidad = $grupos[0]->munidad;
                $nmunidad = $grupos[0]->nmunidad;
                $status_curso = $grupos[0]->status_curso;
                $pdf_curso = $grupos[0]->pdf_curso;
            @endphp
                <div class="form-group col-md-2 mr-sm-1">
                    <div class="dropdown show">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum">&nbsp;SOLICITUD</i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{$file}}" target="_blank">
                                {{ $grupos[0]->munidad."PDF" }}
                            </a>
                        </div>
                    </div>
                </div>

            @if (($opt== "ARC01" AND $status_solicitud != "VALIDADO") OR ($opt== "ARC02" AND $status_solicitud != "VALIDADO"))
            <div class="form-group col-md-2 mr-sm-1">
                @if ($opt== "ARC01")
                {{ Form::button('ARC-01 BORRADOR', ['id'=>'BorradorARC','class' => 'btn']) }}
                @else
                {{ Form::button('ARC-02 BORRADOR', ['id'=>'BorradorARC','class' => 'btn']) }}
                @endif
            </div>
            @endif
            @if($grupos[0]->pdf_curso AND $activar == false)
                <div class="form-group col-md-2">
                    <a href="{{ $grupos[0]->pdf_curso }}" target="_blank" class="btn bg-warning">PDF AUTORIZACIÓN</a>
                </div>
            @endif
        @endif
        {!! Form::hidden('fecha', date('Y-m-d')) !!}
    </div>


    @if(count($grupos)>0)
        <hr />
        <h4><b>GRUPOS</b></h4>
        @include('solicitudes.aperturas.table')
    @endif

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
                <div class="modal-body">
                    <p class="text-center"><span class="font-weight-bold">Total de Instructores: </span> <span id="idTotal"></span></p>
                    {{-- <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert"  id="result_instructor" ></div> --}}
                    <input type="hidden" name="val_folio_grupo" id="val_folio_grupo">
                    <select name="sel_instructor" id="sel_instructor" class="form-control sel_instructor">
                        <option value="0">Ver Instructores</option>
                    </select>
                </div>
                <br>
            </div>
        </div>
    </div>
    {{-- FIN Modal DATOS --}}

</div>
@section('script_content_js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script language="javascript">
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
    });

    function guardar_fecha(memo){
        if (confirm("Está seguro de guardar cambios, en la fecha del ARC-01?") == true) {
            var fecha = $("#fecha_arc01").val();
            $.ajax({
                        url: "aperturas/guardar_fecha",
                        method: 'POST',
                        data: {
                            memo: memo,
                            fecha: fecha
                        },
                        success: function(data) {
                        //$('#result_table').html(data);
                        alert(data);
                    }
            });
        }
    }
    $(document).ready(function() {
        //MOSTRAR BOTONES CONFORME AL MOVIMIENTO
        $("#mrespuesta").hide();
        $("#fecha").hide();
        $("#file").hide();
        $("#observaciones").hide();
        $("#movimiento").change(function() {
            switch ($("#movimiento").val()) {
                case "CAMBIAR":
                    $("#mrespuesta").show();
                    $("#fecha").show();
                break;
                case "EN FIRMA":
                    $("#mrespuesta").show();
                    $("#fecha").show();
                break;
                case "AUTORIZADO":
                    $("#file").show();
                break;
                case "CANCELADO":
                    $("#file").show();
                break;
                case "DENEGADO"://DENEGADO cambio de soporte de pago
                    $("#observaciones").show();
                break;
            }
        });


        $("#buscar").click(function() {
            $('#frm').attr('action', "{{route('solicitudes.aperturas')}}");
            $('#frm').attr('target', '_self').submit();
        });
        $("#BorradorARC").click(function() {
            if ($("#opt").val() == "ARC01") {
                $('#frm').attr('action', "{{route('solicitud.generar.arc01')}}");
                $('#frm').attr('target', '_blank').submit();
            } else if ($("#opt").val() == "ARC02") {
                $('#frm').attr('action', "{{route('solicitud.generar.arc02')}}");
                $('#frm').attr('target', '_blank').submit();
            }
        });
        $("#aceptar").click(function() { // alert($("#movimiento").val());
            if (confirm("Esta seguro de ejecutar la acción?") == true) {
                switch ($("#movimiento").val()) {
                    case "PRETORNADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pretornar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "VALIDADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pvalidar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "EDICION":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pvalidar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "VoBo":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pvalidar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "RETORNADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.retornar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "EN FIRMA":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.asignar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "DESHACER":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.deshacer')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "CAMBIAR":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.cambiarmemo')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "AUTORIZADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.autorizar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "CANCELADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.autorizar')}}");
                        $('#frm').attr('target', '_self').submit();
                    break;
                    case "ACEPTADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.soporte_pago')}}");
                        $('#frm').attr('target', '_self').submit();
                    break;
                    case "DENEGADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.soporte_pago')}}");
                        $('#frm').attr('target', '_self').submit();
                    break;
                    case "ACEPTADO ARC":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.soporte_pago')}}");
                        $('#frm').attr('target', '_self').submit();
                    break;
                    case "DENEGADO ARC":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.soporte_pago')}}");
                        $('#frm').attr('target', '_self').submit();
                    break;
                    default:
                        alert("POR FAVOR SELECCIONE UN MOVIMIENTO.")
                    break;
                }
            }
        });
        $("#generar").click(function() {
            $('#frm').attr('action', "{{route('solicitudes.generar.autoriza')}}");
            $('#frm').attr('target', '_blank').submit();
        });
    });

    // Made by Jose Luis
    function seleccion_instructor(folio_grupo){
        loader('show');
        if (folio_grupo.length >0){
            $.ajax({
                url: "/solicitudes/aperturas/getinstruc",
                method: 'POST',
                data: {
                    folio_grupo : folio_grupo
                },
                success: function(data) {
                    loader('hide');
                    // console.log(data);

                    if (data.status === 200) {
                        let options = '<option value="0">Ver Instructores</option>'; // Dejar siempre el primer option

                        data.instructores.forEach(function(item) {
                            options += `<option value="${item.id}">${item.instructor} / ${item.unidad} / ${item.telefono} / ${item.fecha_validacion} </option>`;
                        });
                        $(".sel_instructor").html(options);

                        // Inicializar o reinicializar Select2
                        $(".sel_instructor").select2({
                            dropdownParent: $('#modalElegirInstruc'), // Importante si el select está en un modal
                            width: '100%',
                            placeholder: "Seleccionar Instructor"
                        });

                        $("#val_folio_grupo").val(folio_grupo);
                        $('#idTotal').text(data.totalInstruc);
                        $("#modalElegirInstruc").modal("show");
                    }else{
                        alert(data.mensaje);
                        return;
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
