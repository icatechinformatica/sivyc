@extends('theme.sivyc.layout')

@section('title', 'Recursos Humanos | SIVyC Icatech')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <div class="card-header">
       Recursos Humanos / Reporte Quincenal
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
        </div>
        <hr style="border-color:dimgray">
        <table id="tablaResultados" class="table table-bordered">
            <caption>Catalogo de Funcionarios</caption>
            <thead>
                <tr>
                    <th scope="col" width="110px">No. DE ENLACE</th>
                    <th scope="col" width="250px">UNIDAD DE CAPACITACIÓN</th>
                    <th scope="col" width="250px">NOMBRE</th>
                    <th width="80px">ACCION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $registro)
                    <tr>
                        <td style="text-align: center;">{{$registro->numero_enlace}}</td>
                        <td>{{$registro->nombre_adscripcion}}</td>
                        <td>{{$registro->nombre_trabajador}}</td>
                        <td>
                            <a data-toggle="modal" data-placement="top" data-target="#ReporteModal" data-id='{{$registro->numero_enlace}}'>
                                <i class="fa fa-file-pdf fa-2x fa-lg text-danger" title="Generar Reporte"></i>
                            </a> &nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="{{route('rh.reporte.detalles', ['id' => $registro->numero_enlace])}}">
                                <i class="fa fa-eye fa-2x fa-lg text-success" title="Ver Detalles"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        {{ $data->appends(request()->query())->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
@section('script_content_js')
    <script>
        $(document).ready(function(){
            $('#busqueda').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('rh.reporte.quincenal') }}",
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
                console.log(fechaInicio);
                console.log(fechaTermino);

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
    <script>
        $(function(){
                    $('#ReporteModal').on('show.bs.modal', function(event) {
                console.log('asd');
            });

        });
    </script>
@endsection
