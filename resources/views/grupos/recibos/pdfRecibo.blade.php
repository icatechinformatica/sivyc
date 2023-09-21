@extends('theme.formatos.mlayout')
@section('title', 'RECIBO DE PAGO | SIVyC Icatech')
@section('css')
    <style>   
        h1 { width: 100%; text-align:center;}
        table { border: 1px solid; width:100%; margin-bottom: 5px; border-spacing: 0; }
        table tr td{ text-align:justify; font-size: 13px; color:black; padding: 4px; }
        .negro {  background-color:black; font-size: 14px; color:white; font-weight:bold; padding-left:7px; width:auto; margin: 0px; }
        .bold {  font-size: 14px; font-weight:bold;}
        .rojo {  font-size: 18px; color:red; font-weight:bold; width:auto; }        
    </style>    
@endsection
@section('body')    
        <h1>UNIDAD DE CAPACITACIÓN {{$data->ubicacion}}</h1>    
        <table> 
            <tr>
            <td class="negro" style="width:15%;">RECIBO No.</td><td style="width:33%;"> <span class="bold" style="font-size: 18px;"> {{$data->uc}}</span> <span class="rojo">{{$data->folio_recibo}}</span></td>
            <td class="negro" style="width:17%;">BUENO POR &nbsp;$</td><td style="width:35%; font-size: 18px;">{{ number_format($data->costo, 2, ".", ",")}}</td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:15%;">RECIBÍ DE:</td><td> {{ $data->recibide }}</td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:21%;">LA CANTIDAD DE:</td><td> {{ $data->importe_letra }} </td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:33%;">POR CONCEPTO DE PAGO DE:</td><td></td></tr>
            <tr ><td colspan="2"> {{ $data->tipo_curso }}.- {{ $data->curso }}  </td></tr>
        </table>
        <table>
            <tr><td class="negro">INFORMACIÓN GENERAL:</td><td></td></tr>
            <tr>
                <td colspan="2"> <span class="bold"> FOLIO DE GRUPO: </span> {{ $data->folio_grupo }}</td>
                <td colspan="2"> <span class="bold"> TOTAL BENEFICIADOS: </span> {{ $data->hombre+$data->mujer}} </td>
            </tr>
        </table>
        <table>            
            <tr>
                <td class="negro" style="text-align:center;">LUGAR DE EXPEDICIÓN:</td>
                <td class="negro" style="text-align:center;">FECHA:</td>
                <td class="negro" style="text-align:center;">NOMBRE Y FIRMA DE RECIBIDO:</td>
            </tr>
            <tr>
                <td style=" border: 1px solid; text-align:center;"><br/> {{ $data->municipio }} <br/> &nbsp;</td>
                <td style=" border: 1px solid; text-align:center;">{{ $fecha }}</td>
                <td style=" border: 1px solid; text-align:center; height:75px;"><br/>{{ $data->recibio }}</td>
            </tr>
            
        </table>
@endsection

