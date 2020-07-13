<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Inicio | Sivyc Icatech')

@section('content')
<div class="container g-pt-50">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="row row-cols-1 row-cols-md-3">
              <div class="col mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-md-5" style="background: #ffffff;">
                            <img src="{{asset("img/blade_icons/users.png")}}" class="card-img-top h-80" alt="Perfil Usuario">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title">{{ Auth::user()->name }}</h5>
                                <p class="card-text">Alice is a freelance web designer and developer based in London. She is specialized in HTML5, CSS3, JavaScript, Bootstrap, etc.</p>
                                <a href="#" class="btn btn-primary stretched-link">Ver perfil</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-md-5" style="background: #ffffff;">
                            <img src="{{asset("img/blade_icons/talk.png")}}" class="card-img-top h-80" alt="Notificaciones">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title">Notificaciones</h5>
                                <p class="card-text">Alice is a freelance web designer and developer based in London. She is specialized in HTML5, CSS3, JavaScript, Bootstrap, etc.</p>
                                <a href="#" class="btn btn-primary stretched-link">Notificaciones</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-md-5" style="background: #ffffff;">
                            <img src="{{asset("img/blade_icons/catalog.png")}}" class="card-img-top h-80" alt="Catalogos">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title">Catalogos</h5>
                                <p class="card-text">Alice is a freelance web designer and developer based in London. She is specialized in HTML5, CSS3, JavaScript, Bootstrap, etc.</p>
                                <a href="#" class="btn btn-primary stretched-link">Catalogos</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-md-5" style="background: #ffffff;">
                            <img src="{{asset("img/blade_icons/catalog.png")}}" class="card-img-top h-80" alt="Catalogos">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title">Alumnos</h5>
                                <p class="card-text">Alice is a freelance web designer and developer based in London. She is specialized in HTML5, CSS3, JavaScript, Bootstrap, etc.</p>
                                <a href="/alumnos/indice" class="btn btn-primary stretched-link">Alumnos</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-md-5" style="background: #ffffff;">
                            <img src="{{asset("img/blade_icons/catalog.png")}}" class="card-img-top h-80" alt="Catalogos">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h5 class="card-title">Manuales</h5>
                                <p class="card-text">Aprenda a manejar el modulo que le han asignado de manera eficiente con el sencillo manual que explica paso a paso el manejo del sistema.</p>
                                <a href="/user/manuales" class="btn btn-primary stretched-link">Manuales</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <br>
</div>
@endsection
