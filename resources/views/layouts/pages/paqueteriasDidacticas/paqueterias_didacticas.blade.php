@extends('theme.sivyc.layout') {{--AGC--}}
@section('title', 'Paqueterias Didacticas | SIVyC Icatech')
@section('content')

<div class="container g-pt-50">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><br />
    @endif
    <form method="POST" action="" method="post" id="frmcursoscatalogo" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso">
                <h1>Formulario de Paqueterias Didacticas</h1>
            </label>
        </div>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link " id="pills-tecnico-tab" data-toggle="pill" href="#pills-tecnico" role="tab" aria-controls="pills-tecnico" aria-selected="true">Informacion Curso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="pills-evalalum-tab" data-toggle="pill" href="#pills-evalalum" role="tab" aria-controls="pills-evalalum" aria-selected="false">Evaluacion Alumno</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-evalinstr-tab" data-toggle="pill" href="#pills-evalinstr" role="tab" aria-controls="pills-evalinstr" aria-selected="false">Evaluacion Instructor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-evalcurso-tab" data-toggle="pill" href="#pills-evalcurso" role="tab" aria-controls="pills-evalcurso" aria-selected="false">Evaluacion Curso</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade" id="pills-tecnico" role="tabpanel" aria-labelledby="pills-tecnico-tab">
            @include('layouts.pages.paqueteriasDidacticas.blades.curso')
            </div>
            <div class="tab-pane fade show active" id="pills-evalalum" role="tabpanel" aria-labelledby="pills-evalalum-tab">
            @include('layouts.pages.paqueteriasDidacticas.blades.evaluacionAlumno')
            </div>
            <div class="tab-pane fade" id="pills-evalcurso" role="tabpanel" aria-labelledby="pills-evalcurso-tab">3</div>
            <div class="tab-pane fade" id="pills-evalinstr" role="tabpanel" aria-labelledby="pills-evalinstr-tab">4</div>
        </div>



    </form>
    <br>
</div>


@section('script_content_js')
<script src="{{asset('js/catalogos/paqueteriasdidactica/paqueterias.js')}}"></script>

@endsection
@endsection