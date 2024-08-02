<!DOCTYPE html>
<html lang="en">
<head>
    @if(!is_null($uuid))
        <style>
            body{
            font-family: sans-serif;
            }
            @page {
                margin: 30px 60px, 60px, 60px;
            }
            header { position: fixed;
                left: 0px;
                top: -155px;
                right: 0px;
                height: 50px;
                background-color: #ddd;
                text-align: center;
            }
            header h1{
                margin: 1px 0;
            }
            header h2{
                margin: 0 0 1px 0;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: 0px;
                right: 0px;
                height: 10px;
                text-align: center;
            }
            footer .page:after {
                content: counter(page);
            }
            footer table {
                width: 100%;
            }
            footer p {
                text-align: right;
            }
            footer .izq {
                text-align: left;
            }
            table, td {
                    border:0px solid black;
                    }
            table {
                border-collapse:collapse;
                width:100%;
            }
            td {
                padding:0px;
            }
            .page-number:after {
                float: right;
                font-size: 10px;
                /* display: inline-block; */
                content: "Pagina " counter(page) " de 5";
            }
            .link {
                position: fixed;
                left: 0px;
                top: 8px;
                font-size: 7px;
                text-align: left;
            }
        </style>
    @else
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 30px 60px, 60px, 60px;
            }
            header { position: fixed;
                left: 0px;
                top: -155px;
                right: 0px;
                height: 50px;
                background-color: #ddd;
                text-align: center;
            }
            header h1{
                margin: 1px 0;
            }
            header h2{
                margin: 0 0 1px 0;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: 0px;
                right: 0px;
                height: 10px;
                text-align: center;
            }
            footer .page:after {
                content: counter(page);
            }
            footer table {
                width: 100%;
            }
            footer p {
                text-align: right;
            }
            footer .izq {
                text-align: left;
            }
            table, td {
                    border:0px solid black;
                    }
            table {
                border-collapse:collapse;
                width:100%;
            }
            td {
                padding:0px;
            }
            .page-number:before {
                content: "Pagina " counter(page);
            }
        </style>
    @endif
</head>
    <body>
        <footer>
            @if($firma_electronica == true)
                <div class="page-number">
                    <small class="link">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} <br> Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas </small>
                </div>
            @else
                <div class="page-number"></div>
            @endif
        </footer>
        <div class= "container g-pt-30" style="font-size: 12px; margin-bottom: 25px;">
            <div id="content">
                    {!! $body_html !!}
                @if($firma_electronica == true)
                    @if(!is_null($uuid))
                        <div>
                            <table style="font:9px;">
                                @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                                    @php $esInstructor = TRUE; @endphp
                                    @if($key == 2)
                                    <tr><td height="10px;"></td></tr>
                                    @endif
                                    <tr>
                                        <td width="100px;"><b>Nombre del firmate:</b></td>
                                        <td height="25px;">{{$moist['_attributes']['nombre_firmante']}}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;"><b>Firma Electronica:</b></td>
                                        <td>{{wordwrap($moist['_attributes']['firma_firmante'], 50, "\n", true) }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Puesto:</b></td>
                                        <td height="25px;">{{$puestos[$key]}}</td>
                                        {{-- @foreach($dataFirmantes as $search_puesto)
                                            @if($search_puesto->curp == $moist['_attributes']['curp_firmante'])
                                                @php $esInstructor = FALSE; @endphp
                                                <td height="25px;">{{$search_puesto->cargo}}</td>
                                                @break
                                            @endif
                                        @endforeach
                                        @if($esInstructor == TRUE)
                                            <td height="25px;">Instructor</td>
                                        @endif --}}
                                    </tr>
                                    <tr>
                                        <td><b>Fecha de Firma:</b></td>
                                        <td>{{$moist['_attributes']['fecha_firmado_firmante']}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Numero de Serie:</b></td>
                                        <td>{{$moist['_attributes']['no_serie_firmante']}}</td>
                                    </tr>

                                @endforeach
                            </table></small>
                    @endif
                    <table style="font:10px;">
                        <tr>
                            <td width="45px;">@if(!is_null($uuid))<img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">@endif</td>
                            <td style="vertical-align: top;" width="25px;">@if(!is_null($uuid))<br><b>Folio:</b>@endif</td>
                            <td style="vertical-align: top; text-align: justify;">
                                <br>{{$uuid}}<br><br><br>
                                Las Firmas electrónicas que anteceden corresponden al Contrato de prestación de servicios profesionales modalidad de  @if($data->tipo_curso=='CURSO') horas curso @else   certificación extraordinaria @endif No. {{$data_contrato->numero_contrato}}, que celebran por una parte el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, representado por el (la) C. {{$funcionarios['director']}}, {{$funcionarios['directorp']}}, y el (la) C. {{$nomins}}, en el Municipio de {{$data_contrato->municipio}}.
                            </td>
                        </tr>
                    </table>
                    </div>
                @else
                    <table>
                        <tr>
                            <td colspan="2"><p align="center"><b>"ICATECH"</b></p></td>
                            <td colspan="2"><p align="center"><b>"PRESTADOR DE SERVICIOS"</b></p></td>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center"><br><br></td></div>
                            <td colspan="2"><div align="center"><br><br></td></div>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center"><b>{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}</b></td></div>
                            <td colspan="2"><div align="center"><b>C. {{$nomins}}</b></td></div>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center"><b>{{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</b></td></div>
                            <td colspan="2"><div align="center"></td></div>
                        </tr>
                    </table>
                    <p align="center"><b>"TESTIGOS"</b></p>
                    <br><br><br><br>
                    <table>
                        <tr>
                            <td colspan="2"><p align="center"></p></td>
                            <td colspan="2"><p align="center"></p></td>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center"><b>{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}</b></td></div>
                            <td colspan="2"><div align="center"><b>{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}</b></td></div>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center"><b>{{$testigo1->puesto}}</b></td></div>
                            <td colspan="2"><div align="center"><b>{{$testigo3->puesto}}</b></td></div>
                        </tr>
                    </table>
                    {{-- <div align=center>
                        <br>
                        <br/>
                        <br>________________________________________
                        <br><small><b>{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}</b></small>
                        <br><small><b>{{$testigo3->puesto}}</b> </small></b>
                    </div> --}}
                    <br>
                    <div align=justify>
                        <small  style="font-size: 10px;">Las Firmas que anteceden corresponden al Contrato de prestación de servicios profesionales en su modalidad de @if($data->tipo_curso=='CURSO') horas curso @else   certificación extraordinaria @endif No. {{$data_contrato->numero_contrato}}, que celebran por una parte el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, representado por el (la) C. {{$funcionarios['director']}}, {{$funcionarios['directorp']}}, y el (la) C. {{$nomins}}, en el Municipio de {{$data_contrato->municipio}}.</small>
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
