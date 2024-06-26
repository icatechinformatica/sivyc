<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Validar Pago| Sivyc Icatech')

@section('content')
    <section class="container g-pt-50">

        <div class="text-center">
            <h1>Validar Pagos</h1>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="dropno_memo">N°. de Contrato</label>
            <input name="numero_contrato" id="numero_contrato" type="text" disabled value="{{$contratos->numero_contrato}}" class="form-control">
            </div>
            <div class="form-group col-md-2">
                <label for="dropfecha_memo">Cantidad</label>
            <input name="cantidad_letras1" id="cantidad_letras1" type="text" disabled value="{{$datapago->liquido}}" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="unidad_capacitacion">Unidad de Capacitación</label>
                <input name="unidad_capacitacion" id="unidad_capacitacion" type="text" disabled value="{{$contratos->unidad_capacitacion}}" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label for="nombre_director">Nombre del Director de Unidad</label>
                <input name="nombre_director" id="nombre_director" type="text" disabled value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}" class="form-control">
            </div>
        </div>
        <br>
        <div class="form-row" style="">
            <div class="form-group col-md-1">
                <button type="button" id="rechazarPago" name="rechazarPago" class="btn btn-danger">Rechazar</a>
            </div>
            <div class="form-group col-md-1">
                <a class="btn btn-success" id="verificar_pago" name="verificar_pago" data-toggle="modal" data-target="#validarModel" data-id="{{ $contratos->id_folios }}">Verificar</a>
            </div>
        </div>
        <form method="POST" action="{{ route('pago-rechazo') }}">
            @csrf
            <div id="rechazar_pago" class="form-row d-none d-print-none">
                <div class="form-group col-md-6">
                    <label for="observaciones">Describa el motivo de rechazo</label>
                    <textarea name="observaciones" id="observaciones" cols="6" rows="6" class="form-control"></textarea>
                </div>
            </div>
            <div id="btn_rechazar" class="form-row d-none d-print-none">
                <div class= "form-group col-md-3">
                    <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                    <input hidden id="idPago" name="idPago" value="{{$datapago->id}}">
                    <input hidden id="idfolios" name="idfolios" value="{{$contratos->id_folios}}">
                </div>
            </div>
        </form>
        <br>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
        <!--Modal-->
            <div class="modal fade" id="validarModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Validar Solicitud</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            ¿ Estás seguro de validar el pago?
                        </div>
                        <div class="modal-footer">
                            <form action="" id="validarForm" method="get">
                                @csrf
                                <input type="hidden" name="id">
                                <button type="button" class="btn btn-danger" style="margin-right: 5px;" data-dismiss="modal">Cerrar</button>
                                <button type="submit" style="margin-left: 5px;" class="btn btn-success">Validar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <!--Modal End-->
        <br>

    </section>
@endsection
@section('script_content_js')
    <script src="{{ asset("js/validate/modals.js") }}"></script>
    <script>
        $(function(){
            //boton valsupre rechazar
            $("#rechazarPago").click(function(e){
            e.preventDefault();
            $('#rechazar_pago').prop("class", "form-row");
            $('#btn_rechazar').prop("class", "form-row");
            //$('#observaciones').prop("class", "form-row");
            //$('#observaciones').rules('add',  { required: true });
            });
        });
    </script>
@endsection
