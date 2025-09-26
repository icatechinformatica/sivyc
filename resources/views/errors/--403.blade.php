@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Error 403 No Autorizado | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <div class="jumbotron">
            <h1 class="display-4">
                ¡Acción no Autorizada!
            </h1>
            <p class="lead">
                <img src="{{asset("img/blade_icons/unauthorized.png") }}" alt="unauthorized" srcset="">
                No cuenta con los permisos suficientes para realizar esta acción.
            </p>
            <hr class="my-4">
            <p>Debido a nuestras políticas de privacidad usted no tiene permisos para visitar o realizar alguna acción en el módulo al cual está refiriendose.</p>
            <p class="lead">
              <a class="btn btn-primary btn-lg" href="#" role="button">Regresar</a>
            </p>
        </div>
    </div>
@endsection
