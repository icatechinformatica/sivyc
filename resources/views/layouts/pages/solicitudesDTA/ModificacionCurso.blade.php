@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Modificación de un curso | SIVyC Icatech')

@section('content')
    
    <div class="container-fluid px-5 mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <div class="col text-center">
                <h2>SOLICITUD MODIFICACIÓN DE CURSO</h2>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <form action="{{route('solicitudesDTA.inicio')}}" method="get">
            @csrf
            <div class="form-group row d-flex align-items-end">
                <div class="form-group col">
                    <label for="num_solicitud" class="control-label">NÚMERO DE SOLICITUD</label>
                    <input type="text" class="form-control" id="num_solicitud" name="num_solicitud"
                        placeholder="NÚMERO DE SOLICITUD">
                </div>
                <div class="form-group col d-flex justify-content-center">
                    <button type="submit" id="btnBuscarSolicitud" class="btn btn-primary">BUSCAR SOLICITUD</button>
                </div>
            </div>
        </form>

        @if ($solicitud != null && !$solicitud->isEmpty())
            <div class="d-none">{{$showButtons = false}}</div>
            @if ($solicitud[0]->archivo_respuesta != null)
                <div class="alert alert-success">
                    <p class="text-center"> <strong>RESPUESTA ENVIADA</strong></p>
                </div>
            @endif

            <table class="table table-bordered table-striped mt-2">
                <thead>
                    <tr>
                        <th scope="col">CLAVE</th>
                        <th scope="col">UNIDAD</th>
                        <th scope="col">AREA</th>
                        <th scope="col">CURSO</th>
                        <th scope="col">ESPECIALIDAD</th>
                        <th scope="col">MOTIVO</th>
                        <th scope="col">DESCRIPCIÓN</th>
                        <th scope="col">ESTATUS</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($solicitud as $item)
                        @if ($item->statusSoli == 'ATENDIDO' || $item->statusSoli == 'NO PROCEDE')
                            <div class="d-none">{{$showButtons = true}}</div>
                        @endif

                        <tr>
                            <td>{{ $item->clave }}</td>
                            <td>{{ $item->unidad }}</td>
                            <td>{{ $item->area }}</td>
                            <td>{{ $item->curso }}</td>
                            <td>{{ $item->espe }}</td>
                            <td> <strong>{{ $item->opcion_solicitud }}</strong></td>
                            <td>{{ $item->obs_solicitud }}</td>
                            @if ($item->statusSoli == 'ATENDIDO')
                                <td><strong>ATENDIDO</strong></td>
                            @elseif($item->statusSoli == 'NO PROCEDE')
                                <td><strong>NO PROCEDE</strong></td>
                            @else
                                <td><strong>NO ATENDIDO</strong></td>
                            @endif
                            <td>
                                @if ($item->statusSoli == 'ATENDIDO' || $item->statusSoli == 'NO PROCEDE')
                                    NO DISPONIBLE
                                @else
                                    @if ($item->opcion_solicitud == 'CANCELACIÓN DE CURSO')
                                        <a class="btn btn-warning btn-circle m-1 btn-circle-sm" title="Modificar"
                                            href="{{ route('solicitudesDTA.show', ['id' => $item->id_solicitud]) }}">
                                            <i class="fa fa-pencil-square-o fa-2x pt-2" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-warning btn-circle m-1 btn-circle-sm" title="Modificar"
                                            href="{{ route('solicitudesDTA.showModify', ['id' => $item->id_solicitud]) }}">
                                            <i class="fa fa-pencil-square-o fa-2x pt-2" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($showButtons)
                {{-- @if ($solicitud[0]->archivo_respuesta == null) --}}
                    <div class="row pr-2 d-flex justify-content-end">
                        <button id="btnRespuesta" type="button" class="btn btn-success">ARCHIVO DE RESPUESTA</button>
                        <a href="{{route('tablaSolicitudDTA.pdf', ['id' => $solicitud[0]->num_solicitud])}}">
                            <button type="button" class="btn btn-primary">GENERAR PDF</button>
                        </a>
                    </div>
                {{-- @endif --}}
            @endif
        @endif

        <!-- Modal solicitar archivo -->
        <div class="modal fade" id="modalArchivoRespuesta" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    

                    <form id="formCargarArchivoRes" enctype="multipart/form-data" action="{{route('solicitudesDTA.guardarRespuesta')}}" method="post">
                        @csrf

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">CARGAR ARCHIVO DE RESPUESTA</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            {{-- archivo --}}
                            <input class="d-none" type="text" id="idsolicitud" name="idSolicitud" value="{{($solicitud != null && !$solicitud->isEmpty()) ? $solicitud[0]->id_solicitud : ''}}">
                            <input class="d-none" type="text" class="form-control" id="num_solicitud" name="num_solicitud" value="{{($solicitud != null && !$solicitud->isEmpty()) ? $solicitud[0]->num_solicitud : ''}}">
                            <div class="form-group col">
                                <label for="status">ARCHIVO DE RESPUESTA</label>
                                <div class="custom-file">
                                    <input type="file" id="archivo_respuesta" name="archivo_respuesta" accept="application/pdf"
                                        class="custom-file-input">
                                    <label for="archivo_respuesta" class="custom-file-label">ARCHIVO DE RESPUESTA</label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">ENVIAR ARCHIVO</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script_content_js')
    <script>
        $('#btnRespuesta').click(function () {
            $('#modalArchivoRespuesta').modal('show');
        });

        $('#formCargarArchivoRes').validate({
            rules: {
                archivo_respuesta: {
                    required: true
                }
            }, 
            messages: {
                archivo_respuesta: {
                    required: 'Debe cargar la respuesta en formato pdf'
                }
            }
        });
    </script>
@endsection
