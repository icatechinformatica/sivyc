@extends('theme.formatos.hlayout')
@section('title', 'Solicitud de Apertura | SIVyC Icatech')
@section('content_script_css')
    <style>         
        .tb {width: 100%; border-collapse: collapse; text-align: center; }
        .tb th{border: 1px solid black; padding: 1px; font-weight: normal; font-size: 5px;}
        .tb td{border: 1px solid black; padding: 1px; font-size: 7px; height: auto;}
        .tablaf { border-collapse: collapse; width: 100%; font-size: 8px; text-align: center; margin-top:15px;}     
        .tablaf tr, .tablaf td {padding: 0px}    
        .tablaf p {margin:5px; padding:0px}
    </style>      
@endsection
@section('content')         
        <main>
        <div class="container">
            <table style="border-collapse: collapse; text-align: right; width:100%; margin-top:-10px;" >
                <tr>
                    @if($reg_unidad->unidad=="COMITAN" || $reg_unidad->unidad=="OCOSINGO" || $reg_unidad->unidad=="SAN CRISTOBAL" || $reg_unidad->unidad=="TUXTLA" || $reg_unidad->unidad=="CATAZAJA" || $reg_unidad->unidad=="YAJALON" || $reg_unidad->unidad=="JIQUIPILAS" || $reg_unidad->unidad=="REFORMA" || $reg_unidad->unidad=="TAPACHULA" || $reg_unidad->unidad=="TONALA" || $reg_unidad->unidad=="VILLAFLORES")
                        <td><strong>Unidad de Capacitación {{$reg_unidad->unidad}}</strong></td> 
                    @else
                        <td><strong>Acción Móvil {{$reg_unidad->unidad}}</strong></td> 
                    @endif
                </tr>
                <tr>
                    <td><strong>Memorándum No. {{$memo}}</strong></td>
                </tr>
                <tr>
                    <td><strong>{{$reg_unidad->municipio_acm}}, Chiapas; {{$date}}.</strong></td>
                </tr>
            </table>
            <table style="margin-top:-25px;">
                <tr>
                    <td>PARA:</td>
                    <td>{{$reg_unidad->academico}}, {{ $reg_unidad->pacademico }}.</td>
                </tr>
                <tr>
                    <td>DE:</td>
                    <td>{{$reg_unidad->vinculacion}}, {{ $reg_unidad->pvinculacion }}.</td>
                </tr>
                <tr>
                    <td>CCP:</td>
                    <td>{{ $reg_unidad->dunidad }}, {{ $reg_unidad->pdunidad }}. <br> {{ $reg_unidad->delegado_administrativo }},{{ $reg_unidad->pdelegado_administrativo }}.</td>
                </tr>
            </table>
            <div>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Archivo/Minutario</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Por medio del presente le solicito a Usted la siguiente apertura:</p>
            </div>
            <table class="tb">
                <tr style="background: #EAECEE;">
                    <th>NÚMERO DE SOLICITUD</th>
                    <th>SERVICIO</th>
                    <th>ESPECIALIDAD</th>
                    <th>NOMBRE</th>
                    <th>MOD</th>
                    <th>TIPO</th>
                    <th>DURA</th>
                    <th>FECHA DE INICIO</th>
                    <th>FECHA DE TERMINO</th>
                    <th>HORARIO</th>
                    <th>DIAS</th>
                    <th>HRS. POR DIA</th>
                    <th>COSTO POR PART</th>
                    <th>TOTAL INGRESO</th>
                    <th>NO. PART</th>
                    <th>H</th>
                    <th>M</th>
                    <th>MEMORÁNDUM DE AUTORIZACIÓN DE EXO/REDUC</th>
                    <th>CONV. GRAL</th>
                    <th>CONVENIO ESPECIFICO /ACTA DE ACUERDO</th>
                    <th>DEPENDENCIA</th>
                    <th>REPRESENTANTE /TELÉFONO</th>                    
                    <th>NOMBRE DEL INSTRUCTOR</th>
                    <th>CURSO VINCULADO POR</th>
                    <th>ESPACIO FISICO</th>
                    <th>OBSERVACIONES</th>
                </tr>
                @foreach ($data as $item)
                    <tr>
                        <td>{{$item['folio_grupo']}}</td>
                        <td>{{$item['tipo_curso']}}</td>
                        <td>{{$item['espe']}}</td>
                        <td>{{$item['curso']}}</td>
                        <td>{{$item['mod']}}</td>
                        <td>{{$item['tcapacitacion']}}</td>
                        <td>{{$item['dura']}}</td>
                        <td>{{$item['inicio']}}</td>
                        <td>{{$item['termino']}}</td>
                        <td>{{$item['horario']}} HRS.</td>
                        <td>{{$item['dia']}}</td>
                        <td>{{$item['horas']}}</td>
                        <td>{{$item['costos']}}</td>
                        <td>{{$item['costo']}}</td>
                        <td>{{$item['tpar']}}</td>
                        <td  >{{$item['hombre']}}</td>
                        <td>{{$item['mujer']}}</td>
                        <td >@if ($item['mexoneracion'])  {{ substr($item['mexoneracion'],1,12)}}  {{ substr($item['mexoneracion'],13,strlen($item['mexoneracion']))}}   @else {{"N/A"}}  @endif</td>
                        <td>@if ($item['cgeneral']!='0') {{ substr($item['cgeneral'],1,5)}} {{ substr($item['cgeneral'],6,strlen($item['cgeneral']))}}@else {{"N/A"}} @endif</td>
                        <td>@if ($item['cespecifico']) {{$item['cespecifico']}} @else {{"N/A"}} @endif </td>
                        <td>{{$item['depen']}}</td>
                        <td>{{$item['depen_repre']}} / {{$item['tel_repre']}}</td>                       
                        <td>{{$item['instructor']}}</td>
                        <td>{{$item['vincu']}}</td>
                        <td>{{$item['efisico']}}</td>
                        <td width='150px'>{{$item['observaciones']}}</td>
                    </tr>
                @endforeach
            </table>
            <div>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sin más por el momento, le envío un cordial saludo.</p>
            </div>
            <table class="tablaf">
                <tr>
                    <td>
                        <p>SOLICITA</p><br><br><br><br><br>
                        <p>{{ $reg_unidad->vinculacion }}</p> 
                        <p>_____________________________________________________</p>
                        <p>{{ $reg_unidad->pvinculacion }}</p>
                    </td>
                    <td>
                        <p>VALIDA</p><br><br><br><br><br>
                        <p>{{ $reg_unidad->academico }}</p>
                        <p>_____________________________________________________</p>
                        
                        <p>{{ $reg_unidad->pacademico }}</p>
                    </td>
                    <td>
                        <p>Vo. Bo.</p><br><br><br><br><br>
                        <p>{{ $reg_unidad->dunidad }}</p>
                        <p>_____________________________________________________</p>
                        <p>{{ $reg_unidad->pdunidad }}</p>
                    </td>
                </tr>
            </table>            
        </div>
    </main>
@endsection
@section('script_content_js') 
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 530, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection