@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Manuales | SIVyC Icatech')
@section('content')
<div class="container">
    <h2>Manuales de Usuario</h2>
    <p><strong>Los manuales de usuario estan divididos por modulo para una busqueda facil.</strong></p>
    <div class="panel-group well" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading" id="xl">
                <h4 class="panel-title">
                    <button class="btn btn-info btn-block" data-toggle="collapse"  data-target="#collapse1">Manual Para modulo Alumnos</button>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in" data-parent="#accordion">
                <li class="g-brd-around g-brd-gray-light-v4 g-brd-left-3 g-brd-primary-left g-rounded-3 g-pa-20 g-mb-7">
                    <div class="d-flex justify-content-start">
                        <h5 class="g-font-weight-600 g-color-black">Manual de Usuario Para el Modulo Alumnos</h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <ul class="u-list-inline"></ul>
                        <div class="align-self-center">
                            <a class="" href="{{asset("manuales/manual-alumnos.pdf")}}" target="_blank"><img src="{{asset("img/blade_icons/2.png")}}"alt=""></a>
                        </div>
                    </div>
                </li>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                <button class="btn btn-info btn-block" data-toggle="collapse" data-target="#collapse2">Manual para modulo Cursos</button>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse" data-parent="#accordion">
                <li class="g-brd-around g-brd-gray-light-v4 g-brd-left-3 g-brd-primary-left g-rounded-3 g-pa-20 g-mb-7">
                    <div class="d-flex justify-content-start">
                        <h5 class="g-font-weight-600 g-color-black">Manual de Usuario Para el Modulo Cursos</h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <ul class="u-list-inline"></ul>
                        <div class="align-self-center">
                            <a class="" href="{{asset("manuales/manual-cursos.pdf")}}" target="_blank"><img src="{{asset("img/blade_icons/2.png")}}"alt=""></a>
                        </div>
                    </div>
                </li>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                <button class="btn btn-info btn-block" data-toggle="collapse" data-target="#collapse3">Manual para modulo Instructores</button>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse" data-parent="#accordion">
                <li class="g-brd-around g-brd-gray-light-v4 g-brd-left-3 g-brd-primary-left g-rounded-3 g-pa-20 g-mb-7">
                    <div class="d-flex justify-content-start">
                        <h5 class="g-font-weight-600 g-color-black">Manual de Usuario Para el modulo Instructores</h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <ul class="u-list-inline"></ul>
                        <div class="align-self-center">
                            <a class="" href="{{asset("manuales/manual-instructores.pdf")}}" target="_blank"><img src="{{asset("img/blade_icons/2.png")}}"alt=""></a>
                        </div>
                    </div>
                </li>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                <button class="btn btn-info btn-block" data-toggle="collapse" data-target="#collapse4">Manual para Jefe de Vinculación</button>
                </h4>
            </div>
            <div id="collapse4" class="panel-collapse collapse" data-parent="#accordion">
                <li class="g-brd-around g-brd-gray-light-v4 g-brd-left-3 g-brd-primary-left g-rounded-3 g-pa-20 g-mb-7">
                    <div class="d-flex justify-content-start">
                        <h5 class="g-font-weight-600 g-color-black">Manual de Usuario para Jefe de Vinculación</h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <ul class="u-list-inline"></ul>
                        <div class="align-self-center">
                            <a class="" href="{{asset("manuales/manual-jefevinculacion.pdf")}}" target="_blank"><img src="{{asset("img/blade_icons/2.png")}}"alt=""></a>
                        </div>
                    </div>
                </li>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                <button class="btn btn-info btn-block" data-toggle="collapse" data-target="#collapse5">Manual de Usuario Vinculador</button>
                </h4>
            </div>
            <div id="collapse5" class="panel-collapse collapse" data-parent="#accordion">
                <li class="g-brd-around g-brd-gray-light-v4 g-brd-left-3 g-brd-primary-left g-rounded-3 g-pa-20 g-mb-7">
                    <div class="d-flex justify-content-start">
                        <h5 class="g-font-weight-600 g-color-black">Manual de Usuario Para el modulo Vinculador</h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <ul class="u-list-inline"></ul>
                        <div class="align-self-center">
                            <a class="" href="{{asset("manuales/manual-vinculacion.pdf")}}" target="_blank"><img src="{{asset("img/blade_icons/2.png")}}"alt=""></a>
                        </div>
                    </div>
                </li>
            </div>
        </div>
    </div>
</div>
@endsection
