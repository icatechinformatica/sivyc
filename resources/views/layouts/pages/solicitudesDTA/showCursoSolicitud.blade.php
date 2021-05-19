@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Modificación de un curso | SIVyC Icatech')

@section('content')
    
    <div class="container-fluid px-5 mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <div class="col text-center">
                <h3><strong>MODIFICACIÓN DEL CURSO {{$curso[0]->curso}}</strong></h3>
                <h5><strong>{{$curso[0]->opcion_solicitud}}</strong></h5>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>clave del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->clave}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Unidad del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->unidad}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Modalidad del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->mod}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Area del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->area}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Especialidad del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->espe}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->curso}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Inicio del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->inicio}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Termino del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->termino}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Ciclo del curso:</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->ciclo}}</h5></strong></div>
                        </div>
                    </div>
                    <div class="col-6 my-2">
                        <div class="row">
                            <div class="col-4"><h5>Tipo de solicitud</h5></div>
                            <div class="col"><h5><strong>{{$curso[0]->opcion_solicitud}}</h5></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col text-right">
                <button id="btnNoProcede" type="button" class="btn btn-danger btn-lg">No procede</button>
                <button id="btnCancelCurso" type="button" class="btn btn-primary btn-lg">Cancelar curso</button>
            </div>
        </div>
    </div>

    <!-- Modal confirmar cancelacion -->
    <div class="modal fade" id="modalCancelCurso" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                
                <form id="formCancelCurso" enctype="multipart/form-data" action="{{route('solicitudesDTA.cancelar')}}" method="post">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">CANCELACIÓN DEL CURSO </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- archivo --}}
                        <input class="d-none" type="text" id="numSolicitud" name="numSolicitud" value="{{$curso[0]->num_solicitud}}">
                        <input class="d-none" type="text" id="idCurso" name="idCurso" value="{{$curso[0]->id}}">
                        <input class="d-none" type="text" class="form-control" id="id_solicitud" name="id_solicitud" value="{{$curso[0]->id_solicitud}}">
                        <div class="form-group row d-flex align-items-end">
                            <div class="form-group col-6">
                                <label for="num_respuesta" class="control-label">NÚMERO DE RESPUESTA</label>
                                <input type="text" class="form-control" id="num_respuesta" name="num_respuesta"
                                    placeholder="NÚMERO DE RESPUESTA">
                            </div>
                            <div class="form-group col-6">
                                <label for="fecha_respuesta" class="control-label">FECHA DE RESPUESTA</label>
                                <input type='text' id="fecha_respuesta" autocomplete="off" readonly="readonly"
                                    name="fecha_respuesta" class="form-control datepicker" placeholder="FECHA DE RESPUESTA">
                            </div>
                            <div class="form-group col-12">
                                <label for="observaciones" class="control-label">OBSERVACIONES</label>
                                <textarea class="form-control" name="observaciones" id="observaciones" 
                                    placeholder="OBSERVACIONES" cols="30" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">NO, CANCELAR</button>
                        <button type="submit" class="btn btn-danger">SI, CANCELAR CURSO</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Modal no procede -->
    <div class="modal fade" id="modalNoProcede" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                
                <form id="formNoProcede" enctype="multipart/form-data" action="{{route('solicitudesDTA.noProcede')}}" method="post">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">NO PROCEDE LA CANCELACION DEL CURSO</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- archivo --}}
                        <input class="d-none" type="text" id="numSolicitudNo" name="numSolicitudNo" value="{{$curso[0]->num_solicitud}}">
                        <input class="d-none" type="text" id="idCursoNo" name="idCursoNo" value="{{$curso[0]->id}}">
                        <input class="d-none" type="text" class="form-control" id="id_solicitudNo" name="id_solicitudNo" value="{{$curso[0]->id_solicitud}}">
                        <div class="form-group row d-flex align-items-end">
                            <div class="form-group col-6">
                                <label for="num_respuestaNo" class="control-label">NÚMERO DE RESPUESTA</label>
                                <input type="text" class="form-control" id="num_respuestaNo" name="num_respuestaNo"
                                    placeholder="NÚMERO DE RESPUESTA">
                            </div>
                            <div class="form-group col-6">
                                <label for="fecha_respuestaNo" class="control-label">FECHA DE RESPUESTA</label>
                                <input type='text' id="fecha_respuestaNo" autocomplete="off" readonly="readonly"
                                    name="fecha_respuestaNo" class="form-control datepicker" placeholder="FECHA DE RESPUESTA">
                            </div>
                            <div class="form-group col-12">
                                <label for="observacionesNo" class="control-label">OBSERVACIONES</label>
                                <textarea class="form-control" name="observacionesNo" id="observacionesNo" 
                                    placeholder="OBSERVACIONES" cols="30" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-danger">NO PROCEDE</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection


@section('script_content_js')
    
<script>
    $('#btnCancelCurso').click(function () {
        $('#modalCancelCurso').modal('show');
    });

    $('#btnNoProcede').click(function () {
        $('#modalNoProcede').modal('show');
    });

    $("#fecha_respuesta").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: 'yy-mm-dd'
    });

    $("#fecha_respuestaNo").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: 'yy-mm-dd'
    });

    $('#formCancelCurso').validate({
        rules: {
            num_respuesta: {
                required: true
            },
            fecha_respuesta: {
                required: true
            }
        },
        messages: {
            num_respuesta: {
                required: 'Número de solicitud requerido'
            },
            fecha_respuesta: {
                required: 'Fecha de solicitud requerida'
            }
        }
    });

    $('#formNoProcede').validate({
        rules: {
            num_respuestaNo: {
                required: true
            },
            fecha_respuestaNo: {
                required: true
            }
        },
        messages: {
            num_respuestaNo: {
                required: 'Número de solicitud requerido'
            },
            fecha_respuestaNo: {
                required: 'Fecha de solicitud requerida'
            }
        }
    });
</script>
@endsection