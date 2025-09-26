@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Error 405 Método No Permitido | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <div class="jumbotron">
            <h1 class="display-4">
                ¡Metodo No Permitido!
            </h1>
            <div style="width: 49.5%; display: inline-block;">
                <p class="lead">
                    <b>Ups!, El método utilizado no está permitido para este recurso.</b>
                </p>
            </div>
            <div style="width: 50%; display: inline-block; text-align: center;">
                <p class="lead">
                    <img  src="{{asset('img/blade_icons/405-error.png')}}" alt="unauthorized">
                </p>
            </div>
            <hr class="my-4">
            <p class="lead">
              <a class="btn btn-primary btn-lg" href="#" role="button">Regresar</a>
            </p>
        </div>
    </div>
@endsection
