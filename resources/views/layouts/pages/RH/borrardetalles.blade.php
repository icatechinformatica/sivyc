<!--Creado por Orlando Chavez BORRAR EL 27 DE MAYO DEL 2025-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Recursos Humanos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card-header">
    Tablero RH / Ver Funcionario
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if ($message =  Session::get('warning'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message =  Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Catalogo de Solcitudes</caption>
            <thead>
                <tr>
                    <th scope="col" width="100px">No. de Enlace</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Entrada - Salida</th>
                    <th scope="col" width="120px">Retardo / Falta</th>
                    <th scope="col">Justificante</th>
                    <th scope="col">Observacion</th>
                    <th width="200px">Accion</th>
                </tr>
            </thead>
            <tbody>
                @if(!is_null($data))
                    @foreach($data as $key => $registro)
                        <tr>
                            <td style="text-align: center;">{{$registro->numero_enlace}}</td>
                            <td>{{$registro->fecha}}</td>
                            <td>{{$registro->entrada}} - {{$registro->salida}}</td>
                            <td>@if(is_null($registro->salida) || $registro->inasistencia) Inasistencia @elseif($registro->retardo) Retardo @endif</td>
                            <td>{{$registro->justificante}}</td>
                            <td>{{$registro->observaciones}}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif
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
        <!-- Modal -->
        <div class="modal fade" id="cargarModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cargar Archivos PDF Generables</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        <!-- Formulario de carga de archivo -->
                        <form action="{{ route('asistencia.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                {{-- <label for="file">Seleccione un archivo</label> --}}
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
@endsection
@section('script_content_js')
    <script>
        // Escuchar el evento "change" en el input de archivo
        document.getElementById('file').addEventListener('change', function(event) {
            const fileName = event.target.files[0]?.name || "Subir";
            document.getElementById('arch_asistencias_name').textContent = fileName;
        });
    </script>
@endsection
