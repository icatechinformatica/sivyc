@extends('theme.formatos.mlayout'.$layout_año)
@section('title', 'RECIBO DE PAGO | SIVyC Icatech')
@section('css')
    <style>
        h1 { width: 100%; text-align:center;}
        table { border: 1px solid; width:90%; margin-bottom: 5px; border-spacing: 0;
            margin: 0 auto 5px;
            border-spacing: 0;
        }
        table tr td{ font-size: 13px; color:black; padding: 4px; }
        .negro {  background-color:black; font-size: 14px; color:white; font-weight:bold; padding-left:7px; width:auto; margin: 0px; }
        .bold {  font-size: 14px; font-weight:bold;}
        .rojo {  font-size: 18px; color:red; font-weight:bold; width:auto; }
    </style>
@endsection
@section('content')
        <h1 style="margin-top: -10px;">UNIDAD DE CAPACITACIÓN {{$data->ubicacion}}</h1>
        <table>
            <tr>
            <td class="negro" style="width:15%;">RECIBO No.</td><td style="width:33%;"> <span class="bold" style="font-size: 18px;"> {{$data->uc}}</span> <span class="rojo">{{ str_pad($data->num_recibo, 4, "0", STR_PAD_LEFT) }}</span></td>
            <td class="negro" style="width:17%;">BUENO POR &nbsp;$</td><td style="width:20%; font-size: 18px;">{{ number_format($data->costo, 2, ".", ",")}}</td>
            <td class="negro" style="width:15%;">{{$data->status_recibo}}</td>
        </tr>
        </table>
        <table>
            <tr><td class="negro" style="width:15%;">RECIBÍ DE:</td><td> {{ $data->recibide }}</td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:21%;">LA CANTIDAD DE:</td><td> {{ $data->importe_letra }} </td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:33%;">POR CONCEPTO DE PAGO DE:</td><td></td></tr>
            <tr ><td colspan="2" style="height:40px; padding: 0 10px;">
                    {{$data->concepto}}
            </td></tr>
        </table>
        <table>
            <tr><td class="negro" style="width:33%;">INFORMACIÓN GENERAL:</td><td></td></tr>
            <tr>
                <td colspan="2" style="height:90px; padding: 0 10px 0 10px; line-height:18px;">
                    @if($data->id_concepto>1)
                        @if($data->alumno)  <b> ALUMNO: </b> {{ $data->alumno }} &nbsp; @endif
                    @endif
                    @if($data->folio_grupo and $data->id_concepto==1)  <b> FOLIO DE GRUPO: </b> {{ $data->folio_grupo }} &nbsp; @endif
                    @if(in_array($data->id_concepto,[2,4])) <b>CLAVE:</b> {{ $data->clave }} @endif
                    @if(in_array($data->id_concepto,[1,2,4])) <br/><b>{{ $data->tipo_curso }}:</b> {{ $data->curso }}.<br/> @endif

                    @if($data->id_concepto==1) <b> TOTAL BENEFICIADOS: </b> {{ $data->hombre+$data->mujer}} @endif
                    @if($data->id_concepto>1)
                        @if($data->id_concepto<>9) <b>DESCRIPCIÓN:</b> @endif  {!! nl2br($data->descripcion) !!}
                        @if(isset($data->constancias)) {{ $data->cantidad==1 ?  "CORRESPONDIENTE AL FOLIO NÚMERO: " : "CORRESPONDIENTE A LOS FOLIOS:" }} {{$data->constancias}}. @endif
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="negro" style="text-align:center;">LUGAR DE EXPEDICIÓN:</td>
                <td class="negro" style="text-align:center;">FECHA:</td>
                <td class="negro" style="text-align:center;">NOMBRE Y FIRMA DE RECIBIDO:</td>
            </tr>
            <tr>
                <td style=" border: 1px solid; text-align:center;">{{ $data->municipio }}</td>
                <td style=" border: 1px solid; text-align:center;">{{ $fecha }}</td>
                <td style=" border: 1px solid; text-align:center; height:55px;">{{ $data->recibio }}</td>
            </tr>

        </table>
@endsection

