@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
        td a{
            display: block;
            box-sizing:border-box;
            height: 100%;
            width: 100%;
        }
        thead tr th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
        .table-responsive {
            height:400px;
            overflow:auto;
            text-align: center;
            padding: 10px;
        }
        .table-responsive2 {
            height:400px;
            overflow:visible;
            text-align: center;
        }
        .container2 {
        position: relative;
        }
        .child {
            width: 98%;
        /* Center vertically and horizontally */
        position: absolute;
        top: 50%;
        left: 6%;
        text-align: center;
        margin: -25px 0 0 -25px; /* apply negative top and left margins to truly center the element */
        }
        .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
        }

        /* Style the buttons inside the tab */
        .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
        background-color: #ddd;
        }

        /* Create an active/current tablink class */
        .tab button.active {
        background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
        animation: fadeEffect 1s;
        }

        @keyframes fadeEffect {
            from {opacity: 0;}
            to {opacity: 1;}
        }
    </style>
    <div class="card-header">
        Solicitud de Reportes Solicitados Detallado
    </div>
    <div class="card card-body" >
        <br />
        <div class="row justify-content-center">
                <H2>Consulta de Solicitados Mediante Suficiencias Presupuestales Unidad: {{$un}}</H2>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputccp"><h3>Filtrado Por Fechas</h3></label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                @if($ini == 0 || $fin == 0)
                    <h4>De manera General</h4>
                @else
                    <h4>{{$ini}} AL {{$fin}}</h4>
                @endif
            </div>
        </div>
        <br>
        <div class="row">
            <div class="table-responsive2" style="width:33%;" style="text-align: center;">
                <div class="alert alert-warning" style="text-align: center; left: 130px; top: 425px; position: absolute;" id='nullsp'>
                    <strong>Info!</strong> Datos Insuficientes
                </div>
                <label for="myChart" >Resumen de Solicitudes de Suficiencias Presupuestales</label>
                <canvas id="myChart" style="position: relative; height:40vh; width:80vw"></canvas>
            </div>
            <div class="table-responsive2" style="width:33%;" style="text-align: center;">
                <label for="myChart2" >Resumen de Solicitudes de Contratos</label>
                <canvas id="myChart2" style="position: relative; height:40vh; width:80vw"></canvas>
                <div class="alert alert-warning" style="text-align: center;  position: absolute; left: 525px; top: 425px; z-index: 20;" id='nullc'>
                    <strong>Info!</strong> Datos Insuficientes
                </div>
            </div>
            <div class="table-responsive2" style="width:33%;" style="text-align: center;">
                <label for="myChart3" >Resumen de Solicitudes de Pagos</label>
                <canvas id="myChart3" style="position: relative; height:40vh; width:80vw"></canvas>
                <div class="alert alert-warning" style="text-align: center;  position: absolute; left: 920px; top: 425px; z-index: 20;" id='nullp'>
                    <strong>Info!</strong> Datos Insuficientes
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div class="tab">
            <button class="tablinks" onclick="openCity(event, 'suficiencias_presupuestales')" id="defaultOpen">Suficiencias Presupuestales</button>
            <button class="tablinks" onclick="openCity(event, 'contratos')">Contratos</button>
            <button class="tablinks" onclick="openCity(event, 'pagos')">Pagos</button>
        </div>
        <div id="suficiencias_presupuestales" class="tabcontent">
            <div class="row justify-content-center">
                <H2>Suficiencias Presupuestales</H2>
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Proceso</H2>
            </div>
            <div class="row">
                @if($consulta1->supre_memo_proceso[0] == "")
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive" >
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consulta1->supre_memo_proceso as $key => $proceso)
                                    <tr>
                                        <td align="center">{{$proceso}}</td>
                                        <td align="center">{{$consulta1->supre_updated_proceso[$key]}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Solicitudes Validadas</H2>
            </div>
            <div class="row">
                @if($cadwell[0] == null)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Validación</th>
                                    <th style="text-align: center;">No. Memorándum de Validación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell as $key => $validado)
                                    <tr>
                                        <td align="center">{{$validado->no_memo}}</td>
                                        <td align="center">{{$validado->updated_at}}</td>
                                        <td align="center">{{$validado->folio_validacion}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Rechazados</H2>
            </div>
            <div class="row">
                @if($consulta1->supre_memo_rechazo[0] == "")
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id='table_supre_cancelado'>
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Rechazo</th>
                                    <th style="text-align: center;">Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consulta1->supre_memo_rechazo as $key => $rechazo)
                                    <tr>
                                        <td align="center">{{$rechazo}}</td>
                                        @if($consulta1->supre_fecha_rechazo[$key] != 'NULL')
                                            <td align="center">{{$consulta1->supre_fecha_rechazo[$key]}}</td>
                                        @else
                                            <td align="center">{{$consulta1->supre_updated_rechazo[$key]}}</td>
                                        @endif
                                        @if($consulta1->supre_observaciones[$key] != 'NULL')
                                            <td align="center">{{$consulta1->supre_observaciones[$key]}}</td>
                                        @else
                                            <td align="center">N/A</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div id="contratos" class="tabcontent">
        <div class="row justify-content-center">
            <H2>Contratos</H2>
        </div>
        <div class="row justify-content-center">
            <H2>Lista de Proceso</H2>
        </div>
            <div class="row">
                <?php $chk= false; foreach($cadwell2 as $prob){if($prob->status == 'Validando_Contrato'){$chk = true;}}?>
                @if(empty($cadwell2[0]) || $chk == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Contrato</th>
                                    <th style="text-align: center;">Curso</th>
                                    <th style="text-align: center;">Instructor</th>
                                    <th style="text-align: center;">Fecha de Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell2 as $key => $proceso)
                                    @if($proceso->status == 'Validando_Contrato')
                                        <tr>
                                            <td align="center">{{$proceso->unidad}}</td>
                                            <td align="center">{{$proceso->numero_contrato}}</td>
                                            <td align="center">{{$proceso->curso}}</td>
                                            <td align="center">{{$proceso->nombre}}</td>
                                            <td align="center">{{$proceso->updated_at}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Contratos Validados</H2>
            </div>
            <div class="row">
                <?php $chk2= false; foreach($cadwell2 as $prob){if($prob->status == 'Contratado'){$chk2 = true;}}?>
                @if(empty($cadwell2[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Contrato</th>
                                    <th style="text-align: center;">Curso</th>
                                    <th style="text-align: center;">Instructor</th>
                                    <th style="text-align: center;">Fecha de Validación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell2 as $key => $validado)
                                    @if($validado->status == 'Contratado')
                                        <tr>
                                            <td align="center">{{$validado->unidad}}</td>
                                            <td align="center">{{$validado->numero_contrato}}</td>
                                            <td align="center">{{$validado->curso}}</td>
                                            <td align="center">{{$validado->nombre}}</td>
                                            <td align="center">{{$validado->updated_at}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Contratos Rechazados</H2>
            </div>
            <div class="row">
                <?php $chk2= false; foreach($cadwell2 as $prob){if($prob->status == 'Contrato_Rechazado'){$chk2 = true;}}?>
                @if(empty($cadwell2[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id='table_supre_cancelado'>
                            <thead>
                                <tr>
                                <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Contrato</th>
                                    <th style="text-align: center;">Curso</th>
                                    <th style="text-align: center;">Instructor</th>
                                    <th style="text-align: center;">Fecha de Cancelación</th>
                                    <th style="text-align: center;">Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell2 as $key => $rechazo)
                                    @if($rechazo->status == 'Contrato_Rechazado')
                                        <tr>
                                            <td align="center">{{$validado->unidad}}</td>
                                            <td align="center">{{$rechazo->numero_contrato}}</td>
                                            <td align="center">{{$rechazo->curso}}</td>
                                            <td align="center">{{$rechazo->nombre}}</td>
                                            <td align="center">{{$rechazo->updated_at}}</td>
                                            @if($rechazo->observacion != NULL)
                                                <td align="center">{{$rechazo->observacion}}</td>
                                            @else
                                                <td align="center">N/A</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div id="pagos" class="tabcontent">
            <div class="row justify-content-center">
                <H2>pagos</H2>
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Proceso</H2>
            </div>
            <div class="row">
                <?php $chk2= false; foreach($cadwell3 as $prob){if($prob->status == 'Verificando_Pago'){$chk2 = true;}}?>
                @if(empty($cadwell3[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Creación</th>
                                    <th style="text-align: center;">Líquido de Factura</th>
                                    <th style="text-align: center;">Importe</th>
                                    <th style="text-align: center;">IVA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell3 as $key => $proceso)
                                    @if($proceso->status == 'Verificando_Pago')
                                        <tr>
                                            <td align="center">{{$proceso->unidad}}</td>
                                            <td align="center">{{$proceso->no_memo}}</td>
                                            <td align="center">{{$proceso->created_at}}</td>
                                            <td align="center">{{$proceso->liquido}}</td>
                                            <td align="center">{{$proceso->importe_total}}</td>
                                            <td align="center">{{$proceso->iva}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Validados</H2>
            </div>
            <div class="row">
            <?php $chk2= false; foreach($cadwell3 as $prob){if($prob->status == 'Pago_Verificado'){$chk2 = true;}}?>
                @if(empty($cadwell3[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id='table_supre_cancelado'>
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Validación</th>
                                    <th style="text-align: center;">Líquido de Factura</th>
                                    <th style="text-align: center;">Importe</th>
                                    <th style="text-align: center;">IVA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell3 as $key => $validado)
                                    @if($validado->status == 'Pago_Verificado')
                                        <tr>
                                            <td align="center">{{$validado->unidad}}</td>
                                            <td align="center">{{$validado->no_memo}}</td>
                                            <td align="center">{{$validado->updated_at}}</td>
                                            <td align="center">{{$validado->liquido}}</td>
                                            <td align="center">{{$validado->importe_total}}</td>
                                            <td align="center">{{$validado->iva}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Pagados</H2>
            </div>
            <div class="row">
                <?php $chk2= false; foreach($cadwell3 as $prob){if($prob->status == 'Finalizado'){$chk2 = true;}}?>
                @if(empty($cadwell3[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Pago</th>
                                    <th style="text-align: center;">Líquido de Factura</th>
                                    <th style="text-align: center;">Importe</th>
                                    <th style="text-align: center;">IVA</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($cadwell3 as $key => $finalizado)
                                    @if($finalizado->status == 'Finalizado')
                                        <tr>
                                            <td align="center">{{$finalizado->unidad}}</td>
                                            <td align="center">{{$finalizado->no_memo}}</td>
                                            <td align="center">{{$finalizado->updated_at}}</td>
                                            <td align="center">{{$finalizado->liquido}}</td>
                                            <td align="center">{{$finalizado->importe_total}}</td>
                                            <td align="center">{{$finalizado->iva}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="row justify-content-center">
                <H2>Lista de Rechazados</H2>
            </div>
            <div class="row">
                <?php $chk2= false; foreach($cadwell3 as $prob){if($prob->status == 'Pago_Rechazado'){$chk2 = true;}}?>
                @if(empty($cadwell3[0]) || $chk2 == false)
                    <div class="container2" style="width:95%;">
                        <div class="alert alert-warning child">
                            <strong>Info!</strong> No hay Registros
                        </div>
                    </div>
                    <br><br><br><br>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id='table_supre_cancelado'>
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Unidad</th>
                                    <th style="text-align: center;">No. Memorándum</th>
                                    <th style="text-align: center;">Fecha de Rechazo</th>
                                    <th style="text-align: center;">Líquido de Factura</th>
                                    <th style="text-align: center;">Importe</th>
                                    <th style="text-align: center;">IVA</th>
                                    <th style="text-align: center;">Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cadwell3 as $key => $rechazo)
                                    @if($rechazo->status == 'Pago_Rechazado')
                                        <tr>
                                            <td align="center">{{$finalizado->unidad}}</td>
                                            <td align="center">{{$finalizado->no_memo}}</td>
                                            <td align="center">{{$finalizado->updated_at}}</td>
                                            <td align="center">{{$finalizado->liquido}}</td>
                                            <td align="center">{{$finalizado->importe_total}}</td>
                                            <td align="center">{{$finalizado->iva}}</td>
                                            @if($finalizado->observacion != NULL)
                                                <td align="center">{{$finalizado->observacion}}</td>
                                            @else
                                                <td align="center">N/A</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <input hidden type="number" name="supre_rechazados" id="supre_rechazados" value="{{$consulta1->supre_rechazados}}">
    <input hidden type="number" name="supre_proceso" id="supre_proceso" value="{{$consulta1->supre_proceso}}">
    <input hidden type="number" name="supre_validados" id="supre_validados" value="{{$consulta1->supre_validados}}">
    <input hidden type="number" name="contrato_rechazados" id="contrato_rechazados" value="{{$consulta2->contrato_rechazados}}">
    <input hidden type="number" name="contrato_proceso" id="contrato_proceso" value="{{$consulta2->contrato_proceso}}">
    <input hidden type="number" name="contrato_validados" id="contrato_validados" value="{{$consulta2->contrato_validados}}">
    <input hidden type="number" name="pago_rechazados" id="pago_rechazados" value="{{$consulta2->pago_rechazados}}">
    <input hidden type="number" name="pago_proceso" id="pago_proceso" value="{{$consulta2->pago_proceso}}">
    <input hidden type="number" name="pago_validados" id="pago_validados" value="{{$consulta2->pago_validados}}">
    <input hidden type="number" name="pago_finalizados" id="pago_finalizados" value="{{$consulta2->pago_finalizados}}">
@endsection
@section('script_content_js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
        <script>
            var rechazados = document.getElementById('supre_rechazados').value;
            var proceso = document.getElementById('supre_proceso').value;
            var validados = document.getElementById('supre_validados').value;
            var total = rechazados + proceso + validados;
            if (total != 0)
            {
                $('#nullsp').prop("class", "d-none d-print-none");
            }
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Rechazados', 'En Proceso', 'Validados'],
                    datasets: [{
                        label: 'Resumen de Solicitudes de Suficiencias Presupuestales',
                        data: [rechazados, proceso, validados],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    radius: ['100%'],
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
            var crechazados = document.getElementById('contrato_rechazados').value;
            var cproceso = document.getElementById('contrato_proceso').value;
            var cvalidados = document.getElementById('contrato_validados').value;
            var ctotal = crechazados + cproceso + cvalidados;
            if (ctotal != 0)
            {
                $('#nullc').prop("class", "d-none d-print-none");
            }
            var ctx2 = document.getElementById('myChart2').getContext('2d');
            var myChart2 = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Rechazados', 'En Proceso', 'Validados'],
                    datasets: [{
                        label: 'Resumen de Solicitudes de Suficiencias Presupuestales',
                        data: [crechazados, cproceso, cvalidados],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    radius: ['100%'],
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
            var prechazados = document.getElementById('pago_rechazados').value;
            var pproceso = document.getElementById('pago_proceso').value;
            var pvalidados = document.getElementById('pago_validados').value;
            var pfinalizados = document.getElementById('pago_finalizados').value;
            var ptotal = prechazados + pproceso + pvalidados;
            if (ptotal != 0)
            {
                $('#nullp').prop("class", "d-none d-print-none");
                console.log('hola');
            }
            var ctx3 = document.getElementById('myChart3').getContext('2d');
            var myChart3 = new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: ['Rechazados', 'En Proceso', 'Validados', 'Pagados'],
                    datasets: [{
                        label: 'Resumen de Solicitudes de Suficiencias Presupuestales',
                        data: [prechazados, pproceso, pvalidados, pfinalizados],
                        //data: [0,0,0,0],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    radius: ['100%'],
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            function openCity(evt, cityName) {
                // Declare all variables
                var i, tabcontent, tablinks;

                // Get all elements with class="tabcontent" and hide them
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                // Get all elements with class="tablinks" and remove the class "active"
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }

                // Show the current tab, and add an "active" class to the button that opened the tab
                document.getElementById(cityName).style.display = "block";
                evt.currentTarget.className += " active";
            }
            document.getElementById("defaultOpen").click();
        </script>

@endsection
<!--a-->
