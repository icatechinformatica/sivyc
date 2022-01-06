<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Suficiencia presupuestal| Sivyc Icatech')
@section('content')
    <section class="container g-pt-50">
        <div class="text-center">
            <h1>Suficiencia Presupuestal</h1>
            <div style="align-content: center">
                <h2>Por favor verifique el documento y que todo este correcto.</h2>
            </div>
        </div>
        <br>
        <div class="text-center">
            <h3><strong>Vista Previa del Documento</strong></h3>
            <div class="form-row text-center">
                <div class="form-group col-md-3"></div>
                <a class="btn btn-info" id="supre_pdf" name="supre_pdf" href="/supre/pdf/{{$id}}" target="_blank">Suficiencia Presupuestal</a><br>
                <div class="form-group col-md-1"></div>
                <a class="btn btn-info" id="anexosupre_pdf" name="anexosupre_pdf" href="/supre/tabla-pdf/{{$id}}" target="_blank">Anexo Suficiencia Presupuestal</a><br>
            </div>
        </div>
        <div class="form-row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{route('modificar_supre', ['id' => $id])}}">Modificar</a>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-success"
                        data-toggle="modal" data-placement="top"
                        data-target="#DocSupreModal"
                        data-id='{{$id}}'
                        title="Enviar a Dirección de Planeación">
                        Enviar a Dirección de Planeación
                    </button>
                </div>
            </div>
        </div>
        <br>
        <!-- Modal -->
            <div class="modal fade" id="DocSupreModal" role="dialog">
                <div class="modal-dialog">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('doc-supre-guardar') }}" id="doc_supre">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Cargar Suficiencia Presupuestal Firmada</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="text-align:center">
                                <div style="text-align:center" class="form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="doc_supre" name="doc_supre" placeholder="Archivo PDF">
                                    <input id="idsupmod" name="idsupmod" hidden>
                                    <button type="submit" class="btn btn-primary" >Guardar</button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <!-- END -->
    </section>
@stop
@section('script_content_js')
    <script src="{{ asset("js/validate/modals.js") }}"></script>
@endsection
