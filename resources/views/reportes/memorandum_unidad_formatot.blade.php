{{-- creado por MIS DANIEL MÉNDEZ CRUZ --}}
@extends('theme.sivyc.layout')
{{-- llamar a la plantilla principal --}}
@section('title', 'Cursos de Formato T enviados a Dirección de Planeación | SIVYC ICATECH')
{{-- sección del titutlo --}}
@section('content_script_css')
    <style>
        #spinner:not([hidden]) {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #spinner::after {
        content: "";
        width: 80px;
        height: 80px;
        border: 2px solid #f3f3f3;
        border-top: 3px solid #f25a41;
        border-radius: 100%;
        will-change: transform;
        animation: spin 1s infinite linear
        }
        table tr td {
            border: 1px solid #ccc;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @media all and (max-width:500px){
            table{
                width:100%;
            }

            td{
                display:block;
                width:100%;
            }

            tr{
                display:block;
                margin-bottom:30px;
            }
        }

    </style>
@endsection
{{-- seccion de un contenido css para estilos definidos del  archivo --}}
@section('content')
    <div class="container-fluid px-5 g-pt-20">
        <div class="alert"></div>
        @if($errors->any())
            <div class="alert alert-danger">
                {{$errors->first()}}
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        {{-- row --}}
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2> <strong>MEMORANDUMS FIRMADOS Y RECIBIDOS POR CADA MES EN LA UNIDAD {{ $unidadstr }}</strong></h2>
                    {{-- formulario de busqueda en index --}}
                    {!! Form::open(['route' => 'checar.memorandum.unidad', 'method' => 'GET', 'class' => 'form-inline']) !!}
                        <select name="busquedaMes" id="busquedaMes" class="form-control mr-sm-2">
                            <option value="">-- SELECCIONAR EL MES --</option>
                            @foreach ($meses as $key => $itemMeses)
                                <option value="{{ $key }}">{{ $itemMeses }}</option>
                            @endforeach
                        </select>
                    {{-- formulario de busqueda en index END --}}
                        {!! Form::submit('GENERAR', ['class' => 'btn btn-outline-info my-2 my-sm-0']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="pull-right">

                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">

                @if (count($queryGetMemo) > 0)
                    {{-- listado de elementos --}}

                    <table  id="table-instructor" class="table table-bordered Datatables" style="width: 100%;">
                        <caption>MEMORANDUM ENVIADOS POR MES</caption>
                        <thead class="thead-dark">
                            <tr align="justify">
                                <th>NÚMERO DE MEMORAUNDUM</th>
                                <th>TIPO DE MEMORANDUM</th>
                                <th>MEMORANDUM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queryGetMemo as $itemgetmemo)
                                <tr align="justify">
                                    <td>
                                        <p>
                                           <b>{{ $itemgetmemo->numero_memo }}</b>
                                        </p>
                                    </td>
                                    <td>
                                        <b>{{ $itemgetmemo->tipo_memo }}</b>
                                    </td>
                                    <td>
                                        <a href="{{ $itemgetmemo->ruta }}" class="btn btn-danger btn-circles btn-xl" title="MEMORANDUM DE ENVÍO A DIRECCIÓN TÉCNICA ACADÉMICA" target="_blank">
                                            <i class="far fa-file-pdf" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    {{ $queryGetMemo->appends(request()->query())->links() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- listado de elementos END --}}
                @else
                    <div style="text-align: center;">
                        <h1><b>LA CONSULTA NO ARROJÓ RESULTADOS</b></h1>
                    </div>
                @endif

                @if (count($queryGetMemoRetorno) > 0)
                    {{-- listado de elementos --}}
                    <table  id="table-instructor" class="table table-bordered Datatables" style="width: 100%;">
                        <caption>MEMORANDUM RECIBIDOS POR MES</caption>
                        <thead class="thead-dark">
                            <tr align="justify">
                                <th>NÚMERO DE MEMORAUNDUM</th>
                                <th>TIPO DE MEMORANDUM</th>
                                <th>MEMORANDUM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queryGetMemoRetorno as $itemgetretorno)
                                <tr align="justify">
                                    <td>
                                        <p>
                                           <b>{{ $itemgetretorno->numero_memo }}</b>
                                        </p>
                                    </td>
                                    <td>
                                        <b>{{ $itemgetretorno->tipo_memo }}</b>
                                    </td>
                                    <td>
                                        <a href="{{ $itemgetretorno->ruta }}" class="btn btn-danger btn-circles btn-xl" title="MEMORANDUM DE ENVÍO A DIRECCIÓN TÉCNICA ACADÉMICA" target="_blank">
                                            <i class="far fa-file-pdf" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    {{ $queryGetMemoRetorno->appends(request()->query())->links() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    {{-- listado de elementos END --}}
                @endif

        </div>
    </div>
    <br>
    {{-- spinner --}}
    <div hidden id="spinner"></div>
    {{-- spinner END --}}

    {{-- MODAL DE ENVIO --}}
    <div class="modal fade" id="modalGoBackDTA" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-info" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="enviar_cursos_dta"><b>ADJUNTAR Y REGRESAR A DIRECCIÓN TÉCNICA ACADÉMICA </b></h5>
            </div>
            <form id="formGoBackDTA" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <input type="file" name="memorandumNegativoFile" id="memorandumNegativoFile" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="send_to_dta">ENVIAR</button>
                <button type="button" id="close_btn_modal_modal_go_back_dta" class="btn btn-danger">CERRAR</button>
                </div>
            </form>
          </div>
        </div>
    </div>
    {{-- MODAL DE ENVIO END --}}

@endsection
{{-- contenido js --}}
@section('script_content_js')
    <script type="text/javascript">

    </script>
@endsection
{{-- contenido js END --}}
