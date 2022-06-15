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
                <a class="nav-link " id="pills-paqdid-tab" data-toggle="pill" href="#pills-paqdid" role="tab" aria-controls="pills-paqdid" aria-selected="false">Evaluacion Alumno</a>
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
<script src="{{asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('objetivoespecifico');
    CKEDITOR.replace('aprendizajeesperado');
    CKEDITOR.replace('observaciones');

    function confirmacion() {
        var opcion = confirm("¿Desea Guardar Esta Paqueterias Para Este Curso?");
        if (opcion == true) {
            var value = $('#cke_observaciones').val();
            console.log(value);
            // submit()
        }
    }


    function submit() {

        var url = window.location.pathname;
        var idCurso = url.substring(url.lastIndexOf('/') + 1);
        var infoCursoTecnico = [];
        var evaluacionAlumno = [];

        infoCursoTecnico.push({

            'nombrecurso': $('#nombrecurso').val(),
            'entidadfederativa': $('#entidadfederativa').val(),
            'cicloescolar': $('#cicloescolar').val(),
            'programaestrategico': $('#programaestrategico').val(),
            'modalidad': $('#modalidad').val(),
            'tipo': $('#tipo').val(),
            'perfilidoneop': $('#perfilidoneop').val(),
            'duracion': $('#duracion').val(),
            'formacionlaboral': $('#formacionlaboral').val(),
            'especialidad': $('#especialidad').val(),
            'publico': $('#publico').val(),
            'aprendizajeesperado': $('#aprendizajeesperado').val(),
            'criterio': $('#criterio').val(),
            'ponderacion': $('#storePonderacion').val(),
            'transversabilidad': $('#transversabilidad').val(),
            'contenidotematico': $('#storeContenidoT').val(),
            'observaciones': $('.observaciones').val(),
            'recursos': $('#storeRecursosD').val(),
        });

        var divParentPreguntas = $('#preguntas-area-parent').children();
        var abecedario = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        
        for (var i = 0; i < divParentPreguntas.length; i++) {
            var opciones = ''
            var opcionesCorrectas = ''
            var pregunta = divParentPreguntas[i].children[0].children[0].children[1].value
            
            
            var divOpciones = divParentPreguntas[i].children[3];

            for (var j = 0; j < divOpciones.children.length; j++) {
                var checkbox = divOpciones.children[j].children[0].children[0].children[0].checked//.checked;
                var opcion = divOpciones.children[j].children[1].value;
                
                if(checkbox){
                    opcionesCorrectas += abecedario[j] + '$=$'
                }

                opciones += opcion + '$=$';    
            }

            evaluacionAlumno.push({
                'pregunta': pregunta,
                'opcionesCorrectas': opcionesCorrectas,
                'opciones': opciones,
            });
        }
        
        

        
        $.ajax({
            url: '/paqueterias/guardar/' + idCurso,
            type: 'post',
            dataType: 'json',
            data: {
                '_token': '{{ csrf_token() }}',
                'infoCursoTecnico': infoCursoTecnico,
                'evaluacionAlumno': evaluacionAlumno,
            }
        });
    }
    // $("#cke_summary-ckeditor").addClass("col-md-12");
</script>
<script src="{{asset('js/catalogos/paqueteriasdidactica/paqueterias.js')}}"></script>
@endsection
@endsection