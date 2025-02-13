@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> --}}
    <style>
        .perfil {
            border-radius: 50%;
            width: 100px;
            /* Ajusta según el tamaño deseado */
            height: 100px;
            /* Ajusta según el tamaño deseado */
            object-fit: cover;
        }

    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
    <div class="card card-body">
        <div class="row">
            <div class="form-group col-md-12">
                <div class="container">

                    <div class="row">
                        <div class="col-sm-4" style="margin-top: 50px;">
                            <div class="text-center">
                                <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="perfil" alt="avatar">
                                <h6>Agregar Imagen...</h6>
                                <input type="file" class="text-center center-block file-upload">
                            </div>
                            </hr><br>

                            <div class="d-flex justify-content-center">
                                <img style="max-width: 100%;" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="data:image/png;base64,{{ $qrCodeBase64 }}" download="codigo_qr.png" class="btn btn-warning mt-3">Descargar <i class="fas fa-qrcode"></i></a>
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
                                                    placeholder="número de enlace" value="{{ $perfil->clave_empleado }}" readonly>
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
                                                    placeholder="Fecha Ingreso" value="{{ $perfil->fecha_ingreso }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="adscripcion">
                                                    <h4>Adscripción</h4>
                                                </label>
                                                <input type="text" class="form-control" name="adscripcion"
                                                    placeholder="ADSCRIPCIÓN" value="{{ $perfil->nombre_adscripcion }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ciudad">
                                                    <h4>Estado del Empleado</h4>
                                                </label>
                                                <input type="text" class="form-control" id="ciudad"
                                                    placeholder="estado" value="{{ ($perfil->status) ? "ACTIVO" : "INACTIVO" }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="comision">
                                                    <h4>Comisionado (si es el caso)</h4>
                                                </label>
                                                <input type="text" class="form-control" name="comision"
                                                    placeholder="ADSCRIPCIÓN" value="{{ $perfil->comision_direccion_o_unidad }}" readonly>
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@endsection
