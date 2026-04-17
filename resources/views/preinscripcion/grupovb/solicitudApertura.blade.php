@extends('theme.formatos.hlayout'.$layout_año)
@section('title', 'Solicitud de Apertura | SIVyC Icatech')
@section('content_script_css')
    <style>
         /* @page { margin-bottom: 107px; } */
        .tb {width: 100%; border-collapse: collapse; text-align: center; }
        .tb th{border: 1px solid black; padding: 1px; font-weight: normal; font-size: 5px;}
        .tb td{border: 1px solid black; padding: 1px; font-size: 7px; height: auto;}
        .tablaf { border-collapse: collapse; width: 100%; font-size: 8px; text-align: center; margin-top:0px;}
        .tablaf tr, .tablaf td {padding: 0px}
        p {margin:5px; padding:0px;font-size: 10px}
        #titulo { top: 0px; width: 100%; text-align: center; margin-top: -30;}
        #titulo h2{padding:0px; margin:0px 0px 2px 0px; font-size: 13px; font-weight:normal;}
        #titulo h3{padding:0px; margin:0px; font-size: 12px; font-weight:normal;}
        #titulo table{position: fixed; top: 93px;}
        #para {position: relative; top: 0px; height:auto; width:60%; font-size: 8px; font-weight:bold; margin-top:20px;}
        header{ line-height: 1; font-size: 12px; top: 20px; font-weight: bold; left: 40px;}
        body { padding-top: 16%; }
    </style>
@endsection
@section('header')
@endsection
@section('content')
        <div id="titulo">
            <h2>Solicitud de Apertura de Curso</h2>
            <h3>Departamento de Vinculación</h3>
            <table width="100%">
                <tr>
                    <td style='text-align:right; font-size: 12px; padding-right: 50px;'>
                        @if(strpos($reg_unidad->unidad.$reg_unidad->cct, "07EIC0"))
                            Unidad de Capacitación
                        @else
                            Acción Móvil
                        @endif
                        {{ $reg_unidad->unidad}}<br/>
                        Memorándum No. {{$memo}} <br/>
                        {{$reg_unidad->municipio_acm}}, Chiapas; {{$date}}.<br/>
                    </td>
                </tr>
            </table>
        </div>
        <div id="para">
            PARA: {{$reg_unidad->academico}}, {{ $reg_unidad->pacademico }}. <br/>
            DE: {{$reg_unidad->vinculacion}}, {{ $reg_unidad->pvinculacion }}.<br/>
            CCP: {{ $reg_unidad->dunidad }}, {{ $reg_unidad->pdunidad }}. <br>
            {{ $reg_unidad->delegado_administrativo }},{{ $reg_unidad->pdelegado_administrativo }}.
        </div><br>
        <small>Archivo</small><br/>
        <p>&nbsp;&nbsp;&nbsp;Por medio del presente le solicito a Usted la siguiente apertura:</p>
        <table class="tb">
                <tr style="background: #EAECEE;">
                    <th>NÚMERO DE SOLICITUD</th>
                    <th>CURSO /<br/>CERTIFICACIÓN</th>
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
                        <td >@if ($item['mexoneracion'])  {{ substr($item['mexoneracion'],0,12)}}  {{ substr($item['mexoneracion'],12,strlen($item['mexoneracion']))}}   @else {{"N/A"}}  @endif</td>
                        <td>@if ($item['cgeneral']!='0') {{ substr($item['cgeneral'],0,5)}} {{ substr($item['cgeneral'],5,strlen($item['cgeneral']))}}@else {{"N/A"}} @endif</td>
                        <td>@if ($item['cespecifico']) {{$item['cespecifico']}} @else {{"N/A"}} @endif </td>
                        <td>{{$item['depen']}}</td>
                        <td>{{$item['depen_repre']}} / {{$item['tel_repre']}}</td>
                        <td>{{$item['instructor']}}</td>
                        <td>{{$item['vincu']}}</td>
                        <td width='auto'>{{$item['efisico']}}</td>
                        <td width='auto'>{{$item['observaciones']}}</td>
                    </tr>
                @endforeach
            </table>
            <div>
                <p>&nbsp;&nbsp;&nbsp;Sin más por el momento, le envío un cordial saludo.</p>
            </div>
            <br/>
            <br/>
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

@endsection
@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 530, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection
