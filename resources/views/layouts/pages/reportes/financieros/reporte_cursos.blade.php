<!-- Creado por Orlando Chávez 200320204 /// 9612986322 /// oorlandochavez@outlook.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reporte de Cursos| Sivyc Icatech')
<head>
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

        td {
            text-align: center; /* center checkbox horizontally */
            vertical-align: middle; /* center checkbox vertically */
        }
        #choice-td{
            background-color: white;
        }
        table {
            border: 1px solid;
            width: 200px;
        }
        tr {
            height: 65px;
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
@section('content')
    <section class="container g-pt-50">
        <form action="{{ route('financieros-reporte-cursos-xls') }}" method="post">
            @csrf
            <div class="text-center">
                <h1>Reporte de Cursos</h1>
            </div>
            <br>
            {{-- <h2>Filtrar Trámites Recepcionados Por:</h2>
            <br> --}}
            {{-- <hr style="border-color:rgb(245, 245, 245)"> --}}
            <div class="form-row">
                <div class="form-group col-md-3"></div>
                <div class="form-group col-md-3">
                    <label for="unidad"><h3>Unidad:</h3></label>
                    <select name="unidad" id="unidad" class="form-control">
                        <option value="SIN ESPECIFICAR">SIN ESPECIFICAR</option>
                        @foreach ($unidades as $data )
                            <option value="{{$data->ubicacion}}">{{$data->ubicacion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="status"><h3>status:</h3></label>
                    <select name="status" id="status" class="form-control">
                        <option value="SIN ESPECIFICAR">SIN ESPECIFICAR</option>
                        <option value="En Espera">En espera de validación</option>
                        <option value="VALIDADO">Validado Digital</option>
                        <option value="PAGADO">Pagado</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3"></div>
                <div class="form-group col-md-3">
                    <label for="fecha1"><h3>Fecha Inicio:</h3></label>
                    <input type="date" name="fecha1" id="fecha1" class="form-control" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha2"><h3>Fecha Termino :</h3></label>
                    <input type="date" name="fecha2" id="fecha2" class="form-control" required>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button id="submit" name="submit" type="submit" class="btn btn-primary" >Generar</button>
                    </div>
                </div>
            </div>
        </form>
        <!--display modal-->
        <div class="modal">
            <div class="center">
                <img alt="" src="{{URL::asset('/img/cargando.gif')}}" />
            </div>
        </div>
    </section>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
