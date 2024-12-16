@extends('theme.formatos.vlayout2025')
@section('title', 'ENTREGA FORMATO T PLANEACIÓN | SIVyC Icatech')
@section('content_script_css')
    <style>
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;
        /* Propiedad que ha sido agreda*/
        }
        .margin_top_ccp {
            margin-top: 7em;
        }
    </style>
@endsection
@section('content')
    {{-- SECCIÓN DE CONTENIDO --}}
    <div class="contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCIÓN TÉCNICA ACADÉMICA. </b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $num_memo_planeacion }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIÉRREZ, CHIAPAS; {{ $fecha_ahora_espaniol }}</b></div>
        <br><br>
        <div align=left style="font-size:12px;"><b>{{ $funcionarios['dplaneacion']['titulo'] }} {{ $funcionarios['dplaneacion']['nombre'] }}.</b></div>
        <div align=left style="font-size:11px;"><b>{{ $funcionarios['dplaneacion']['puesto'] }}.</b></div>
        <div align="left" style="font-size: 11px;"><b>Presente</b></div>
        <br><br><br><br>
        <div align="justify" style="font-size:16px;">
           <p>
            Por este medio y en seguimiento a la integración del Formato T de las Unidades de Capacitación
            de este Instituto correspondiente al mes de <b>{{$mesUnity}}</b> de la anualidad en curso; me es grato infórmarle
            que fue revisado y liberado el reporte estadístico denominado Formato T con la cantidad de <strong>{{$totalCursos}}</strong> cursos.
           </p>
        </div>
        <br>

        <br>
        <div align="justify" style="font-size:16px;">Sin más por el momento agradezco de su atención y le envío un cordial saludo.</div>
        {{-- <br> --}}

        <br>
        <br><br>
        <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>

        <div class="margin_top_ccp">
            <div style="font-size:11px;"> <b> {{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }} </b> </div>
            <div style="font-size:11px;"> <b> {{ $funcionarios['dacademico']['puesto'] }} </b> </div>
            <br><br><br>
            <div style="font-size:9px;"> <b>C.c.p  {{ $funcionarios['dgeneral']['titulo'] }} {{ $funcionarios['dgeneral']['nombre'] }}, {{ $funcionarios['dgeneral']['puesto'] }}. Para su conocimiento.</b> </div>
            <div style="font-size:9px"><b>C.c. {{ $funcionarios['progpres']['titulo'] }} {{ $funcionarios['progpres']['nombre'] }} - {{ $funcionarios['progpres']['puesto'] }} . Para su conocimiento.</b></div>
            <div style="font-size:9px;"> <b>Archivo.</b> </div>
            <div style="font-size:8px;"> <b>Validó: {{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }} - {{ $funcionarios['dacademico']['puesto'] }}</b></div>
            <div style="font-size:8px;"> <b>Elaboró: {{ $funcionarios['certificacion']['titulo'] }} {{ $funcionarios['certificacion']['nombre'] }} - {{ $funcionarios['certificacion']['puesto'] }} . </b></div>
            <br><br>
        </div>

    </div>
@endsection
@section('script_content_js')
@endsection
