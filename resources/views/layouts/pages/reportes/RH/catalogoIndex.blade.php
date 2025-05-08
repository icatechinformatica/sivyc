@extends('theme.sivyc.layout')
@section('title', 'Recursos Humanos | SIVyC Icatech')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <div class="card-header">
       Recursos Humanos / Catalogo de Funcionario
    </div>
    <div class="card card-body" style="min-height:450px;">
        @if ($message = Session::get('warning'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <div >
                <label for="busqueda">Busqueda</label>
                <input type="text" id="busqueda" class="form-control mr-sm-2" placeholder="BUSCAR">
            </div>
            <div style="margin-left: 0%;">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" id="fecha_inicio" class="form-control mr-sm-2" >
            </div>
            <div style="margin-left: -2%;">
                <label for="fecha_inicio">Fecha Termino</label>
                <input type="date" id="fecha_termino" class="form-control mr-sm-2">
            </div>
            <div style="padding-top: 2%">
                {{-- <button id="buscar" class="btn btn-primary">Buscar</button> --}}
            </div>
            <div style="margin-left: 40%;"></div>
        </div>
        <hr style="border-color:dimgray">
        <table id="tablaResultados" class="table table-bordered">
            @include('layouts.pages.reportes.RH.table_catalogoIndex')
        </table>
        <br>
    </div>
@endsection

@section('script_content_js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#busqueda').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('rh.index') }}",
                    method: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#tablaResultados').html(data);
                    }
                });
            });

            function buscarDatos() {
                var query = $('#busqueda').val();
                var fechaInicio = $('#fecha_inicio').val();
                var fechaTermino = $('#fecha_termino').val();

                $.ajax({
                    url: "{{ route('rh.index') }}",
                    method: 'GET',
                    data: {
                        search: query,
                        fecha_inicio: fechaInicio,
                        fecha_termino: fechaTermino
                    },
                    success: function(data) {
                        console.log(data);
                        $('#tablaResultados').html(data); // Solo actualiza el tbody
                    }
                });
            }

            $('#fecha_inicio, #fecha_termino').on('change', function() {
                buscarDatos();
            });
        });
    </script>
@endsection
