@extends('theme.sivyc.layout')
@section('title', 'Recursos Humanos | SIVyC Icatech')
@section('content')
    @php $months = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE']; $id_registro = null; @endphp
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <div class="card-header">
       RECURSOS HUMANOS / TARJETA DE TIEMPO
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
        <form id="pdfForm" action="{{ route('rh.reporte.quincenal.pdf') }}" method="POST" target="_blank">
            @csrf
            <div class="col-lg-4 d-flex justify-content-between align-items-center">
                <div class="form-group">
                    <label for="mes">Busqueda de Tarjetas de Tiempo</label>
                    <select name="mes" id="mes" class="form-control">
                        @foreach($months as $key => $month)
                            <option value="{{$key+1}}" @if($month == $range['month']) selected @endif>{{$month}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="quincena">Quincena</label>
                    <select name="quincena" id="quincena" class="form-control">
                        <option value="1" @if($range['quincena'] == 'primera') selected @endif>PRIMERA QUINCENA</option>
                        <option value="16" @if($range['quincena'] == 'segunda') selected @endif>SEGUNDA QUINCENA</option>
                    </select>
                </div>
                <input id="numero_enlace" name="numero_enlace" type="hidden" value="{{$numero_enlace}}">
            </div>
        </form>
        <div class="form-row" style=" margin: 0% 5% -2% 5%;">
            <div class="form-group" style="width: 50%;">
                <p>Número: <b>{{$numero_enlace}}</b></p>
                <p>Empleado: <b>{{$data[0]->nombre_trabajador}}</b></p>
            </div>
            <div class="form-group" style="text-align: right; width: 50%;">
                <p style="text-align: right;">Grupo: OFICINAS ADMINISTRATIVAS</p>
                {{-- <p style="text-align: right;">Grupo: {{$data[0]->nombre_adscripcion}}</p> --}}
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table id="tablaResultados" class="table table-bordered" style='text-align:center;'>
            <caption>Tarjeta de Tiempo</caption>
            @include('layouts.pages.RH.table_reporteQuincenalDetalles')
        </table>
        <div style="text-align: right;">
            <a style="width: 20%;" type="button" class="btn btn-info" href="#" target="_blank" onclick="GenerarPDF()"> Generar Tarjeta de Tiempo</a>
        </div>
        <!-- Modal Justificante-->
        <div class="modal fade" id="JustificanteModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Justificante de Falta</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        {{-- <form action="{{ route('add.justificante') }}" method="POST" enctype="multipart/form-data"> --}}
                            @csrf
                            <div class="form-group" style="text-align: center; justify-content: center;">
                                <label for="justificante" id="justificantelabel">Numero de oficio de justificante</label>
                                <input style="width: 70%; margin-left: 15%; text-align: center;" type="text" name="justificante" id="justificante" class="form-control">
                                <input id="registro_id" name="registro_id" type="hidden">
                                <input id="registro_fecha" name="registro_fecha" type="hidden">
                                <input id="numero_enlace_justificante" name="numero_enlace_justificante" type="hidden">
                                <br>
                                <button type="button" class="btn btn-success" id="confirmar_justificante" name="confirmar_justificante">CONFIRMAR</button>
                            </div>
                        {{-- </form> --}}
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
    <script>
        $(document).ready(function(){
            function buscarDatos() {
                var mes = $('#mes').val();
                var quincena = $('#quincena').val();
                var numero_enlace = $('#numero_enlace').val();
                $.ajax({
                    url: "{{ route('rh.reporte.detalles', ['id' => '__ID__']) }}".replace('__ID__', numero_enlace),
                    method: 'GET',
                    data: {
                        mes: mes,
                        quincena: quincena,
                        numero_enlace: numero_enlace
                    },
                    success: function(data) {
                        $('#tablaResultados').html(data); // Solo actualiza el tbody
                    }
                });
            }

            $('#mes, #quincena').on('change', function() {
                buscarDatos();
            });

            $('#confirmar_justificante').click(function () { // envio de justificante para su guardado
            var registro_id = $('#registro_id').val();
            var registro_fecha = $('#registro_fecha').val();
            var justificante = $('#justificante').val();
            var numero_enlace = $('#numero_enlace_justificante').val();
            $.ajax({
                    url: "{{ route('rh.agregar.justificante') }}",
                    method: 'GET',
                    data: {
                        numero_enlace: numero_enlace,
                        registro_id: registro_id,
                        registro_fecha: registro_fecha,
                        justificante: justificante
                    },
                    success: function(data) {
                        $('#JustificanteModal').modal('hide');
                        $('#justificante').val('');
                        buscarDatos();
                    }
                });
            });
        });

        function GenerarPDF() { // generacion de pdf
            document.getElementById('pdfForm').submit();
        }
    </script>
    <script>
        $(function(){
            $('#JustificanteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                // console.log(id)
                document.getElementById('registro_id').value = id[0];
                document.getElementById('registro_fecha').value = id[1];
                document.getElementById('numero_enlace_justificante').value = id[2];
            });

        });
    </script>
@endsection
