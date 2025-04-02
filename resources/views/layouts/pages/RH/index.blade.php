@extends('theme.sivyc.layout')
@section('title', 'Recursos Humanos | SIVyC Icatech')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <div class="card-header">
       Recursos Humanos / Registro de Checado
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
            @include('layouts.pages.RH.table_data')
        </table>
        <div class="col-lg-12 d-flex justify-content-between align-items-right" style="text-align: right;">
            <div></div>
            <div style="padding-top: 3%;">
                <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-placement="top" data-target="#cargarModal"> CARGAR ASISTENCIAS</button>
            </div>
        </div>
        <br>
        <!-- Modal Cargar Asistencias-->
        <div class="modal fade" id="cargarModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cargar Archivos PDF Generables</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        <form action="{{ route('asistencia.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file" id="arch_asitencias_label">
                                    <a class="btn px-10 py-10 mr-10" style="background-color: #12322B; color: white;">
                                        &nbsp; <i class="fas fa-cloud-upload-alt f-s"></i> &nbsp;
                                    </a>
                                    <span id="arch_asistencias_name">Subir</span>
                                </label>
                                <input hidden type="file" name="file" id="file" class="form-control-file" accept=".dat,.csv,.txt">
                            </div>
                            <button type="submit" class="btn btn-primary">Subir Archivo</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Fin Modal --}}
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
