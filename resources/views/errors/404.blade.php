@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Error 404 No Encontrado | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <div class="jumbotron">
            <h1 class="display-4">
                ¡Página o archivo No encontrado!
            </h1>
            <p class="lead">
                <img src="{{asset("img/blade_icons/404-error.png") }}" alt="unauthorized" srcset="">
                El archivo o página que intenta accesar no se encuentra en el sistema.
            </p>
            <hr class="my-4">
            <p class="lead">
              <a class="btn btn-primary btn-lg" href="#" role="button">Regresar</a>
            </p>
        </div>
    </div>
@endsection
