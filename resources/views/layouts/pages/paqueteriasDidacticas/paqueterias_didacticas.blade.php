@extends('theme.sivyc.layout') {{--AGC--}}
@section('title', 'Paqueterias Didacticas | SIVyC Icatech')
@section('css_content')
<link rel="stylesheet" href="{{ asset('css/paqueterias/paqueterias.css') }}" />
@endsection
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
    <form method="POST" action="{{route('paqueteriasGuardar',$idCurso)}}" id="creacion" enctype="multipart/form-data">
        @csrf

        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso">
                <h1>Formulario de Paqueterias Didacticas</h1>
            </label>
        </div>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-tecnico-tab" data-toggle="pill" href="#pills-tecnico" role="tab" aria-controls="pills-tecnico" aria-selected="true">Informacion Curso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " id="pills-evalalum-tab" data-toggle="pill" href="#pills-evalalum" role="tab" aria-controls="pills-evalalum" aria-selected="false">Evaluacion Alumno</a>
            </li>
            @if($paqueterias != null)
            <li class="nav-item">
                <a class="nav-link " id="pills-paqdid-tab" data-toggle="pill" href="#pills-paqdid" role="tab" aria-controls="pills-paqdid" aria-selected="false">Paqueterias Didacticas</a>
            </li>
            @endif
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active " id="pills-tecnico" role="tabpanel" aria-labelledby="pills-tecnico-tab">
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
<script src="{{asset('vendor/ckeditor/ckeditor.js') }}" ></script>
<script src="{{asset('vendor/ckeditor/translations/es.js') }}" ></script>
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
    ClassicEditor
            .create( document.querySelector( '#objetivoespecifico' ),{
                language: 'es'
            } )
            .catch( error => {
                console.error( error );
            } );
    
    ClassicEditor
            .create( document.querySelector( '#aprendizajeesperado' ),{
                language: 'es'
            } )
            .catch( error => {
                console.error( error );
            } );
    
    ClassicEditor
            .create( document.querySelector( '#observaciones' ),{
                language: 'es'
            } )
            .catch( error => {
                console.error( error );
            } );
    
    ClassicEditor
            .create( document.querySelector( '#contenidotematico' ),{
                language: 'es'
            } )
            .then ( editor =>{
                editor.data.set("<ul><li><strong>Tema</strong><ul><li>Subtema 1&nbsp;</li><li>Subtema 2</li></ul></li></ul>");
                editorContenidoT = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#estrategiadidactica' ),{
                language: 'es'
            } )
            .then ( editor =>{
                editorEstrategiaD = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#procesoevaluacion' ),{
                language: 'es'
            } )
            .then( editor =>{
                editorProcesoE = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#duracionT' ),{
                language: 'es'
            } )
            .then( editor =>{
                editorDuracionT = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#contenidoExtra' ),{
            language: 'es'} )
            .then ( editor =>{
                editor.data.set("<h2>Tema</h2><ul><li>Contenido…..</li></ul><h4>Subtema 1</h4><ul><li>Contenido …</li></ul><h4>Subtemas 2</h4><ul><li>Contenido …</li></ul>");
                editorContenidoE = editor;
            })
            .catch( error => {
                console.error( error );
            } );


    ClassicEditor
            .create( document.querySelector( '#elementoapoyo' ),{
            language: 'es'} )
            .then ( editor =>{
                editorElementoA = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#auxenseñanza' ),{
            language: 'es'} )
            .then ( editor =>{
                editorAuxE = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    ClassicEditor
            .create( document.querySelector( '#referencias' ),{
            language: 'es'} )
            .then ( editor =>{
                editorReferencias = editor;
            })
            .catch( error => {
                console.error( error );
            } );
    

</script>
<script src="{{asset('js/catalogos/paqueteriasdidactica/paqueterias.js')}}"></script>
@endsection
@endsection