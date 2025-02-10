<!-- Creado por Orlando Chávez 20052021-->
@extends('theme.sivyc.layout')
@section('title', 'Reporte de Alumnos por Vinculador| Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" /> 
    <style>
        .radio-xl .custom-control-label::before,
        .radio-xl .custom-control-label::after {
        top: 1.2rem;
        width: 1.85rem;
        height: 1.85rem;
        }

        .radio-xl .custom-control-label {
        padding-top: 23px;
        padding-left: 10px;
        }
      
       .modal
        {
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background-color: Black;
            filter: alpha(opacity=60);
            opacity: 0.6;
            -moz-opacity: 0.8;
        }
        .center
        {
            z-index: 1000;
            margin: 300px auto;
            padding: 10px;
            width: 150px;
            background-color: White;
            border-radius: 10px;
            filter: alpha(opacity=100);
            opacity: 1;
            -moz-opacity: 1;
        }
        .center img
        {
            height: 128px;
            width: 128px;
        }
    </style>
</head>
@endsection
@section('content')       
    <div class="card-header">
        Reportes / Alumnos por Vinculador
    </div>
    <div class="card card-body p-5" >        
        <form action="{{ route('vinculacion.reportepdf') }}" method="post" id="registercontrato">
            @csrf            
            <h2>Filtrar Por:</h2>
            <br>
            <table  id="table-instructor" class="table table-responsive-md">
                <tbody>
                    <tr>
                        <td class="custom-radio radio-xl" id='choice-td'>
                            <input type="radio" class="custom-control-input"  id="general" name="filtro" value="general">
                            <label for="general" class="custom-control-label"><h4>General</h4></label>
                        </td>
                        <td class="custom-radio radio-xl" id='choice-td'>
                            <input type="radio" class="custom-control-input"  id="curso" name="filtro" value="curso">
                            <label for="curso" class="custom-control-label"><h4>Curso</h4></label>
                        </td>
                        <td class="custom-radio radio-xl" id='choice-td'>
                            <input type="radio" class="custom-control-input"  id="vinculador" name="filtro" value="vinculador">
                            <label for="vinculador" class="custom-control-label"><h4>Vinculador</h4></label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <div class="form-row">
                <div class="form-group col-md-2"></div>
                <div class="form-group col-md-3">
                    <label for="inputid_curso"><h3>De:</h3></label>
                    <input type="date" name="fecha1" id="fecha1" class="form-control" required>
                </div>
                <div class="form-group col-md-1"></div>
                <div class="form-group col-md-3">
                    <label for="inputid_curso"><h3>Hasta:</h3></label>
                    <input type="date" name="fecha2" id="fecha2" class="form-control" required>
                </div>
            </div>
            <div id="div_curso" class="form-row d-none d-print-none">
                <div class="form-group col-md-2"></div>
                <div class="form-group col-md-6">
                    <label for="inputid_curso"><h3>Nombre del Curso</h3></label>
                    <input type="text" name="cursoaut" id="cursoaut" class="form-control">
                    <input type="text" name="id_curso" id="id_curso" class="form-control" hidden>
                </div>
            </div>
            <div id="div_vinculador" class="form-row d-none d-print-none">
                <div class="form-group col-md-2"></div>
                <div class="form-group col-md-6">
                    <label for="inputid_vinculador"><h3>Nombre del Vinculador</h3></label>
                    <input type="text" name="vinculadoraut" id="vinculadoraut" class="form-control">
                    <input type="text" name="id_vinculador" id="id_vinculador" class="form-control" hidden>
                </div>
            </div>
            <br>            
            <div class="row d-flex justify-content-between">
                <a class="btn btn" href="{{URL::previous()}}"> < Regresar</a>
                <button id="submit" name="submit" type="submit" class="btn btn-danger">Generar</button>
            </div>
        </form>
        <!--display modal-->
        <div class="modal">
            <div class="center">
                <img alt="" src="{{URL::asset('/img/cargando.gif')}}" />
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script>
    $(function(){
        //metodo
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

       $( document ).on('input', function(){
            if(document.getElementById('curso') != null || document.getElementById('general') != null || document.getElementById('vinculador') != null)
            {
                if (document.getElementById('curso').checked) {
                    $('#div_curso').prop("class", "form-row")
                    $('#div_vinculador').prop("class", "form-row d-none d-print-none")
                    $('#div_unidad').prop("class", "form-row d-none d-print-none")
                }
                else if (document.getElementById('general').checked) {
                    $('#div_curso').prop("class", "form-row d-none d-print-none")
                    $('#div_vinculador').prop("class", "form-row d-none d-print-none")
                    $('#div_unidad').prop("class", "form-row d-none d-print-none")
                }
                else if (document.getElementById('vinculador').checked) {
                    $('#div_curso').prop("class", "form-row d-none d-print-none")
                    $('#div_vinculador').prop("class", "form-row")
                }
            }
        });
    });
</script>
@endsection
