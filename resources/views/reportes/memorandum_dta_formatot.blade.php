{{-- creado por MIS DANIEL MÉNDEZ CRUZ --}}
@extends('theme.sivyc.layout')
@section('title', 'Turnados a DTA | SIVYC ICATECH')
@section('content_script_css')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
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
@section('content')
    <div class="card-header">
        FormatoT / Memorándums Turnados a la DTA
    </div>
    <div class="card card-body">    
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
                    {{-- formulario de busqueda en index --}}
                    {!! Form::open(['route' => 'checar.memorandum.dta.mes', 'method' => 'GET', 'class' => 'form-inline']) !!}
                        <select name="busquedaMes" id="busquedaMes" class="form-control mr-sm-2">
                            <option value="">-- SELECCIONAR EL MES --</option>
                            @foreach ($meses as $key => $itemMeses)
                                <option value="{{ $key }}">{{ $itemMeses }}</option>
                            @endforeach
                        </select>
                        {{-- selector de busqueda por unidades--}}
                        <select name="busquedaPorUnidad" id="busquedaPorUnidad" class="form-control mr-sm-2">
                            <option value="">--SELECCIONAR LA UNIDAD--</option>
                            @foreach ($unidadstr as $itemUnidades)
                                <option value="{{ $itemUnidades->ubicacion }}">{{ $itemUnidades->ubicacion }}</option>
                            @endforeach
                        </select>
                        {{-- selector de busqueda por unidades END --}}


                    {{-- formulario de busqueda en index END --}}
                        {!! Form::submit('FILTRAR', ['class' => 'btn my-2 my-sm-0']) !!}
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
                        <caption>MEMORANDUM RECIBIDOS Y ENVIADOS POR MES</caption>
                        <thead class="thead-dark">
                            <tr align="justify">
                                <th>NÚMERO DE MEMORANDUM</th>
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
                    <div class="col-12">                        
                        <h4 class="text-center">NO SE ENCONTRARON REGISTROS</h4>
                    </div>
                @endif

        </div>
    </div>
    <br>

@endsection
{{-- contenido js --}}
@section('script_content_js')
    <script type="text/javascript">

    </script>
@endsection
{{-- contenido js END --}}
