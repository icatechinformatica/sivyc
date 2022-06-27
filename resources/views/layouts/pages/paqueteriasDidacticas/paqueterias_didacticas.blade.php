@extends('theme.sivyc.layout') {{--AGC--}}
@section('title', 'Paqueterias Didacticas | SIVyC Icatech')
@section('css_content')
<link rel="stylesheet" href="{{ asset('css/paqueterias/paqueterias.css') }}" />
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

<div class="card-header">
Formulario de Paqueterias Didacticas
</div>
<div class="card card-body" style=" min-height:450px;">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><br />
    @endif
    <form method="POST" action="{{route('paqueteriasGuardar',$idCurso)}}" id="creacion" enctype="multipart/form-data">
        @csrf

        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso">
                
            </label>
        </div>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-tecnico-tab" data-toggle="pill" href="#pills-tecnico" role="tab" aria-controls="pills-tecnico" aria-selected="true">Informacion Curso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-evalalum-tab" data-toggle="pill" href="#pills-evalalum" role="tab" aria-controls="pills-evalalum" aria-selected="false">Evaluacion Alumno</a>
            </li>
            @if($paqueterias != null)
            <li class="nav-item">
                <a class="nav-link " id="pills-paqdid-tab" data-toggle="pill" href="#pills-paqdid" role="tab" aria-controls="pills-paqdid" aria-selected="false">Paqueterias Didacticas</a>
            </li>
            @endif
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-tecnico" role="tabpanel" aria-labelledby="pills-tecnico-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.curso')
            </div>
            <div class="tab-pane fade" id="pills-evalalum" role="tabpanel" aria-labelledby="pills-evalalum-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.evaluacionAlumno')
            </div>
            <div class="tab-pane fade" id="pills-paqdid" role="tabpanel" aria-labelledby="pills-paqdid-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.descargarPaqueteria')
            </div>
        </div>
    </form>
    <br>
</div>


@section('script_content_js')
<!-- <script src="{{asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('vendor/ckeditor/translations/es.js') }}"></script> -->
<script src="{{asset('vendor/ckeditor5-decoupled-document/ckeditor.js') }}"></script>
<!-- <script src="{{asset('vendor/ckeditor5-decoupled-document/translations/es.js') }}"></script> -->


<script>
    //inicializacion de text areas ckeditor 5
    var editorContenidoT;
    var editorEstrategiaD;
    var editorProcesoE;
    var editorDuracionT;
    var editorContenidoE;

    var editorElementoA;
    var editorAuxE;
    var editorReferencias;
    // DecoupledEditor
    //     .create(document.querySelector('#editor'), {
    //         // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
    //     })
    //     .then(editor => {
    //         const toolbarContainer = document.querySelector('main .toolbar-container');

    //         toolbarContainer.prepend(editor.ui.view.toolbar.element);

    //         window.editor = editor;
    //     })
    //     .catch(err => {
    //         console.error(err.stack);
    //     });
    ClassicEditor
        .create(document.querySelector('#objetivoespecifico'), {
            language: 'es',

        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#aprendizajeesperado'), {
            language: 'es',

        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#observaciones'), {
            language: 'es',

        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#contenidoT-inp'), {
            language: 'es',
            filebrowserUploadUrl: "{{route('ckeditorUpload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form'

        })
        .then(editor => {
            editorContenidoT = editor;
        })
        .catch(error => {
            console.error(error);
        });


    ClassicEditor
        .create(document.querySelector('#elementoapoyo'), {
            language: 'es',
        })
        .then(editor => {
            editorElementoA = editor;
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#auxenseñanza'), {
            language: 'es',
        })
        .then(editor => {
            editorAuxE = editor;
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#referencias'), {
            language: 'es',
        })
        .then(editor => {
            editorReferencias = editor;
        })
        .catch(error => {
            console.error(error);
        });

    $(document).ready(function() {
        $("#botonCARTADESCPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarPaqueteria',$idCurso)}}");
            $('#creacion').submit();
        });
        $("#botonEVALALUMNPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarEvalAlumno',$idCurso)}}");
            $('#creacion').submit();
        });
        $("#botonEVALINSTRUCTORPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarEvalInstructor',$idCurso)}}");
            $('#creacion').submit();
        });


    });
</script>
<script src="{{asset('js/catalogos/paqueteriasdidactica/paqueterias.js')}}"></script>
@endsection
@endsection