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
                    <h2> <strong>Módulo de Seguimiento y Avance de las Unidades en el tema de formato T</strong></h2>
                    {{-- formulario de busqueda en index --}}
                    {!! Form::open(['route' => 'seguimento.avance.unidades.formatot.ejecutiva.index', 'method' => 'GET', 'class' => 'form-inline']) !!}
                        <select name="busquedaMes" id="busquedaMes" class="form-control mr-sm-2">
                            <option value="">-- SELECCIONAR EL MES --</option>
                            @foreach ($meses as $key => $itemMeses)
                                <option value="{{ $itemMeses }}">{{ $itemMeses }}</option>
                            @endforeach
                        </select>
                        {{-- selector de busqueda por unidades--}}
                        <select name="busquedaPorUnidad" id="busquedaPorUnidad" class="form-control mr-sm-2">
                            <option value="">--SELECCIONAR LA UNIDAD--</option>
                            @foreach ($unidadesIcatech as $itemUnidades)
                                <option value="{{ $itemUnidades->ubicacion }}">{{ $itemUnidades->ubicacion }}</option>
                            @endforeach
                        </select>
                        {{-- selector de busqueda por unidades END --}}
                        
                        
                    {{-- formulario de busqueda en index END --}}
                        {!! Form::submit('CONSULTAR', ['class' => 'btn btn-outline-info my-2 my-sm-0']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="pull-right">

                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <span class="badge badge-pill badge-success">ENTREGADO</span>
        <span class="badge badge-pill badge-warning">NO ENTREGADO</span>
        <br><br>
        <div class="form-row">
            {{-- tabla --}}
            <table class="table">
                <caption>AVANCES de las UNIDADES EN EL FORMATO T MES DE {{ $mesActual }}</caption>
                <thead class="thead-light">
                    <tr>
                        <th scope="col">UBICACIÓN</th>
                        <th scope="col">CURSOS TOTALES</th>
                        <th scope="col">CURSOS NO REPORTADOS</th>
                        <th scope="col">TURNADOS A DTA</th>
                        <th scope="col">TURNADOS A PLANEACIÓN</th>
                        <th scope="col">REPORTADOS</th>
                        <th scope="col">% DE ENTREGA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($query_entrega_contable_fotmatot as $itemEntregaContrableFormatoT)
                    <tr class="{{ ($itemEntregaContrableFormatoT->porcentaje >= 50.00) ? 'table-success' : 'table-warning' }}">
                        <th>{{ $itemEntregaContrableFormatoT->ubicacion }}</th>
                        <td>{{ $itemEntregaContrableFormatoT->total_cursos }}</td>
                        <td>{{ $itemEntregaContrableFormatoT->no_reportado_unidad }}</td>
                        <td>{{ $itemEntregaContrableFormatoT->turnado_dta }}</td>
                        <td>{{ $itemEntregaContrableFormatoT->turnado_planeacion }}</td>
                        <td>{{ $itemEntregaContrableFormatoT->reportado }}</td>
                        <td>{{ $itemEntregaContrableFormatoT->porcentaje }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- fin tabla --}}
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
