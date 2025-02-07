@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-4" style="margin-top: 50px;">
                <div class="text-center">
                    <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail"
                        alt="avatar">
                    <h6>Upload a different photo...</h6>
                    <input type="file" class="text-center center-block file-upload">
                </div>
                </hr><br>

                <div class="panel panel-default">
                    <div class="panel-heading">Sitio Web <i class="fa fa-link fa-1x"></i></div>
                    <div class="panel-body"><a href="https://github.com/sergio-gonzalez11">github.com/sergio-gonzalez11</a>
                    </div>
                </div>

                <ul class="list-group">
                    <li class="list-group-item text-muted">Actividad <i class="fa fa-dashboard fa-1x"></i></li>
                    <li class="list-group-item text-right"><span class="pull-left"><strong>Shares</strong></span> 125
                    </li>
                    <li class="list-group-item text-right"><span class="pull-left"><strong>Likes</strong></span> 13</li>
                </ul>
            </div>

            <div class="col-sm-8" style="margin-top: 50px;">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home">Perfil</a></li>
                </ul>


                <div class="tab-content">
                    <div class="tab-pane active" id="home">
                        <hr>
                        <form class="form" action="##" method="post" id="registrationForm">
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="nombre">
                                        <h4>Nombre</h4>
                                    </label>
                                    <input type="text" class="form-control" name="nombre" id="nombre"
                                        placeholder="nombre">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="last_name">
                                        <h4>Apellidos</h4>
                                    </label>
                                    <input type="text" class="form-control" name="apellidos" id="apellidos"
                                        placeholder="apellidos">
                                </div>
                            </div>

                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="teléfono">
                                        <h4>Teléfono</h4>
                                    </label>
                                    <input type="text" class="form-control" name="teléfono" id="teléfono"
                                        placeholder="teléfono">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="mobile">
                                        <h4>Movil</h4>
                                    </label>
                                    <input type="text" class="form-control" name="movil" id="movil"
                                        placeholder="introduce tu móvil">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="email">
                                        <h4>Email</h4>
                                    </label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="you@email.com">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="ciudad">
                                        <h4>Ciudad</h4>
                                    </label>
                                    <input type="text" class="form-control" id="ciudad" placeholder="ciudad">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="password">
                                        <h4>Contraseña</h4>
                                    </label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="contraseña">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="password2">
                                        <h4>Verifica la contraseña</h4>
                                    </label>
                                    <input type="password" class="form-control" name="password2" id="password2"
                                        placeholder="password2">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <br>
                                    <button class="btn btn-success" type="submit"><i
                                            class="glyphicon glyphicon-ok-sign"></i> Guardar</button>
                                    <button class="btn btn-danger" type="reset"><i
                                            class="glyphicon glyphicon-repeat"></i> Limpiar</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@endsection
