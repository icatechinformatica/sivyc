@extends('theme.sivyc.layout')

@section('title', 'Recursos Humanos | SIVyC Icatech')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <div class="card-header">
       RECURSOS HUMANOS / REGISTRO DE CHECADO
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
            <div>
                <input type="text" id="busqueda" class="form-control mr-sm-2" placeholder="BUSCAR">
            </div>
            <div style="padding-top: 3%">
                <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-placement="top" data-target="#cargarModal"> CARGAR ASISTENCIAS</button>
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
                    <th scope="col" width="90px">FECHA</th>
                    <th scope="col">ENTRADA - SALIDA</th>
                    <th scope="col" width="120px">RETARDO / FALTA</th>
                    <th scope="col">JUSTIFICANTE</th>
                    <th scope="col">OBSERVACIÓN</th>
                    <th width="80px">ACCION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $registro)
                    <tr>
                        <td style="text-align: center;">{{$registro->numero_enlace}}</td>
                        <td>{{$registro->nombre_adscripcion}}</td>
                        <td>{{$registro->nombre_trabajador}}</td>
                        <td>{{$registro->fecha}}</td>
                        <td>{{$registro->entrada}} - {{$registro->salida}}</td>
                        <td>@if(is_null($registro->salida) || $registro->inasistencia) Inasistencia @elseif($registro->retardo) Retardo @endif</td>
                        <td>{{$registro->justificante}}</td>
                        <td>{{$registro->observaciones}}</td>
                        <td>
                            <a data-toggle="modal" data-placement="top" data-target="#JustificanteModal">
                                <i class="fa fa-edit fa-2x fa-lg text-success" title="Agregar Justificante"></i>
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
                        <form action="{{ route('add.justificante') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group" style="text-align: center; justify-content: center;">
                                <label for="file" id="justificante">Numero de oficio de justificante</label>
                                <input style="width: 70%; padding-left: 15%;" type="text" name="justificante" id="justificante" class="form-control">
                            </div>
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
        });
    </script>
@endsection
