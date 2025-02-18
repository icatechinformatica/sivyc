@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> --}}
    <style>
        .upload {
            width: 100px;
            position: relative;
            margin: auto;
        }

        .upload img {
            border-radius: 50%;
            border: 6px solid #eaeaea;
        }

        .upload .round {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #00B4FF;
            width: 33px;
            height: 33px;
            line-height: 33px;
            text-align: center;
            border-radius: 50%;
            overflow: hidden;
        }

        .upload .round input[type = "file"] {
            position: absolute;
            transform: scale(2);
            opacity: 0;
        }

        input[type=file]::-webkit-file-upload-button {
            cursor: pointer;
        }

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fondo semi-transparente */
            z-index: 9999;
            /* Asegura que esté por encima de otros elementos */
            display: none;
            /* Ocultar inicialmente */
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

        #loader-text {
            color: #fff;
            margin-top: 150px;
            text-align: center;
            font-size: 20px;
        }

        /* Texto loader */
        #loader-text span {
            opacity: 0;
            /* Inicia los puntos como invisibles */
            font-size: 30px;
            font-weight: bold;
            animation: fadeIn 1s infinite;
            /* Aplica la animación de aparecer */
        }

        @keyframes fadeIn {

            0%,
            100% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }
        }

        #loader-text span:nth-child(1) {
            animation-delay: 0.5s;
        }

        #loader-text span:nth-child(2) {
            animation-delay: 1s;
        }

        #loader-text span:nth-child(3) {
            animation-delay: 1.5s;
        }

        .btn-custom {
            background-color: #009B85;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background-color: #007A69;
        }
    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
    <div id="loader-overlay">
        <div id="loader"></div>
        <div id="loader-text">
            Espere un momento mientras se realiza el proceso<span> . </span><span> . </span><span> . </span>
        </div>
    </div>
    <div class="card-header">
       <a href="{{ route('credencial.indice') }}" style="color: white !important;"> Generación Código QR </a> / Perfil
    </div>
    <div class="card card-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="container">

                    <div class="row">
                        <div class="col-sm-4" style="margin-top: 50px;">
                            <div class="upload">
                                <img src="{{ $avatar ? $avatar : 'http://ssl.gstatic.com/accounts/ui/avatar_2x.png' }}"
                                    width=140 height=140 alt="" id="avatarImage">
                                <div class="round">
                                    <form method="post" style="display: none" id="avatarForm"
                                        action="{{ route('credencial.uploadphoto') }}">
                                        @csrf
                                        <input type="file" id="avatarInput" name="photo">
                                        <input type="hidden" name="curp" id="curp"
                                            value="{{ $perfil->curp_usuario }}">
                                    </form>
                                    <i class = "fa fa-camera" style = "color: #fff;"></i>
                                </div>
                            </div>
                            </hr><br>

                            <div class="d-flex justify-content-center">
                                <img style="max-width: 100%;" src="data:image/png;base64,{{ $qrCodeBase64 }}"
                                    alt="Código QR">
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="data:image/png;base64,{{ $qrCodeBase64 }}"
                                    download="{{ $perfil->curp_usuario }}_{{ $perfil->clave_empleado }}.png"
                                    class="btn btn-custom mt-3">Descargar <i class="fas fa-qrcode"></i></a>
                            </div>
                        </div>

                        <div class="col-sm-8" style="margin-top: 50px;">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home">Perfil</a></li>
                            </ul>


                            <div class="tab-content">
                                <div class="tab-pane active" id="home">
                                    <hr>
                                    <form class="form" action="##" method="post" id="registrationForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="nombre">
                                                    <h4>Nombre</h4>
                                                </label>
                                                <input type="text" class="form-control" name="nombre" id="nombre"
                                                    placeholder="nombre" value="{{ $perfil->nombre_trabajador }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="movil">
                                                    <h4>Número de Enlace</h4>
                                                </label>
                                                <input type="text" class="form-control" name="movil" id="movil"
                                                    placeholder="número de enlace" value="{{ $perfil->clave_empleado }}"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="telefono">
                                                    <h4>Puesto</h4>
                                                </label>
                                                <input type="text" class="form-control" name="telefono" id="telefono"
                                                    placeholder="teléfono" value="{{ $perfil->puesto_estatal }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="movil">
                                                    <h4>Fecha de Ingreso</h4>
                                                </label>
                                                <input type="text" class="form-control" name="movil" id="movil"
                                                    placeholder="Fecha Ingreso" value="{{ $perfil->fecha_ingreso }}"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="adscripcion">
                                                    <h4>Adscripción</h4>
                                                </label>
                                                <input type="text" class="form-control" name="adscripcion"
                                                    placeholder="ADSCRIPCIÓN" value="{{ $perfil->nombre_adscripcion }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ciudad">
                                                    <h4>Estado del Empleado</h4>
                                                </label>
                                                <input type="text" class="form-control" id="ciudad"
                                                    placeholder="estado"
                                                    value="{{ $perfil->status ? 'ACTIVO' : 'INACTIVO' }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="comision">
                                                    <h4>Comisionado (si es el caso)</h4>
                                                </label>
                                                <input type="text" class="form-control" name="comision"
                                                    value="{{ $perfil->comision_direccion_o_unidad }}" readonly>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(function() {
            let $avatarImage, $avatarInput, $avatarForm;

            $avatarImage = $('#avatarImage');
            $avatarInput = $('#avatarInput');
            $avatarForm = $('#avatarForm');

            $avatarImage.on('click', function() {
                $avatarInput.click();
            });

            $avatarInput.on('change', function() {

                const URL = $avatarForm.attr('action');

                let formData = new FormData();
                formData.append('photo', $avatarInput[0].files[0]);
                formData.append('curp', $('#curp').val());
                $.ajax({
                    url: URL,
                    method: $avatarForm.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        document.getElementById('loader-overlay').style.display = 'block';
                        console.log('cargando...')
                    },
                    success: function(response) {
                        setTimeout(function() {
                            // Ocultar el loader y mostrar el contenido después de la carga
                            document.getElementById('loader-overlay').style.display =
                                'none';
                            if (response.data.result === true) {
                                window.location.href =
                                    "{{ route('credencial.ver', ['id' => $id]) }}"; // redirect
                            }
                        }, 2300);
                    },
                    error: function(xhr, textStatus, error) {
                        // manejar errores
                        console.log('ESTADO TEXTO: ' + xhr.statusText);
                        console.log('RESPUESTA:  ' + xhr.responseText);
                        console.log('ESTADO: ' + xhr.status);
                        console.log(textStatus);
                        console.log(error);
                    }
                }).done(function(data) {
                    if (data.success) {
                        $avatarImage.attr('src', data.path);
                    } else {
                        console.log(data);
                    }
                }).fail(function() {
                    alert('La imagen subida no tiene un formato correcto');
                });;
            });
        });
    </script>
@endsection
